class SessionManager {
    static async checkSession() {
        try {
            const response = await fetch('./php/check-session.php');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error verificando sesión:', error);
            return { logged_in: false };
        }
    }

    static async logout() {
        try {
            const response = await fetch('./php/logout.php');
            const data = await response.json();
            
            if (data.success) {
                window.location.href = 'index.html';
            }
        } catch (error) {
            console.error('Error cerrando sesión:', error);
            window.location.href = 'index.html';
        }
    }

    static async redirectIfLoggedIn() {
        try {
            const data = await this.checkSession();
            if (data.logged_in && (window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/'))) {
                window.location.href = 'dashboard.html';
            }
        } catch (error) {
            console.error('Error en redirectIfLoggedIn:', error);
        }
    }

    static async redirectIfNotLoggedIn() {
        try {
            const data = await this.checkSession();
            const currentPage = window.location.pathname;
            const isLoginPage = currentPage.endsWith('index.html') || currentPage.endsWith('/');
            
            if (!data.logged_in && !isLoginPage) {
                window.location.href = 'index.html';
            }
            return data;
        } catch (error) {
            console.error('Error en redirectIfNotLoggedIn:', error);
            window.location.href = 'index.html';
            return { logged_in: false };
        }
    }

    static async loadUserInfo(elementId = 'usernameDisplay') {
        try {
            const data = await this.checkSession();
            const element = document.getElementById(elementId);
            
            if (element && data.logged_in && data.user) {
                element.textContent = data.user.username;
            }
            
            return data;
        } catch (error) {
            console.error('Error cargando información del usuario:', error);
            return { logged_in: false };
        }
    }
}