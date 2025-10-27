<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

class UserManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // Obtener todos los usuarios
    public function getUsers() {
        try {
            $query = "SELECT id, username, email, full_name, role, is_active, 
                             created_at, last_login, updated_at 
                      FROM mcmi_users 
                      ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $users = $stmt->fetchAll();
            
            return [
                'success' => true,
                'users' => $users
            ];
            
        } catch (PDOException $e) {
            error_log("Error obteniendo usuarios: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener usuarios'
            ];
        }
    }
    
    // Obtener un usuario por ID
    public function getUserById($id) {
        try {
            $query = "SELECT id, username, email, full_name, role, is_active 
                      FROM mcmi_users 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                return [
                    'success' => true,
                    'user' => $stmt->fetch()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error obteniendo usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener usuario'
            ];
        }
    }
    
    // Crear nuevo usuario
    public function createUser($data) {
        try {
            // Validar campos requeridos
            if (empty($data['username']) || empty($data['email']) || empty($data['full_name']) || empty($data['role'])) {
                return [
                    'success' => false,
                    'message' => 'Todos los campos marcados con * son requeridos'
                ];
            }
            
            // Verificar si el usuario ya existe
            $checkQuery = "SELECT id FROM mcmi_users WHERE username = :username OR email = :email";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':username', $data['username']);
            $checkStmt->bindParam(':email', $data['email']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'El usuario o email ya existe'
                ];
            }
            
            // Hash de la contraseña
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO mcmi_users (username, email, password_hash, full_name, role, is_active) 
                      VALUES (:username, :email, :password_hash, :full_name, :role, :is_active)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':is_active', $data['is_active']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Usuario creado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear usuario'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear usuario'
            ];
        }
    }
    
    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            // Validar campos requeridos
            if (empty($data['username']) || empty($data['email']) || empty($data['full_name']) || empty($data['role'])) {
                return [
                    'success' => false,
                    'message' => 'Todos los campos marcados con * son requeridos'
                ];
            }
            
            // Verificar si el usuario o email ya existen (excluyendo el actual)
            $checkQuery = "SELECT id FROM mcmi_users WHERE (username = :username OR email = :email) AND id != :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':username', $data['username']);
            $checkStmt->bindParam(':email', $data['email']);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'El usuario o email ya existe'
                ];
            }
            
            // Construir query dinámicamente
            if (!empty($data['password'])) {
                $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
                $query = "UPDATE mcmi_users 
                          SET username = :username, email = :email, password_hash = :password_hash, 
                              full_name = :full_name, role = :role, is_active = :is_active,
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id";
            } else {
                $query = "UPDATE mcmi_users 
                          SET username = :username, email = :email, 
                              full_name = :full_name, role = :role, is_active = :is_active,
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':is_active', $data['is_active']);
            $stmt->bindParam(':id', $id);
            
            if (!empty($data['password'])) {
                $stmt->bindParam(':password_hash', $password_hash);
            }
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar usuario'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error actualizando usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario'
            ];
        }
    }
    
    // Eliminar usuario
    public function deleteUser($id) {
        try {
            // No permitir eliminar el propio usuario
            if ($id == $_SESSION['user_id']) {
                return [
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ];
            }
            
            $query = "DELETE FROM mcmi_users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar usuario'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error eliminando usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario'
            ];
        }
    }
}

// Procesar las solicitudes
$userManager = new UserManager();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
        // Obtener un usuario específico
        $result = $userManager->getUserById($_GET['id']);
        echo json_encode($result);
    } else {
        // Obtener todos los usuarios
        $result = $userManager->getUsers();
        echo json_encode($result);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'create':
                $result = $userManager->createUser($input);
                echo json_encode($result);
                break;
                
            case 'update':
                if (isset($input['id'])) {
                    $result = $userManager->updateUser($input['id'], $input);
                    echo json_encode($result);
                }
                break;
                
            case 'delete':
                if (isset($input['id'])) {
                    $result = $userManager->deleteUser($input['id']);
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