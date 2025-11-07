<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database-militar.php';

class CedulaVerifier {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // Verificar si la cédula existe en la base de datos militar
    public function verifyCedula($cedula) {
        try {
            $query = "SELECT * FROM ejercito.pmiperbasd WHERE ccedula = :cedula";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Cédula encontrada en sistema militar'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Cédula no encontrada en sistema militar'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error verificando cédula: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al verificar cédula en sistema militar'
            ];
        }
    }
}

// Procesar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['cedula'])) {
        $verifier = new CedulaVerifier();
        $result = $verifier->verifyCedula($input['cedula']);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cédula no proporcionada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>