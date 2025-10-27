// Variables globales
let patients = [];
let currentEditId = null;
let currentSearch = '';
let currentFilter = 'all';


// Mostrar mensaje
function showMessage(message, type = 'success') {
    const messageContainer = document.getElementById('messageContainer');
    if (messageContainer) {
        messageContainer.innerHTML = `
            <div class="${type === 'success' ? 'success-message' : 'error-message'}">
                ${message}
            </div>
        `;
        
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }
}

// Calcular edad desde fecha de nacimiento
function calculateAge(fechaNacimiento) {
    const birthDate = new Date(fechaNacimiento);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// Cargar pacientes
async function loadPatients() {
    try {
        const url = `php/patients.php?search=${encodeURIComponent(currentSearch)}&filter=${currentFilter}`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            patients = data.patients;
            renderPatientsTable();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error cargando pacientes:', error);
        showMessage('Error de conexión al cargar pacientes', 'error');
    }
}

// Renderizar tabla de pacientes
function renderPatientsTable() {
    const tableBody = document.getElementById('patientsTableBody');
    const loading = document.getElementById('loading');
    const table = document.getElementById('patientsTable');
    
    if (patients.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem;">
                    No hay pacientes registrados
                </td>
            </tr>
        `;
    } else {
        tableBody.innerHTML = patients.map(patient => {
            const age = calculateAge(patient.fecha_nacimiento);
            return `
            <tr>
                <td>${patient.cedula}</td>
                <td>${patient.nombres}</td>
                <td>${patient.apellidos}</td>
                <td>${age} años</td>
                <td>${patient.genero.charAt(0).toUpperCase() + patient.genero.slice(1)}</td>
                <td>${patient.telefono || 'N/A'}</td>
                <td class="${patient.is_active ? 'status-active' : 'status-inactive'}">
                    ${patient.is_active ? 'Activo' : 'Inactivo'}
                </td>
                <td>${new Date(patient.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-view" onclick="viewPatient(${patient.id})" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-edit" onclick="editPatient(${patient.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" onclick="deletePatient(${patient.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `}).join('');
    }
    
    loading.style.display = 'none';
    table.style.display = 'table';
}

// Buscar pacientes
function searchPatients() {
    currentSearch = document.getElementById('searchInput').value;
    loadPatients();
}

// Filtrar pacientes
function filterPatients() {
    currentFilter = document.getElementById('statusFilter').value;
    loadPatients();
}

// Abrir modal para nuevo paciente
function openPatientModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Nuevo Paciente';
    document.getElementById('patientForm').reset();
    document.getElementById('patientModal').style.display = 'block';
}

// Cerrar modal
function closePatientModal() {
    document.getElementById('patientModal').style.display = 'none';
    currentEditId = null;
}

// Editar paciente
async function editPatient(id) {
    try {
        const response = await fetch(`php/patients.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const patient = data.patient;
            currentEditId = id;
            
            document.getElementById('modalTitle').textContent = 'Editar Paciente';
            document.getElementById('patientId').value = patient.id;
            document.getElementById('cedula').value = patient.cedula;
            document.getElementById('nombres').value = patient.nombres;
            document.getElementById('apellidos').value = patient.apellidos;
            document.getElementById('fecha_nacimiento').value = patient.fecha_nacimiento;
            document.getElementById('genero').value = patient.genero;
            document.getElementById('email').value = patient.email || '';
            document.getElementById('telefono').value = patient.telefono || '';
            document.getElementById('direccion').value = patient.direccion || '';
            document.getElementById('ciudad').value = patient.ciudad || '';
            document.getElementById('estado_civil').value = patient.estado_civil || '';
            document.getElementById('ocupacion').value = patient.ocupacion || '';
            document.getElementById('educacion').value = patient.educacion || '';
            document.getElementById('referencia').value = patient.referencia || '';
            document.getElementById('motivo_consulta').value = patient.motivo_consulta || '';
            document.getElementById('antecedentes').value = patient.antecedentes || '';
            document.getElementById('observaciones').value = patient.observaciones || '';
            document.getElementById('is_active').value = patient.is_active.toString();
            
            document.getElementById('patientModal').style.display = 'block';
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error cargando paciente:', error);
        showMessage('Error al cargar paciente', 'error');
    }
}

// Ver paciente (podría expandirse para mostrar más detalles)
function viewPatient(id) {
    // Por ahora, redirige a editar. Puedes crear una vista detallada después.
    editPatient(id);
}

// Eliminar paciente
async function deletePatient(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este paciente? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch('php/patients.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: id
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message, 'success');
            loadPatients();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error eliminando paciente:', error);
        showMessage('Error de conexión al eliminar paciente', 'error');
    }
}

// Manejar envío del formulario
document.getElementById('patientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        cedula: formData.get('cedula'),
        nombres: formData.get('nombres'),
        apellidos: formData.get('apellidos'),
        fecha_nacimiento: formData.get('fecha_nacimiento'),
        genero: formData.get('genero'),
        email: formData.get('email'),
        telefono: formData.get('telefono'),
        direccion: formData.get('direccion'),
        ciudad: formData.get('ciudad'),
        estado_civil: formData.get('estado_civil'),
        ocupacion: formData.get('ocupacion'),
        educacion: formData.get('educacion'),
        referencia: formData.get('referencia'),
        motivo_consulta: formData.get('motivo_consulta'),
        antecedentes: formData.get('antecedentes'),
        observaciones: formData.get('observaciones'),
        is_active: formData.get('is_active') === 'true'
    };
    
    try {
        let response;
        if (currentEditId) {
            // Actualizar paciente existente
            data.id = currentEditId;
            data.action = 'update';
            response = await fetch('php/patients.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
        } else {
            // Crear nuevo paciente
            data.action = 'create';
            response = await fetch('php/patients.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
        }
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closePatientModal();
            loadPatients();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Error guardando paciente:', error);
        showMessage('Error de conexión al guardar paciente', 'error');
    }
});


// Función para inicializar eventos cuando se carga el contenido
function initializePatientsEvents() {
    // Asignar evento al botón de nuevo paciente
    const newPatientBtn = document.getElementById('newPatientBtn');
    if (newPatientBtn) {
        newPatientBtn.onclick = openPatientModal;
    }
    
    // También puedes asignar otros eventos aquí si es necesario
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.onkeyup = searchPatients;
    }
    
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.onchange = filterPatients;
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('patientModal');
    if (event.target === modal) {
        closePatientModal();
    }
}

// Inicializar cuando se cargue el contenido
if (document.getElementById('patientsTable')) {
    loadPatients();
    initializePatientsEvents();
}



