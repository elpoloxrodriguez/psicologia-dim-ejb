<?php
header('Content-Type: application/json');
session_start();

// Incluir la configuración de la base de datos
require_once __DIR__ . '/../config/database.php';

class ReportManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getReport($resultId) {
        try {
            error_log("Buscando reporte con ID: " . $resultId);
            
            // Obtener el resultado con información del paciente
            $query = "SELECT r.*, p.nombres, p.apellidos, p.cedula, p.fecha_nacimiento, p.genero,
                             u.username as evaluator_name
                      FROM mcmi_results r 
                      JOIN mcmi_patients p ON r.patient_id = p.id 
                      LEFT JOIN mcmi_users u ON r.evaluator_id = u.id 
                      WHERE r.id = :result_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':result_id', $resultId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Log para debugging
                error_log("Reporte encontrado para paciente: " . $result['nombres'] . " " . $result['apellidos']);
                
                return [
                    'success' => true,
                    'result' => $result,
                    'patient' => [
                        'nombres' => $result['nombres'],
                        'apellidos' => $result['apellidos'],
                        'cedula' => $result['cedula'],
                        'fecha_nacimiento' => $result['fecha_nacimiento'],
                        'genero' => $result['genero']
                    ]
                ];
            } else {
                error_log("No se encontró resultado con ID: " . $resultId);
                return [
                    'success' => false,
                    'message' => 'Resultado no encontrado'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error obteniendo reporte: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor al obtener el reporte: ' . $e->getMessage()
            ];
        }
    }
    
    public function getAllReports($filters = []) {
        try {
            $query = "SELECT r.id, r.evaluation_date, r.status,
                             p.cedula, p.nombres, p.apellidos,
                             u.username as evaluator_name
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
                $query .= " AND DATE(r.evaluation_date) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $query .= " AND DATE(r.evaluation_date) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $query .= " ORDER BY r.evaluation_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Encontrados " . count($reports) . " reportes");
            
            return [
                'success' => true,
                'reports' => $reports
            ];
            
        } catch (PDOException $e) {
            error_log("Error obteniendo reportes: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener reportes: ' . $e->getMessage()
            ];
        }
    }
}

// Manejar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Procesar solicitudes
$reportManager = new ReportManager();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Si no se puede decodificar JSON, intentar con POST normal
    if ($input === null && !empty($_POST)) {
        $input = $_POST;
    }
    
    error_log("Solicitud recibida: " . print_r($input, true));
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'get_report':
                if (isset($input['result_id'])) {
                    $result = $reportManager->getReport($input['result_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID de resultado no especificado']);
                }
                break;
                
            case 'get_all_reports':
                $filters = $input['filters'] ?? [];
                $result = $reportManager->getAllReports($filters);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida: ' . $input['action']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    }
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
?>