<?php
header('Content-Type: application/json');
session_start();

function checkPatientSession() {
    try {
        if (isset($_SESSION['patient_logged_in']) && $_SESSION['patient_logged_in'] === true) {
            return [
                'success' => true,
                'logged_in' => true,
                'patient' => [
                    'id' => $_SESSION['patient_id'] ?? null,
                    'cedula' => $_SESSION['patient_cedula'] ?? null,
                    'name' => $_SESSION['patient_name'] ?? null
                ]
            ];
        } else {
            return [
                'success' => true,
                'logged_in' => false
            ];
        }
    } catch (Exception $e) {
        error_log("Error en check-patient-session: " . $e->getMessage());
        return [
            'success' => false,
            'logged_in' => false,
            'message' => 'Error verificando sesión'
        ];
    }
}

echo json_encode(checkPatientSession());
?>