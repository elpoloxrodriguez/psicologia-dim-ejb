<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

function checkEvaluationStatus() {
    try {
        if (!isset($_SESSION['patient_logged_in']) || $_SESSION['patient_logged_in'] !== true) {
            return [
                'completed' => false,
                'error' => 'No hay sesión de paciente activa'
            ];
        }
        
        $patientId = $_SESSION['patient_id'] ?? null;
        
        if (!$patientId) {
            return [
                'completed' => false,
                'error' => 'ID de paciente no disponible'
            ];
        }
        
        $database = new Database();
        $db = $database->connect();
        
        $query = "SELECT id, completion_date, created_at 
                  FROM mcmi_results 
                  WHERE patient_id = :patient_id 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'completed' => true,
                'completion_date' => $result['completion_date'] ?? $result['created_at'],
                'result_id' => $result['id']
            ];
        } else {
            return [
                'completed' => false
            ];
        }
        
    } catch (Exception $e) {
        return [
            'completed' => false,
            'error' => 'Error del servidor'
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = checkEvaluationStatus();
    echo json_encode($result);
} else {
    echo json_encode([
        'completed' => false,
        'error' => 'Método no permitido'
    ]);
}
?>