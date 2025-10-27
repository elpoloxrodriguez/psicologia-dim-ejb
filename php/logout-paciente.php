<?php
header('Content-Type: application/json');
session_start();

try {
    // Destruir todas las variables de sesión del paciente
    unset($_SESSION['patient_id']);
    unset($_SESSION['patient_cedula']);
    unset($_SESSION['patient_name']);
    unset($_SESSION['patient_logged_in']);

    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada correctamente'
    ]);
} catch (Exception $e) {
    error_log("Error en logout-paciente: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error cerrando sesión'
    ]);
}
?>