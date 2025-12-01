<?php
header('Content-Type: application/json');
// session_start();

// Para debugging - mostrar errores (quitar en producción)
// error_reporting(E_ALL);
// ini_set('display_errors', 0);

try {
    require_once '../config/database.php';
} catch (Exception $e) {
    error_log("Error cargando database.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de configuración del servidor'
    ]);
    exit;
}

class PatientAuth {
    private $db;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->connect();
        } catch (Exception $e) {
            error_log("Error en constructor PatientAuth: " . $e->getMessage());
            throw new Exception("No se pudo conectar a la base de datos");
        }
    }
    
    public function login($cedula, $password) {
        try {
            // Buscar paciente en la base de datos
            $query = "SELECT * FROM mcmi_patients WHERE cedula = :cedula AND is_active = true";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $patient = $stmt->fetch();
                
                // Verificar la contraseña (asumiendo que se almacena como hash)
                // Para desarrollo, puedes usar una contraseña temporal
                if ($this->verifyPassword($password, $patient)) {
                    // Iniciar sesión de paciente
                    $_SESSION['patient_id'] = $patient['id'];
                    $_SESSION['patient_cedula'] = $patient['cedula'];
                    $_SESSION['patient_name'] = $patient['nombres'] . ' ' . $patient['apellidos'];
                    $_SESSION['patient_logged_in'] = true;
                    
                    return [
                        'success' => true,
                        'message' => 'Login exitoso',
                        'patient' => [
                            'id' => $patient['id'],
                            'cedula' => $patient['cedula'],
                            'name' => $patient['nombres'] . ' ' . $patient['apellidos']
                        ]
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Contraseña incorrecta'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Paciente no encontrado o inactivo'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en login paciente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor al procesar login'
            ];
        }
    }
    
    private function verifyPassword($password, $patient) {
        // Si existe password_hash en la base de datos, usar password_verify
        if (isset($patient['password_hash']) && !empty($patient['password_hash'])) {
            return password_verify($password, $patient['password_hash']);
        }
        
        // Para desarrollo: contraseña temporal (eliminar en producción)
        // Por ahora, aceptar cualquier contraseña para pacientes de prueba
        return true;
        
        // En producción, deberías tener un sistema de contraseñas seguro:
        // return $password === 'contraseña_temporal'; // NO USAR EN PRODUCCIÓN
    }
}

// Procesar la solicitud de login
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($input === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos JSON inválidos'
            ]);
            exit;
        }
        
        $cedula = trim($input['cedula'] ?? '');
        $password = $input['password'] ?? '';
        
        // Validar campos
        if (empty($cedula) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Cédula y contraseña son requeridos'
            ]);
            exit;
        }
        
        // Realizar login
        $patientAuth = new PatientAuth();
        $result = $patientAuth->login($cedula, $password);
        
        echo json_encode($result);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
} catch (Exception $e) {
    error_log("Error general en login-pacientes.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>