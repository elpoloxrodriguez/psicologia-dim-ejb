            <?php
            header('Content-Type: application/json');
            session_start();

            require_once '../config/database.php';

            class ResultsManager {
                private $db;
                
                public function __construct() {
                    $database = new Database();
                    $this->db = $database->connect();
                }
                
                public function saveResults($data) {
                    try {
                        error_log("Datos recibidos para guardar: " . print_r($data, true));
                        
                        // Validar datos requeridos
                        if (empty($data['patient_id'])) {
                            error_log("Error: patient_id está vacío");
                            return [
                                'success' => false,
                                'message' => 'ID de paciente requerido'
                            ];
                        }
                        
                        if (empty($data['results'])) {
                            error_log("Error: results está vacío");
                            return [
                                'success' => false,
                                'message' => 'Datos de resultados requeridos'
                            ];
                        }
                        
                        // Preparar datos para la inserción
                        $patientId = $data['patient_id'];
                        $results = $data['results'];
                        $responses = $data['responses'] ?? [];
                        $interpretation = $data['interpretation'] ?? '';
                        
                        // Determinar el evaluador
                        $evaluatorId = $_SESSION['user_id'] ?? $_SESSION['patient_id'] ?? null;
                        error_log("Evaluador ID: " . $evaluatorId);
                        
                        // Construir la consulta SQL
                        $query = "INSERT INTO mcmi_results (
                            patient_id, evaluator_id, completion_date, status,
                            raw_1, raw_2A, raw_2B, raw_3, raw_4, raw_5,
                            raw_6A, raw_6B, raw_7, raw_8A, raw_8B,
                            raw_S, raw_C, raw_P,
                            raw_A, raw_H, raw_N, raw_D, raw_B, raw_T, raw_R,
                            raw_SS, raw_CC, raw_PP,
                            raw_X, raw_Y, raw_Z, raw_V,
                            br_1, br_2A, br_2B, br_3, br_4, br_5,
                            br_6A, br_6B, br_7, br_8A, br_8B,
                            br_S, br_C, br_P,
                            br_A, br_H, br_N, br_D, br_B, br_T, br_R,
                            br_SS, br_CC, br_PP,
                            br_X, br_Y, br_Z, br_V,
                            responses, primary_interpretation
                        ) VALUES (
                            :patient_id, :evaluator_id, CURRENT_TIMESTAMP, 'completed',
                            :raw_1, :raw_2A, :raw_2B, :raw_3, :raw_4, :raw_5,
                            :raw_6A, :raw_6B, :raw_7, :raw_8A, :raw_8B,
                            :raw_S, :raw_C, :raw_P,
                            :raw_A, :raw_H, :raw_N, :raw_D, :raw_B, :raw_T, :raw_R,
                            :raw_SS, :raw_CC, :raw_PP,
                            :raw_X, :raw_Y, :raw_Z, :raw_V,
                            :br_1, :br_2A, :br_2B, :br_3, :br_4, :br_5,
                            :br_6A, :br_6B, :br_7, :br_8A, :br_8B,
                            :br_S, :br_C, :br_P,
                            :br_A, :br_H, :br_N, :br_D, :br_B, :br_T, :br_R,
                            :br_SS, :br_CC, :br_PP,
                            :br_X, :br_Y, :br_Z, :br_V,
                            :responses, :primary_interpretation
                        ) RETURNING id";
                        
                        error_log("Query preparado: " . $query);
                        
                        $stmt = $this->db->prepare($query);
                        
                        // Bind parameters
                        $stmt->bindParam(':patient_id', $patientId);
                        $stmt->bindParam(':evaluator_id', $evaluatorId);
                        
                        // Puntuaciones brutas y BR
                        $this->bindResults($stmt, $results, 'raw');
                        $this->bindResults($stmt, $results, 'br');
                        
                        // Respuestas e interpretación
                        $responsesJson = json_encode($responses);
                        $stmt->bindParam(':responses', $responsesJson);
                        $stmt->bindParam(':primary_interpretation', $interpretation);
                        
                        if ($stmt->execute()) {
                            $resultId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                            error_log("Resultado guardado exitosamente con ID: " . $resultId);
                            return [
                                'success' => true,
                                'message' => 'Resultados guardados exitosamente',
                                'result_id' => $resultId
                            ];
                        } else {
                            $errorInfo = $stmt->errorInfo();
                            error_log("Error en execute: " . print_r($errorInfo, true));
                            return [
                                'success' => false,
                                'message' => 'Error al ejecutar la consulta: ' . $errorInfo[2]
                            ];
                        }
                        
                    } catch (PDOException $e) {
                        error_log("Error PDO guardando resultados: " . $e->getMessage());
                        return [
                            'success' => false,
                            'message' => 'Error del servidor al guardar resultados: ' . $e->getMessage()
                        ];
                    }
                }
                
                private function bindResults($stmt, $results, $type) {
                    $scales = [
                        '1', '2A', '2B', '3', '4', '5', '6A', '6B', '7', '8A', '8B',
                        'S', 'C', 'P', 'A', 'H', 'N', 'D', 'B', 'T', 'R',
                        'SS', 'CC', 'PP', 'X', 'Y', 'Z', 'V'
                    ];
                    
                    foreach ($scales as $scale) {
                        $paramName = ':' . $type . '_' . $scale;
                        $value = $results[$scale][$type . 'Score'] ?? 0;
                        error_log("Bind: $paramName = $value");
                        $stmt->bindParam($paramName, $value, PDO::PARAM_INT);
                    }
                }
            }

            class EvaluationStatus {
                private $db;
        
                public function __construct() {
                    $database = new Database();
                    $this->db = $database->connect();
                }
        
                public function checkStatus($patientId) {
                    try {
                        $query = "SELECT id, completion_date, status 
                                FROM mcmi_results 
                                WHERE patient_id = :patient_id 
                                AND status = 'completed'
                                ORDER BY completion_date DESC 
                                LIMIT 1";
                        
                        $stmt = $this->db->prepare($query);
                        $stmt->bindParam(':patient_id', $patientId);
                        $stmt->execute();
                        
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result) {
                            return [
                                'completed' => true,
                                'completion_date' => $result['completion_date'],
                                'result_id' => $result['id']
                            ];
                        } else {
                            return [
                                'completed' => false
                            ];
                        }
                        
                    } catch (PDOException $e) {
                        error_log("Error checking evaluation status: " . $e->getMessage());
                        return [
                            'completed' => false,
                            'error' => 'Error verificando estado de evaluación'
                        ];
                    }
                }
            }

            // Procesar solicitudes
            $resultsManager = new ResultsManager();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                error_log("Input recibido: " . print_r($input, true));
                
                if (isset($input['action'])) {
                    switch ($input['action']) {
                        case 'save':
                            $result = $resultsManager->saveResults($input);
                            echo json_encode($result);
                            break;
                        case 'get_evaluation_status':
                if (empty($input['patient_id'])) {
                    echo json_encode(['success' => false, 'message' => 'ID de paciente requerido']);
                    break;
                }
                
                $evaluationStatus = new EvaluationStatus();
                $status = $evaluationStatus->checkStatus($input['patient_id']);
                echo json_encode($status);
                break;
                            
                        default:
                            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            }
            ?>