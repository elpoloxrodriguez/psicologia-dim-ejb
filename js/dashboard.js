class DashboardManager {
    constructor() {
        this.currentContent = 'dashboard';
        this.init();
    }

    init() {
        this.setupMenuInteractions();
        this.loadContent('dashboard');
    }

    setupMenuInteractions() {
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                const content = link.getAttribute('data-content');
                if (content) {
                    // Remover active de todos los links
                    navLinks.forEach(l => l.classList.remove('active'));

                    // Agregar active al link clickeado
                    link.classList.add('active');

                    // Cargar el contenido
                    this.loadContent(content);
                }
            });
        });
    }

    async loadContent(contentType) {
        this.currentContent = contentType;

        const defaultContent = document.getElementById('defaultContent');
        const loadedContent = document.getElementById('loadedContent');

        // Mostrar loading
        defaultContent.style.display = 'none';
        loadedContent.style.display = 'block';
        loadedContent.innerHTML = `
            <div class="loading-content">
                <i class="fas fa-spinner fa-spin"></i> Cargando...
            </div>
        `;

        try {
            let contentHtml = '';

            switch (contentType) {
                case 'dashboard':
                    defaultContent.style.display = 'block';
                    loadedContent.style.display = 'none';
                    this.updateHeader('Dashboard Principal');
                    return;

                case 'users':
                    contentHtml = await this.loadUsersContent();
                    this.updateHeader('Gestión de Usuarios');
                    break;

                case 'patients':
                    contentHtml = await this.loadPatientsContent();
                    this.updateHeader('Gestión de Pacientes');
                    break;

                case 'evaluations':
                    contentHtml = await this.loadEvaluationsContent();
                    this.updateHeader('Evaluaciones');
                    break;

                case 'reports':
                    contentHtml = '<div class="default-content"><h2>Reportes</h2><p>Generación de reportes...</p></div>';
                    this.updateHeader('Reportes');
                    break;

                case 'settings':
                    contentHtml = '<div class="default-content"><h2>Configuración</h2><p>Configuración del sistema...</p></div>';
                    this.updateHeader('Configuración');
                    break;

                default:
                    contentHtml = '<div class="default-content"><h2>Contenido no encontrado</h2></div>';
                    this.updateHeader('Error');
            }

            loadedContent.innerHTML = contentHtml;

            // Inicializar scripts específicos según el contenido
            if (contentType === 'users') {
                this.initializeUsersScript();
            } else if (contentType === 'patients') {
                this.initializePatientsScript();
            } else if (contentType === 'evaluations') {
                this.initializeEvaluationsScript();
            }

        } catch (error) {
            console.error('Error cargando contenido:', error);
            loadedContent.innerHTML = `
                <div class="default-content">
                    <h2>Error al cargar el contenido</h2>
                    <p>No se pudo cargar la sección solicitada.</p>
                </div>
            `;
        }
    }

    async loadUsersContent() {
        try {
            const response = await fetch('pages/users-content.html');
            if (!response.ok) {
                throw new Error('No se pudo cargar el contenido de usuarios');
            }
            return await response.text();
        } catch (error) {
            return `
                <div class="default-content">
                    <h2>Error al cargar usuarios</h2>
                    <p>No se pudo cargar la gestión de usuarios.</p>
                </div>
            `;
        }
    }

    async loadPatientsContent() {
        try {
            const response = await fetch('pages/patients-content.html');
            if (!response.ok) {
                throw new Error('No se pudo cargar el contenido de pacientes');
            }
            return await response.text();
        } catch (error) {
            return `
                <div class="default-content">
                    <h2>Error al cargar pacientes</h2>
                    <p>No se pudo cargar la gestión de pacientes.</p>
                </div>
            `;
        }
    }

    async loadEvaluationsContent() {
        try {
            const response = await fetch('views/historial.html');
            if (!response.ok) {
                throw new Error('No se pudo cargar el contenido de evaluaciones');
            }
            return await response.text();
        } catch (error) {
            return `
                <div class="default-content">
                    <h2>Error al cargar evaluaciones</h2>
                    <p>No se pudo cargar el historial de evaluaciones.</p>
                </div>
            `;
        }
    }

initializeUsersScript() {
    // Solo cargar el script si no está ya cargado
    if (!window.usersScriptLoaded) {
        const script = document.createElement('script');
        script.src = 'js/users.js';
        script.onload = () => {
            window.usersScriptLoaded = true;
            if (typeof loadUsers === 'function') {
                loadUsers();
            }
        };
        document.head.appendChild(script);
    } else {
        // Si ya está cargado, solo ejecutar la función
        if (typeof loadUsers === 'function') {
            loadUsers();
        }
    }
}

    initializePatientsScript() {
        // Cargar el script de pacientes dinámicamente
        if (!window.patientsScriptLoaded) {
            const script = document.createElement('script');
            script.src = 'js/patients.js';
            script.onload = () => {
                window.patientsScriptLoaded = true;
                if (typeof loadPatients === 'function') {
                    loadPatients();
                }
            };
            document.head.appendChild(script);
        } else {
            if (typeof loadPatients === 'function') {
                loadPatients();
            }
        }
    }

    initializeEvaluationsScript() {
        // Cargar el script de evaluaciones dinámicamente si es necesario
        if (!window.evaluationsScriptLoaded) {
            const script = document.createElement('script');
            script.src = 'js/evaluations.js'; // Ajusta la ruta si es necesario
            script.onload = () => {
                window.evaluationsScriptLoaded = true;
                if (typeof loadReports === 'function') {
                    loadReports();
                }
            };
            document.head.appendChild(script);
        } else {
            if (typeof loadReports === 'function') {
                loadReports();
            }
        }
    }

    updateHeader(title) {
        const header = document.querySelector('.header-left h1');
        if (header) {
            header.textContent = title;
        }
    }
}

// Inicializar el dashboard cuando la página cargue
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardManager = new DashboardManager();
});