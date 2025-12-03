<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

class PatientManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // Obtener todos los pacientes
    public function getPatients($search = '', $filter = 'all') {
        try {
            $query = "SELECT p.*, 
                             u.username as created_by_name 
                      FROM mcmi_patients p 
                      LEFT JOIN mcmi_users u ON p.created_by = u.id 
                      WHERE 1=1";
            
            $params = [];
            
            if (!empty($search)) {
                $query .= " AND (p.cedula LIKE :search OR p.nombres LIKE :search OR p.apellidos LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($filter === 'active') {
                $query .= " AND p.is_active = true";
            } elseif ($filter === 'inactive') {
                $query .= " AND p.is_active = false";
            }
            
            $query .= " ORDER BY p.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $patients = $stmt->fetchAll();
            
            return [
                'success' => true,
                'patients' => $patients
            ];
            
        } catch (PDOException $e) {
            error_log("Error obteniendo pacientes: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener pacientes'
            ];
        }
    }
    
    // Obtener un paciente por ID
    public function getPatientById($id) {
        try {
            $query = "SELECT * FROM mcmi_patients WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                return [
                    'success' => true,
                    'patient' => $stmt->fetch()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Entrevistado no encontrado'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error obteniendo paciente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener paciente'
            ];
        }
    }
    
    // Crear nuevo paciente
    public function createPatient($data) {
        try {
            // Validar campos requeridos
            $required = ['cedula', 'nombres', 'apellidos', 'fecha_nacimiento', 'genero' , 'password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "El campo " . str_replace('_', ' ', $field) . " es requerido"
                    ];
                }
            }
            
            // Verificar si la cédula ya existe
            $checkQuery = "SELECT id FROM mcmi_patients WHERE cedula = :cedula";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':cedula', $data['cedula']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'La cédula ya está registrada'
                ];
            }

            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO mcmi_patients 
                      (cedula, nombres, apellidos, fecha_nacimiento, genero, email, telefono, 
                       direccion, ciudad, estado_civil, ocupacion, educacion, referencia, 
                       motivo_consulta, antecedentes, observaciones, password_hash, is_active, created_by) 
                      VALUES 
                      (:cedula, :nombres, :apellidos, :fecha_nacimiento, :genero, :email, :telefono, 
                       :direccion, :ciudad, :estado_civil, :ocupacion, :educacion, :referencia, 
                       :motivo_consulta, :antecedentes, :observaciones, :password_hash, :is_active, :created_by)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cedula', $data['cedula']);
            $stmt->bindParam(':nombres', $data['nombres']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
            $stmt->bindParam(':genero', $data['genero']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':direccion', $data['direccion']);
            $stmt->bindParam(':ciudad', $data['ciudad']);
            $stmt->bindParam(':estado_civil', $data['estado_civil']);
            $stmt->bindParam(':ocupacion', $data['ocupacion']);
            $stmt->bindParam(':educacion', $data['educacion']);
            $stmt->bindParam(':referencia', $data['referencia']);
            $stmt->bindParam(':motivo_consulta', $data['motivo_consulta']);
            $stmt->bindParam(':antecedentes', $data['antecedentes']);
            $stmt->bindParam(':observaciones', $data['observaciones']);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':is_active', $data['is_active']);
            $stmt->bindParam(':created_by', $_SESSION['user_id']);
            
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Entrevistado creado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear paciente'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error creando paciente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear paciente: ' . $e->getMessage()
            ];
        }
    }
    
    // Actualizar paciente
    public function updatePatient($id, $data) {
        try {
            // Validar campos requeridos
            $required = ['cedula', 'nombres', 'apellidos', 'fecha_nacimiento', 'genero'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "El campo " . str_replace('_', ' ', $field) . " es requerido"
                    ];
                }
            }
            
            // Verificar si la cédula ya existe (excluyendo el actual)
            $checkQuery = "SELECT id FROM mcmi_patients WHERE cedula = :cedula AND id != :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':cedula', $data['cedula']);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'La cédula ya está registrada en otro paciente'
                ];
            }
            
            $query = "UPDATE mcmi_patients 
                      SET cedula = :cedula, nombres = :nombres, apellidos = :apellidos, 
                          fecha_nacimiento = :fecha_nacimiento, genero = :genero, email = :email, 
                          telefono = :telefono, direccion = :direccion, ciudad = :ciudad, 
                          estado_civil = :estado_civil, ocupacion = :ocupacion, educacion = :educacion, 
                          referencia = :referencia, motivo_consulta = :motivo_consulta, 
                          antecedentes = :antecedentes, observaciones = :observaciones, 
                          is_active = :is_active, updated_at = CURRENT_TIMESTAMP 
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cedula', $data['cedula']);
            $stmt->bindParam(':nombres', $data['nombres']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
            $stmt->bindParam(':genero', $data['genero']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':direccion', $data['direccion']);
            $stmt->bindParam(':ciudad', $data['ciudad']);
            $stmt->bindParam(':estado_civil', $data['estado_civil']);
            $stmt->bindParam(':ocupacion', $data['ocupacion']);
            $stmt->bindParam(':educacion', $data['educacion']);
            $stmt->bindParam(':referencia', $data['referencia']);
            $stmt->bindParam(':motivo_consulta', $data['motivo_consulta']);
            $stmt->bindParam(':antecedentes', $data['antecedentes']);
            $stmt->bindParam(':observaciones', $data['observaciones']);
            $stmt->bindParam(':is_active', $data['is_active']);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Entrevistado actualizado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar paciente'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error actualizando paciente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al actualizar paciente: ' . $e->getMessage()
            ];
        }
    }
    
    // Eliminar paciente
    public function deletePatient($id) {
        try {
            $query = "DELETE FROM mcmi_patients WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Entrevistado eliminado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar paciente'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error eliminando paciente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al eliminar paciente'
            ];
        }
    }
}

// Procesar las solicitudes
$patientManager = new PatientManager();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? 'all';
    
    if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
        // Obtener un paciente específico
        $result = $patientManager->getPatientById($_GET['id']);
        echo json_encode($result);
    } else {
        // Obtener todos los pacientes con filtros
        $result = $patientManager->getPatients($search, $filter);
        echo json_encode($result);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'create':
                $result = $patientManager->createPatient($input);
                echo json_encode($result);
                break;
                
            case 'update':
                if (isset($input['id'])) {
                    $result = $patientManager->updatePatient($input['id'], $input);
                    echo json_encode($result);
                }
                break;
                
            case 'delete':
                if (isset($input['id'])) {
                    $result = $patientManager->deletePatient($input['id']);
                    echo json_encode($result);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>