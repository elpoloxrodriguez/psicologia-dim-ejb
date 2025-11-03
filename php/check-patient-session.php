<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

function checkPatientSession() {
    try {
        if (isset($_SESSION['patient_logged_in']) && $_SESSION['patient_logged_in'] === true) {
            
            // Verificar también si ya completó la evaluación
            $evaluationCompleted = false;
            $completionDate = null;
            
            if (isset($_SESSION['patient_id'])) {
                $database = new Database();
                $db = $database->connect();
                
                $query = "SELECT completion_date FROM mcmi_results 
                         WHERE patient_id = :patient_id 
                         ORDER BY created_at DESC LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':patient_id', $_SESSION['patient_id']);
                $stmt->execute();
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $evaluationCompleted = true;
                    $completionDate = $result['completion_date'];
                }
            }
            
            return [
                'success' => true,
                'logged_in' => true,
                'evaluation_completed' => $evaluationCompleted,
                'completion_date' => $completionDate,
                'patient' => [
                    'id' => $_SESSION['patient_id'] ?? null,
                    'cedula' => $_SESSION['patient_cedula'] ?? null,
                    'name' => $_SESSION['patient_name'] ?? null
                ]
            ];
        } else {
            return [
                'success' => true,
                'logged_in' => false,
                'evaluation_completed' => false
            ];
        }
    } catch (Exception $e) {
        error_log("Error en check-patient-session: " . $e->getMessage());
        return [
            'success' => false,
            'logged_in' => false,
            'evaluation_completed' => false,
            'message' => 'Error verificando sesión'
        ];
    }
}

echo json_encode(checkPatientSession());
?>