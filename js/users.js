// Variables globales
let users = [];
let currentEditId = null;

// Mostrar mensaje
function showMessage(message, type = 'success') {
    const messageContainer = document.getElementById('messageContainer');
    if (messageContainer) {
        messageContainer.innerHTML = `
            <div class="${type === 'success' ? 'success-message' : 'error-message'}">
                ${message}
            </div>
        `;
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }
}

// Cargar usuarios
async function loadUsers() {
    try {
        const response = await fetch('php/users.php');
        const data = await response.json();
        
        if (data.success) {
            users = data.users;
            renderUsersTable();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error cargando usuarios:', error);
        showMessage('Error de conexión al cargar usuarios', 'error');
    }
}


// Renderizar tabla de usuarios
function renderUsersTable() {
    const tableBody = document.getElementById('usersTableBody');
    const loading = document.getElementById('loading');
    const table = document.getElementById('usersTable');
    
    if (users.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem;">
                    No hay usuarios registrados
                </td>
            </tr>
        `;
    } else {
        tableBody.innerHTML = users.map(user => `
            <tr>
                <td>${user.id}</td>
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>${user.full_name}</td>
                <td><span class="role-${user.role}">${user.role === 'admin' ? 'Administrador' : 'Psicólogo'}</span></td>
                <td class="${user.is_active ? 'status-active' : 'status-inactive'}">
                    ${user.is_active ? 'Activo' : 'Inactivo'}
                </td>
                <td>${user.last_login ? new Date(user.last_login).toLocaleString() : 'Nunca'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="editUser(${user.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" onclick="deleteUser(${user.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    loading.style.display = 'none';
    table.style.display = 'table';
}

// Abrir modal para nuevo usuario
function openModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
    document.getElementById('userForm').reset();
    document.getElementById('password').required = true;
    document.getElementById('passwordHelp').textContent = '*';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('userModal').style.display = 'block';
}

// Cerrar modal
function closeModal() {
    document.getElementById('userModal').style.display = 'none';
    currentEditId = null;
}

// Editar usuario
async function editUser(id) {
    try {
        const response = await fetch(`./php/users.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            currentEditId = id;
            
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('full_name').value = user.full_name;
            document.getElementById('role').value = user.role;
            document.getElementById('is_active').value = user.is_active.toString();
            
            document.getElementById('password').required = false;
            document.getElementById('passwordHelp').textContent = '';
            document.getElementById('passwordHint').style.display = 'block';
            
            document.getElementById('userModal').style.display = 'block';
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error cargando usuario:', error);
        showMessage('Error al cargar usuario', 'error');
    }
}

// Eliminar usuario
async function deleteUser(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch('./php/users.php', {
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
            loadUsers(); // Recargar la lista
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error eliminando usuario:', error);
        showMessage('Error de conexión al eliminar usuario', 'error');
    }
}

// Manejar envío del formulario
document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        username: formData.get('username'),
        email: formData.get('email'),
        full_name: formData.get('full_name'),
        password: formData.get('password'),
        role: formData.get('role'),
        is_active: formData.get('is_active') === 'true'
    };
    
    try {
        let response;
        if (currentEditId) {
            // Actualizar usuario existente
            data.id = currentEditId;
            data.action = 'update';
            response = await fetch('./php/users.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
        } else {
            // Crear nuevo usuario
            data.action = 'create';
            response = await fetch('./php/users.php', {
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
            closeModal();
            loadUsers(); // Recargar la lista
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Error guardando usuario:', error);
        showMessage('Error de conexión al guardar usuario', 'error');
    }
});

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        closeModal();
    }
}