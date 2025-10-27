// Función para mostrar mensajes
function showMessage(message, type = 'error') {
    const messageContainer = document.getElementById('messageContainer');
    if (messageContainer) {
        messageContainer.textContent = message;
        messageContainer.style.display = 'block';
        messageContainer.className = type === 'success' ? 'success-message' : 'error-message';
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            messageContainer.style.display = 'none';
        }, 5000);
    }
}

// Función para manejar errores de JSON
function safeJsonParse(text) {
    try {
        return JSON.parse(text);
    } catch (error) {
        console.error('Error parseando JSON:', error, 'Respuesta:', text);
        return {
            success: false,
            message: 'Respuesta inválida del servidor'
        };
    }
}

// Manejar envío del formulario de login
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const cedula = document.getElementById('cedula').value;
    const password = document.getElementById('password').value;
    const submitBtn = form.querySelector('.login-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Mostrar loading
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('php/login-pacientes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cedula: cedula,
                password: password
            })
        });
        
        // Primero obtener el texto de la respuesta
        const text = await response.text();
        console.log('Respuesta del servidor:', text);
        
        // Luego intentar parsear como JSON
        const data = safeJsonParse(text);
        
        if (data.success) {
            showMessage('✓ ' + data.message, 'success');
            // Redirigir al instrumento MCMI-III
            setTimeout(() => {
                window.location.href = 'instrumento-mcmi.html';
            }, 1000);
        } else {
            showMessage('✗ ' + (data.message || 'Error desconocido'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        showMessage('Error de conexión con el servidor: ' + error.message);
    } finally {
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
    }
});

// Verificar si ya está logueado
async function checkPatientSession() {
    try {
        const response = await fetch('php/check-patient-session.php');
        const text = await response.text();
        const data = safeJsonParse(text);
        
        if (data.success && data.logged_in) {
            // Si ya está logueado, redirigir al instrumento
            window.location.href = 'instrumento-mcmi.html';
        }
    } catch (error) {
        console.error('Error verificando sesión:', error);
    }
}

// Verificar sesión al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    checkPatientSession();
});