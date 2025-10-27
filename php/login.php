<?php
header('Content-Type: application/json');
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'success' => true,
        'message' => 'Ya tiene una sesión activa',
        'redirect' => 'dashboard.html'
    ]);
    exit;
}

// Para debugging durante desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 0); // Mejor 0 en producción, 1 en desarrollo
ini_set('log_errors', 1);

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

class Auth {
    private $db;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->connect();
        } catch (Exception $e) {
            error_log("Error en constructor Auth: " . $e->getMessage());
            throw new Exception("No se pudo conectar a la base de datos");
        }
    }
    
    public function login($username, $password) {
        try {
            // Buscar usuario en la base de datos
            $query = "SELECT * FROM mcmi_users WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                
                // Verificar la contraseña
                if (password_verify($password, $user['password_hash'])) {
                    // Iniciar sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    
                    return [
                        'success' => true,
                        'message' => 'Login exitoso',
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'role' => $user['role']
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
                    'message' => 'Usuario no encontrado'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor al procesar login'
            ];
        }
    }
}

// Procesar la solicitud de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($input === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos JSON inválidos'
            ]);
            exit;
        }
        
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        
        // Validar campos
        if (empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario y contraseña son requeridos'
            ]);
            exit;
        }
        
        // Realizar login
        $auth = new Auth();
        $result = $auth->login($username, $password);
        
        echo json_encode($result);
        
    } catch (Exception $e) {
        error_log("Error general en login.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>