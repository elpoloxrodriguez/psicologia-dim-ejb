        // Cargar reportes al iniciar
        document.addEventListener('DOMContentLoaded', loadReports);
        
        async function loadReports() {
            const filters = {
                patient_cedula: document.getElementById('filter-cedula').value,
                patient_name: document.getElementById('filter-name').value,
                date_from: document.getElementById('filter-date-from').value,
                date_to: document.getElementById('filter-date-to').value
            };

            
            try {
                const response = await fetch('./php/get-report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_all_reports',
                        filters: filters
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    displayReports(data.reports);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Error de conexión');
            }
        }
        
        function displayReports(reports) {
            const container = document.getElementById('reports-container');
            
            if (reports.length === 0) {
                container.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <h3>No se encontraron evaluaciones</h3>
                        <p>No hay resultados que coincidan con los criterios de búsqueda.</p>
                    </div>
                `;
                return;
            }
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Cédula</th>
                            <th>Evaluador</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            reports.forEach(report => {
                const date = new Date(report.evaluation_date).toLocaleDateString('es-ES');
                const statusBadge = report.status === 'completed' ? 
                    '<span style="color: #28a745;"><i class="fas fa-check-circle"></i> Completado</span>' :
                    '<span style="color: #ffc107;"><i class="fas fa-clock"></i> En Progreso</span>';
                
                html += `
                    <tr>
                        <td>${date}</td>
                        <td>${report.nombres} ${report.apellidos}</td>
                        <td>${report.cedula}</td>
                        <td>${report.evaluator_name || 'Sistema'}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="./views/reportes.php?id=${report.id}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = html;
        }
        
        function clearFilters() {
            document.getElementById('filter-cedula').value = '';
            document.getElementById('filter-name').value = '';
            document.getElementById('filter-date-from').value = '';
            document.getElementById('filter-date-to').value = '';
            loadReports();
        }
        
        function showError(message) {
            const container = document.getElementById('reports-container');
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error al cargar el historial</h3>
                    <p>${message}</p>
                    <button onclick="loadReports()" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }