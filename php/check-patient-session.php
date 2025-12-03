<?php
header('Content-Type: application/json');
session_start();

// Habilitar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar si la sesión existe y tiene los datos necesarios
    $response = [
        'success' => false,
        'logged_in' => false,
        'evaluation_completed' => false,
        'session_data' => $_SESSION // Para debugging
    ];

    // Verificar si el paciente está logueado
    if (isset($_SESSION['patient_logged_in']) && $_SESSION['patient_logged_in'] === true) {
        $response['logged_in'] = true;
        $response['success'] = true;
        
        // Agregar datos del paciente si existen
        if (isset($_SESSION['patient_id'])) {
            $response['patient'] = [
                'id' => $_SESSION['patient_id'],
                'cedula' => $_SESSION['patient_cedula'] ?? null,
                'name' => $_SESSION['patient_name'] ?? null
            ];
            
            // Verificar si ya completó la evaluación
            try {
                require_once '../config/database.php';
                $database = new Database();
                $db = $database->connect();
                
                $query = "SELECT completion_date, status FROM mcmi_results 
                         WHERE patient_id = :patient_id 
                         AND status = 'completed'
                         ORDER BY completion_date DESC LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':patient_id', $_SESSION['patient_id'], PDO::PARAM_INT);
                $stmt->execute();
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $response['evaluation_completed'] = true;
                    $response['completion_date'] = $result['completion_date'];
                    $response['status'] = $result['status'];
                }
                
            } catch (PDOException $e) {
                error_log("Error DB en check-patient-session: " . $e->getMessage());
                $response['db_error'] = $e->getMessage();
            }
        }
    } else {
        $response['success'] = true; // Éxito en la verificación, pero no logueado
        $response['message'] = 'Entrevistado no logueado';
    }

    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error general en check-patient-session: " . $e->getMessage());
    
    // Respuesta de error
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'evaluation_completed' => false,
        'error' => 'Error interno del servidor',
        'debug_info' => $e->getMessage()
    ]);
}
?>