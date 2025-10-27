<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

class ResultsQuery {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAllResults($filters = []) {
        try {
            $query = "SELECT 
                r.id, r.evaluation_date, r.status,
                p.cedula, p.nombres, p.apellidos,
                u.username as evaluator_name,
                -- Escalas más elevadas
                GREATEST(
                    r.br_1, r.br_2A, r.br_2B, r.br_3, r.br_4, r.br_5,
                    r.br_6A, r.br_6B, r.br_7, r.br_8A, r.br_8B,
                    r.br_S, r.br_C, r.br_P
                ) as max_br
                FROM mcmi_results r
                JOIN mcmi_patients p ON r.patient_id = p.id
                LEFT JOIN mcmi_users u ON r.evaluator_id = u.id
                WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (!empty($filters['patient_cedula'])) {
                $query .= " AND p.cedula LIKE :cedula";
                $params[':cedula'] = "%{$filters['patient_cedula']}%";
            }
            
            if (!empty($filters['patient_name'])) {
                $query .= " AND (p.nombres LIKE :name OR p.apellidos LIKE :name)";
                $params[':name'] = "%{$filters['patient_name']}%";
            }
            
            if (!empty($filters['date_from'])) {
                $query .= " AND r.evaluation_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $query .= " AND r.evaluation_date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            if (!empty($filters['min_br'])) {
                $query .= " AND GREATEST(
                    r.br_1, r.br_2A, r.br_2B, r.br_3, r.br_4, r.br_5,
                    r.br_6A, r.br_6B, r.br_7, r.br_8A, r.br_8B,
                    r.br_S, r.br_C, r.br_P
                ) >= :min_br";
                $params[':min_br'] = $filters['min_br'];
            }
            
            $query .= " ORDER BY r.evaluation_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'results' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            error_log("Error consultando resultados: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al consultar resultados'
            ];
        }
    }
    
    public function getResultsStatistics() {
        try {
            $query = "SELECT 
                COUNT(*) as total_evaluations,
                COUNT(DISTINCT patient_id) as unique_patients,
                AVG(GREATEST(
                    br_1, br_2A, br_2B, br_3, br_4, br_5,
                    br_6A, br_6B, br_7, br_8A, br_8B,
                    br_S, br_C, br_P
                )) as avg_max_br,
                MIN(evaluation_date) as first_evaluation,
                MAX(evaluation_date) as last_evaluation
                FROM mcmi_results";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return [
                'success' => true,
                'statistics' => $stmt->fetch(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ];
        }
    }
}

// Procesar solicitudes
$resultsQuery = new ResultsQuery();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'get_all':
                $filters = $input['filters'] ?? [];
                $result = $resultsQuery->getAllResults($filters);
                echo json_encode($result);
                break;
                
            case 'get_statistics':
                $result = $resultsQuery->getResultsStatistics();
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>