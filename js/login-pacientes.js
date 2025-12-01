// Código de validación y manejo de UI movido o integrado en el handler principal si es necesario.
// En este caso, el handler principal (más abajo) ya maneja la lógica real.
// Se mantienen solo las validaciones si son necesarias, pero el bloque mock completo se elimina.

document.addEventListener('DOMContentLoaded', function () {
    const cedulaInput = document.getElementById('cedula');
    const passwordInput = document.getElementById('password');
    const cedulaError = document.getElementById('cedulaError');

    // Validación de cédula (solo números)
    if (cedulaInput) {
        cedulaInput.addEventListener('input', function () {
            const value = this.value;

            // Solo permitir números
            if (!/^\d*$/.test(value)) {
                this.value = value.replace(/[^\d]/g, '');
            }

            // Validar longitud
            if (value.length > 10) {
                this.value = value.slice(0, 10);
            }
        });

        // Prevenir pegado de texto con caracteres no numéricos
        cedulaInput.addEventListener('paste', function (e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^\d+$/.test(pastedText)) {
                e.preventDefault();
            }
        });
    }
});

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
document.getElementById('loginForm').addEventListener('submit', async function (e) {
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
        const response = await fetch('./php/login-pacientes.php', {
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
            // Redirigir al instrumento ICMM-III
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
document.addEventListener('DOMContentLoaded', function () {
    checkPatientSession();
});