// Función segura para mostrar alertas
function showAlert(icon, title, text, timer = null) {
    // Verificar si SweetAlert2 está disponible
    if (typeof Swal !== 'undefined') {
        const config = {
            icon: icon,
            title: title,
            text: text
        };
        
        if (timer) {
            config.timer = timer;
            config.showConfirmButton = false;
        }
        
        return Swal.fire(config);
    } else {
        // Fallback a alertas nativas si SweetAlert2 no está disponible
        console.log(`${title}: ${text}`);
        alert(`${title}: ${text}`);
        return Promise.resolve();
    }
}

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const submitBtn = form.querySelector('.login-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Mostrar loading
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('./php/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Error parseando JSON:', parseError, 'Respuesta:', text);
            await showAlert('error', 'Error', 'Respuesta inválida del servidor');
            return;
        }
        
        if (data.success) {
            await showAlert('success', '¡Éxito!', data.message, 1500);
            // Redirigir al dashboard
            window.location.href = 'dashboard.html';
        } else {
            await showAlert('error', 'Error', data.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        await showAlert('error', 'Error de conexión', error.message || 'No se pudo conectar con el servidor');
    } finally {
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
    }
});