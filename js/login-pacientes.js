        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const cedulaInput = document.getElementById('cedula');
            const passwordInput = document.getElementById('password');
            const cedulaError = document.getElementById('cedulaError');
            const passwordError = document.getElementById('passwordError');
            const messageContainer = document.getElementById('messageContainer');
            const submitBtn = document.getElementById('submitBtn');
            
            // Función para mostrar mensajes
            function showMessage(message, type) {
                messageContainer.textContent = message;
                messageContainer.className = 'message-container ' + type;
                messageContainer.style.display = 'block';
                
                // Ocultar mensaje después de 5 segundos
                setTimeout(() => {
                    messageContainer.style.display = 'none';
                }, 5000);
            }
            
            // Validación de cédula (solo números)
            cedulaInput.addEventListener('input', function() {
                const value = this.value;
                
                // Solo permitir números
                if (!/^\d*$/.test(value)) {
                    this.value = value.replace(/[^\d]/g, '');
                }
                
                // Validar longitud
                if (value.length > 10) {
                    this.value = value.slice(0, 10);
                }
                
                validateCedula();
            });
            
            // Validación de contraseña
            passwordInput.addEventListener('input', validatePassword);
            
            // Validación de cédula
            function validateCedula() {
                const value = cedulaInput.value.trim();
                
                if (value === '') {
                    cedulaInput.classList.remove('valid', 'error');
                    cedulaError.style.display = 'none';
                    return false;
                }
                
                if (!/^\d+$/.test(value) || value.length < 6) {
                    cedulaInput.classList.remove('valid');
                    cedulaInput.classList.add('error');
                    cedulaError.style.display = 'block';
                    return false;
                } else {
                    cedulaInput.classList.remove('error');
                    cedulaInput.classList.add('valid');
                    cedulaError.style.display = 'none';
                    return true;
                }
            }
            
            // Validación de contraseña
            function validatePassword() {
                const value = passwordInput.value;
                
                if (value === '') {
                    passwordInput.classList.remove('valid', 'error');
                    passwordError.style.display = 'none';
                    return false;
                }
                
                // Detectar caracteres peligrosos para SQL
                const dangerousChars = /['"\\;()]|(--)|(\/\*)|(\*\/)/;
                
                if (value.length < 6 || dangerousChars.test(value)) {
                    passwordInput.classList.remove('valid');
                    passwordInput.classList.add('error');
                    passwordError.textContent = value.length < 6 
                        ? 'La contraseña debe tener al menos 6 caracteres' 
                        : 'La contraseña contiene caracteres no permitidos';
                    passwordError.style.display = 'block';
                    return false;
                } else {
                    passwordInput.classList.remove('error');
                    passwordInput.classList.add('valid');
                    passwordError.style.display = 'none';
                    return true;
                }
            }
            
            // Envío del formulario
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const cedulaValid = validateCedula();
                const passwordValid = validatePassword();
                
                if (cedulaValid && passwordValid) {
                    // Mostrar estado de carga
                    submitBtn.disabled = true;
                    submitBtn.classList.add('loading');
                    
                    // Simular envío al servidor (en un caso real, aquí iría una petición fetch/AJAX)
                    setTimeout(() => {
                        // Limpiar caracteres peligrosos antes de enviar
                        const cedula = cedulaInput.value.trim();
                        const password = passwordInput.value.replace(/['"\\;()]|(--)|(\/\*)|(\*\/)/g, '');
                        
                        // En un caso real, aquí enviaríamos los datos al servidor
                        // console.log('Datos enviados:', { cedula, password });
                        
                        // Simular respuesta del servidor
                        const success = Math.random() > 0.3; // 70% de éxito para la demo
                        
                        if (success) {
                            showMessage('Inicio de sesión exitoso. Redirigiendo...', 'success-message');
                            // En un caso real, redirigiríamos al usuario
                            window.location.href = 'instrumento-mcmi.html';
                        } else {
                            showMessage('Credenciales incorrectas. Intente nuevamente.', 'error-message');
                        }
                        
                        // Restaurar botón
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('loading');
                    }, 1500);
                } else {
                    showMessage('Por favor, corrija los errores en el formulario.', 'error-message');
                }
            });
            
            // Prevenir pegado de texto con caracteres no numéricos en cédula
            cedulaInput.addEventListener('paste', function(e) {
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(pastedText)) {
                    e.preventDefault();
                    showMessage('Solo puede pegar números en el campo de cédula', 'error-message');
                }
            });
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