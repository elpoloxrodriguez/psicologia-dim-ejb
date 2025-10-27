// Función para guardar resultados en la base de datos
async function saveResultsToDatabase(results, responses, interpretation = '') {
    try {
        // Obtener ID del paciente desde la sesión
        const patientId = await getPatientIdFromSession();
        
        if (!patientId) {
            console.error('No se pudo obtener el ID del paciente');
            return false;
        }
        
        const data = {
            action: 'save',
            patient_id: patientId,
            results: results,
            responses: responses,
            interpretation: interpretation
        };
        
        const response = await fetch('php/save-results.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            console.log('Resultados guardados exitosamente. ID:', result.result_id);
            return result.result_id;
        } else {
            console.error('Error guardando resultados:', result.message);
            return false;
        }
        
    } catch (error) {
        console.error('Error guardando resultados:', error);
        return false;
    }
}

// Función para obtener el ID del paciente desde la sesión
async function getPatientIdFromSession() {
    try {
        const response = await fetch('php/check-patient-session.php');
        const data = await response.json();
        
        if (data.logged_in && data.patient) {
            return data.patient.id;
        }
        return null;
    } catch (error) {
        console.error('Error obteniendo sesión del paciente:', error);
        return null;
    }
}

// Función para obtener resultados anteriores del paciente
async function getPatientPreviousResults() {
    try {
        const patientId = await getPatientIdFromSession();
        
        if (!patientId) {
            return [];
        }
        
        const response = await fetch('php/save-results.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_patient_results',
                patient_id: patientId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.results;
        } else {
            console.error('Error obteniendo resultados anteriores:', data.message);
            return [];
        }
        
    } catch (error) {
        console.error('Error obteniendo resultados anteriores:', error);
        return [];
    }
}

// Función para mostrar historial de evaluaciones
async function showEvaluationHistory() {
    const previousResults = await getPatientPreviousResults();
    
    if (previousResults.length === 0) {
        return '<p>No hay evaluaciones anteriores.</p>';
    }
    
    let html = `
        <div class="evaluation-history">
            <h3>Historial de Evaluaciones</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Escalas Elevadas</th>
                        <th>BR Máximo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    previousResults.forEach(result => {
        const evaluationDate = new Date(result.evaluation_date).toLocaleDateString();
        const elevatedScales = getElevatedScalesFromResult(result);
        const maxBR = getMaxBRFromResult(result);
        
        html += `
            <tr>
                <td>${evaluationDate}</td>
                <td>${elevatedScales.join(', ')}</td>
                <td>${maxBR}</td>
                <td>
                    <button onclick="viewResultDetail(${result.id})" class="btn-view">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    <button onclick="downloadResultPDF(${result.id})" class="btn-download">
                        <i class="fas fa-download"></i> PDF
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    return html;
}

// Función auxiliar para obtener escalas elevadas desde un resultado
function getElevatedScalesFromResult(result) {
    const elevatedScales = [];
    const scalePrefixes = ['1', '2A', '2B', '3', '4', '5', '6A', '6B', '7', '8A', '8B', 'S', 'C', 'P'];
    
    scalePrefixes.forEach(scale => {
        const brValue = result[`br_${scale}`];
        if (brValue >= 75) {
            elevatedScales.push(scale);
        }
    });
    
    return elevatedScales.slice(0, 3); // Mostrar solo las 3 principales
}

// Función auxiliar para obtener el BR máximo desde un resultado
function getMaxBRFromResult(result) {
    let maxBR = 0;
    const scalePrefixes = ['1', '2A', '2B', '3', '4', '5', '6A', '6B', '7', '8A', '8B', 'S', 'C', 'P'];
    
    scalePrefixes.forEach(scale => {
        const brValue = result[`br_${scale}`];
        if (brValue > maxBR) {
            maxBR = brValue;
        }
    });
    
    return maxBR;
}