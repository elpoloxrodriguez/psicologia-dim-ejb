<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id']) && !isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener el ID del resultado desde la URL
$resultId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($resultId === 0) {
    header('Location: dashboard.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte ICMM-III - Sistema de Evaluación</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .patient-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-item {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .info-item strong {
            display: block;
            font-size: 0.9em;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .results-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .results-table {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }
        
        .scale-row {
            display: grid;
            grid-template-columns: 80px 1fr 80px 80px 150px 100px;
            gap: 15px;
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }
        
        .scale-row.header {
            display: grid;
            grid-template-columns: 80px 1fr 80px 80px 150px 100px;
            background: #f8f9fa;
            font-weight: bold;
            border-bottom: 2px solid #667eea;
            position: sticky;
            top: 0;
        }
        
        .br-indicator {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .br-fill {
            height: 100%;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .br-absent { background: #28a745; }
        .br-low { background: #6fcf97; }
        .br-present { background: #f2c94c; }
        .br-prominent { background: #f2994a; }
        .br-elevated { background: #eb5757; }
        
        .interpretation-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .interpretation-table {
            overflow-x: auto;
        }
        
        .interpretation-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .interpretation-table th,
        .interpretation-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .interpretation-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .elevated { background: #ffeaea; }
        .prominent { background: #fff4e6; }
        .present { background: #fffae6; }
        .low { background: #f0fff4; }
        .absent { background: #f8f9fa; }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            width: 100%;
            justify-content: center;
        }
        
        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .error-message {
            background: #ffeaea;
            color: #dc3545;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 800px;
            border-radius: 15px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.3);
            animation: slideIn 0.4s ease;
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
            flex-shrink: 0;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
        }
        
        .close-modal:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 30px;
            overflow-y: auto;
            line-height: 1.6;
            font-size: 14px;
            flex-grow: 1;
        }
        
        .modal-body h4 {
            color: #667eea;
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 1.2em;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
        }
        
        .modal-body p {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .modal-body ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .modal-body li {
            margin-bottom: 8px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Print Styles */
        @media print {
            .modal {
                display: none !important;
            }
            
            .btn-info {
                display: none;
            }
            
            .scale-row {
                grid-template-columns: 80px 1fr 80px 80px 150px;
            }
            
            .scale-row.header {
                grid-template-columns: 80px 1fr 80px 80px 150px;
            }
            
            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Encabezado del Reporte -->
        <div class="report-header">
            <h1><i class="fas fa-file-medical-alt"></i> Reporte ICMM-III</h1>
            <div class="patient-info" id="patient-info">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Cargando información del paciente...
                </div>
            </div>
        </div>
        
        <!-- Contenedor Principal -->
        <div id="report-content">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Cargando resultados...
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="action-buttons">
            <a href="../dashboard.html" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Historial
            </a>
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir Reporte
            </button>
        </div>
    </div>
    
    <!-- Modal para información de escala -->
    <div id="scaleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> <span id="modalScaleTitle"></span></h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalScaleContent">
                <!-- La información se cargará aquí dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        const resultId = <?php echo $resultId; ?>;
        
        // Información detallada de cada escala
        const scaleInfo = {
            "1": {
                title: "Esquizoide (Escala 1)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Son individuos que se caracterizan por su falta de deseo y su incapacidad para experimentar placer o dolor intenso. Tienden a ser apáticos, desganados, distantes y asocíales. Sus emociones y necesidades afectivas son mínimas y funcionan como observadores pasivos indiferentes a las recompensas y afectos, así como a las demandas de relaciones con los demás.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Falta de interés en relaciones sociales</li>
                        <li>Afectividad restringida o plana</li>
                        <li>Prefiere actividades solitarias</li>
                        <li>Poca o ninguna experiencia de placer</li>
                        <li>Indiferencia a la crítica o elogios</li>
                        <li>Ausencia de amigos íntimos o confidentes</li>
                        <li>Frialdad emocional y distanciamiento</li>
                    </ul>
                    
                    <h4>Manifestaciones Clínicas</h4>
                    <p>Los pacientes con puntuaciones elevadas en esta escala muestran un distanciamiento social marcado, dificultad para expresar emociones y una aparente falta de motivación para interactuar con otros. Su vida emocional es pobre y limitada.</p>
                    
                    <h4>Contexto de Desarrollo</h4>
                    <p>Este patrón suele desarrollarse en entornos familiares emocionalmente fríos o negligentes, donde el niño aprende a desconectarse emocionalmente como mecanismo de protección.</p>
                `
            },
            "2A": {
                title: "Evitativo (Escala 2A)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Los individuos evitativos experimentan pocos refuerzos positivos de sí mismos y de los demás. Permanecen alerta y siempre en guardia, preparados para distanciarse de la anticipación ansiosa de las experiencias dolorosas o negativamente reforzantes de la vida.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Evitación de actividades sociales por miedo a la crítica</li>
                        <li>Inhibición en situaciones nuevas</li>
                        <li>Sentimientos de inadecuación</li>
                        <li>Miedo a ser rechazado o humillado</li>
                        <li>Deseo de afecto pero miedo a la intimidad</li>
                        <li>Hipersensibilidad a la evaluación negativa</li>
                        <li>Autopercepción de inferioridad social</li>
                    </ul>
                    
                    <h4>Estrategia Adaptativa</h4>
                    <p>Su estrategia adaptativa refleja su miedo y desconfianza hacia los demás. Mantienen un control constante de sus impulsos y sus anhelos de afecto, para prevenir la repetición del dolor y la angustia que han experimentado con otras personas. Sólo pueden protegerse a sí mismos mediante una retirada activa.</p>
                    
                    <h4>Dinámica Interna</h4>
                    <p>A pesar de sus deseos de relacionarse, han aprendido que es mejor negar estos sentimientos y mantener bastante distancia interpersonal. Esta paradoja entre deseo de conexión y miedo al rechazo es central en este patrón.</p>
                `
            },
            "2B": {
                title: "Depresivo (Escala 2B)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Hay cosas comunes en los tres primeros patrones clínicos de personalidad, especialmente tristeza, pesimismo, falta de alegría, incapacidad para experimentar placer y un aparente retardo motor. Se puede relacionar a una incapacidad para sentir alegría como tristeza.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Estado de ánimo depresivo crónico</li>
                        <li>Pesimismo y desesperanza</li>
                        <li>Baja autoestima</li>
                        <li>Sensación de vacío existencial</li>
                        <li>Dificultad para experimentar placer (anhedonia)</li>
                        <li>Autocrítica excesiva</li>
                        <li>Sensación de culpa injustificada</li>
                    </ul>
                    
                    <h4>Diferenciación de Otros Patrones</h4>
                    <p>En el patrón de evitación existe una hipervigilancia hacia dolor anticipado, mientras que en el patrón depresivo ha habido una pérdida significativa, una sensación de rendición y una pérdida de esperanza en la recuperación de la alegría. Ninguno de estos tipos de personalidad experimentan placer, pero por razones diferentes.</p>
                    
                    <h4>Contexto de Desarrollo</h4>
                    <p>Una familia desconsolada, un ambiente estéril y una perspectiva sin esperanzas pueden moldear el estilo de carácter depresivo. La experiencia puede condicionar una orientación desesperanza hacia una pérdida significativa.</p>
                `
            },
            "3": {
                title: "Dependiente (Escala 3)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Los individuos dependientes han aprendido no sólo a recurrir a los demás para obtener cuidado y seguridad, sino también a esperar pasivamente que otros tomen el mando y se lo proporcione. Se caracterizan por una búsqueda de relaciones en las que puedan apoyarse en otros para conseguir afecto, seguridad y consejos.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Necesidad excesiva de ser cuidado</li>
                        <li>Comportamiento sumiso y adhesivo</li>
                        <li>Miedo a la separación</li>
                        <li>Dificultad para tomar decisiones sin consejo</li>
                        <li>Busca constantemente relaciones de apoyo</li>
                        <li>Incomodidad o impotencia cuando está solo</li>
                        <li>Urgencia por establecer nuevas relaciones cuando termina una</li>
                    </ul>
                    
                    <h4>Origen y Desarrollo</h4>
                    <p>Su falta de iniciativa y autonomía es a menudo una consecuencia de la sobreprotección paternal. En función de estas experiencias, simplemente han aprendido que es más cómodo asumir un rol pasivo en las relaciones interpersonales, aceptando la bondad y el apoyo que puedan encontrar, y se someten de buen grado a los deseos de los demás con mantener su afecto.</p>
                    
                    <h4>Dinámica Interpersonal</h4>
                    <p>Esta dependencia excesiva puede llevar a relaciones desequilibradas donde el individuo sacrifica sus propias necesidades y deseos para mantener el apoyo del otro.</p>
                `
            },
            "4": {
                title: "Histriónico (Escala 4)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Aunque los individuos histriónicos recurren a los demás en igual medida que los dependientes, a primera vista parecen ser bastante diferentes de sus equivalentes más pasivos. Esta diferencia en su estilo observable surge desde su manipulación fácil y emprendedora de los hechos, mediante la cual maximizan la atención y los favores que reciben y evitan la indiferencia y la desaprobación de los demás.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Búsqueda de atención constante</li>
                        <li>Comportamiento seductor o provocativo</li>
                        <li>Expresión emocional exagerada</li>
                        <li>Sugestionabilidad</li>
                        <li>Considera las relaciones más íntimas de lo que son</li>
                        <li>Utiliza la apariencia física para llamar la atención</li>
                        <li>Estilo de habla impresionista y carente de detalles</li>
                    </ul>
                    
                    <h4>Dinámica Subyacente</h4>
                    <p>Debajo de su apariencia confiada subyace el miedo a la autonomía genuina y una reiterada necesidad de señales de aceptación y aprobación. El aprecio y el afecto deben ser constantemente alimentados y se buscan en cada fuente interpersonal y en todos los contextos sociales.</p>
                    
                    <h4>Manifestaciones Sociales</h4>
                    <p>Estos individuos a menudo muestran una insaciable, e incluso indiscriminada, búsqueda de estimulación y afecto. Su inteligente (y a menudo ingenioso) comportamiento social da la apariencia de confianza personal y seguridad en sí mismo.</p>
                `
            },
            "5": {
                title: "Narcisista (Escala 5)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Los individuos narcisistas destacan por su forma egoísta de dedicarse a sí mismos, experimentando placer primario simplemente siendo pasivos o centrándose en sí mismos. Sus experiencias tempranas les han enseñado a sobrevalorarse.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Sentimientos grandiosos de autoimportancia</li>
                        <li>Preocupación por fantasías de éxito ilimitado</li>
                        <li>Creencia de ser "especial" o único</li>
                        <li>Requiere admiración excesiva</li>
                        <li>Falta de empatía hacia los demás</li>
                        <li>Actitudes arrogantes y soberbias</li>
                        <li>Envidia de los demás o creencia de que otros le envidian</li>
                    </ul>
                    
                    <h4>Dinámica Interpersonal</h4>
                    <p>Su seguridad y superioridad puede estar fundada sobre premisas falsas; es decir, puede que no se apoyen en logros reales o maduros. Sin embargo, los narcisistas asumen alegremente que los demás reconocerán su cualidad de ser especiales. Por tanto mantienen un aire arrogante de seguridad en sí mismos y, sin pensarlo ni pretenderlo conscientemente, explotan a los demás en beneficio propio.</p>
                    
                    <h4>Motivación</h4>
                    <p>Aunque las atenciones de los demás son bienvenidas y alentadas, su aire de esnobismo y superioridad pretenciosa requiere poca confirmación de logros reales o de aprobación social. Su confianza sublime en que las cosas van a salir bien motiva poco para implicarse en el «toma y daca» de la vida social.</p>
                `
            },
            "6A": {
                title: "Antisocial (Escala 6A)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Los individuos antisociales actúan para contrarrestar las expectativas de dolor y la posible agresión a manos de los otros, al implicarse en conductas engañosas o ilegales con el fin de explotar el entorno en beneficio propio. Su orientación engrandecida refleja su escepticismo hacia los motivos de los demás, su deseo de autonomía y el anhelo de venganza y recompensa por lo que ellos consideran que fueron injusticias pasadas.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Desprecio por los derechos de los demás</li>
                        <li>Impulsividad e irresponsabilidad</li>
                        <li>Falta de remordimiento</li>
                        <li>Engaño y manipulación</li>
                        <li>Irritabilidad y agresividad</li>
                        <li>Indiferencia por la seguridad propia y ajena</li>
                        <li>Incapacidad para mantener conductas laborales coherentes</li>
                    </ul>
                    
                    <h4>Justificación Personal</h4>
                    <p>Son irresponsables e impulsivos, cualidades que justifican por qué consideran poco fiables y desleales a los demás. Su insensibilidad y crueldad son sus únicos medios de evitar el abuso y convertirse en víctimas.</p>
                    
                    <h4>Historia de Desarrollo</h4>
                    <p>Este patrón a menudo se desarrolla en entornos donde se modela o refuerza el comportamiento antisocial, con antecedentes de problemas conductuales tempranos y posiblemente trastorno por déficit de atención e hiperactividad en la infancia.</p>
                `
            },
            "6B": {
                title: "Agresivo/Sádico (Escala 6B)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Reconoce que individuos que no se consideran antisociales pueden obtener placer y satisfacción personal humillando a otras personas y violando sus derechos y sentimientos. Dependiendo de la clase social y de otros factores moderadores, pueden ser análogos a los rasgos clínicos de lo que se conoce en la bibliografía como el carácter sádico, o pueden exhibir estilos de carácter parecidos a la competitiva y ambiciosa personalidad Tipo A.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Comportamiento cruel y abusivo</li>
                        <li>Humillación intencional de otros</li>
                        <li>Complacencia con el sufrimiento ajeno</li>
                        <li>Hostilidad y combatividad</li>
                        <li>Dominación y control sobre otros</li>
                        <li>Intimidación para lograr obediencia</li>
                        <li>Fascinación por la violencia y armas</li>
                    </ul>
                    
                    <h4>Manifestaciones</h4>
                    <p>Denominadas como personalidades agresivas en la teoría de Millón, generalmente son hostiles y globalmente combativos, y parecen indiferentes o incluso complacidos por las consecuencias destructivas de sus comportamientos contenciosos, abusivos y brutales.</p>
                    
                    <h4>Adaptación Social</h4>
                    <p>Aunque algunos encubren sus tendencias más maliciosas y orientadas al poder desempeñando roles y vocaciones que reciben la aprobación pública, se delatan por sus acciones dominantes, antagónicas y a menudo persecutorias.</p>
                `
            },
            "7": {
                title: "Compulsivo (Escala 7)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Esta orientación ambivalente coincide con el Trastorno de personalidad Obsesivo-Compulsivo; Los individuos compulsivos han sido intimidados y coaccionados para aceptar las demandas y los juicios impuestos por los demás. Sus formas de actuar prudentes, controladas y perfeccionistas derivan de un conflicto entre la hostilidad hacia los demás y el miedo a la desaprobación social.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Preocupación por el orden y control</li>
                        <li>Perfeccionismo que interfiere con la actividad</li>
                        <li>Dedicación excesiva al trabajo</li>
                        <li>Inflexibilidad en temas de moral y valores</li>
                        <li>Incapacidad para deshacerse de objetos inútiles</li>
                        <li>Reluctancia a delegar tareas</li>
                        <li>Avaricia y rigidez en los gastos</li>
                    </ul>
                    
                    <h4>Conflicto Interno</h4>
                    <p>Resuelven esta ambivalencia suprimiendo su resentimiento y manifestando un conformismo excesivo y exigiéndose mucho a sí mismos y a los demás. Su disciplinado dominio de sí mismos les ayuda a controlar unos sentimientos de rebeldía, intensos, aunque ocultos, dando lugar a una pasividad abierta y una aparente conformidad pública.</p>
                    
                    <h4>Expresión Emocional</h4>
                    <p>Sin embargo, tras esta máscara de decencia y dominio, hay sentimientos de ira y de rebeldía que ocasionalmente desbordan sus controles. Esta expresión emocional contenida puede manifestarse en síntomas psicosomáticos o en episodios explosivos ocasionales.</p>
                `
            },
            "8A": {
                title: "Negativista/Pasivo-Agresivo (Escala 8A)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Esta orientación ambivalente se aproxima al Trastorno de personalidad Pasivo-Agresivo; Los individuos negativistas sienten una pugna entre seguir las recompensas ofrecidas por otros y aquéllas que ellos mismos desean. Esta lucha representa una incapacidad para resolver conflictos similar a la que padecen los obsesivos compulsivos.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Resistencia pasiva a demandas sociales</li>
                        <li>Quejas de ser incomprendido</li>
                        <li>Actitud cínica y resentida</li>
                        <li>Critica y desprecio hacia figuras de autoridad</li>
                        <li>Expresión de envidia hacia los más afortunados</li>
                        <li>Quejas persistentes de mala suerte</li>
                        <li>Alternancia entre desafío hostil y arrepentimiento</li>
                    </ul>
                    
                    <h4>Patrón de Comportamiento</h4>
                    <p>Sin embargo, los conflictos de los individuos negativistas se mantienen cercanos a la conciencia e irrumpen en la vida diaria. Estos individuos experimentan continuas discusiones y desengaños cuando vacilan entre la deferencia y el desafío, la obediencia y la oposición agresiva. Su comportamiento se caracteriza por un patrón errático de ira explosiva o tozudez entremezclado con períodos de culpa y vergüenza.</p>
                    
                    <h4>Dinámica Interpersonal</h4>
                    <p>Esta ambivalencia constante genera frustración en quienes interactúan con ellos, ya que nunca están completamente conformes ni completamente en desacuerdo, sino en un estado perpetuo de resistencia pasiva.</p>
                `
            },
            "8B": {
                title: "Autodestructivo (Escala 8B)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Esta orientación discordante se corresponde con el Trastorno de personalidad autodestructiva (masoquista), un tipo de carácter bien descrita en la bibliografía. Las personas masoquistas se relacionan con otros de forma servil y autosacrificándose, permiten y, quizá incluso, alientan a los demás a explotarles o aprovecharse de ellos.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Relaciones autosacrificantes</li>
                        <li>Permite que otros se aprovechen de él/ella</li>
                        <li>Rechaza oportunidades de placer</li>
                        <li>Auto-sabotaje del éxito personal</li>
                        <li>Se siente indigno de preocupación o cuidado</li>
                        <li>Rechaza ayuda cuando está en problemas</li>
                        <li>Se involucra en relaciones que conducen al desengaño</li>
                    </ul>
                    
                    <h4>Dinámica Interna</h4>
                    <p>Si manifiestan sus peores rasgos, muchos mantienen que merecen ser avergonzados y humillados. Para agravar su dolor y angustia, que pueden experimentar como reconfortantes, las personas de personalidad autodestructiva rememoran sus desgracias pasadas activa y reiteradamente y esperan resultados problemáticos de circunstancias que se considerarían afortunadas.</p>
                    
                    <h4>Manifestaciones Conductuales</h4>
                    <p>Actúan típicamente de un modo modesto y humilde. A menudo intensifican sus déficits y se colocan en una posición inferior o despreciable. Esta postura de víctima puede ser una forma de obtener atención y cuidado de manera indirecta.</p>
                `
            },
            "S": {
                title: "Esquizotípico (Escala S)",
                content: `
                    <h4>Descripción General</h4>
                    <p>Trastorno esquizotípico de personalidad representa una orientación cognitivamente disfuncional e interpersonalmente indiferente. Los individuos esquizotípicos prefieren el aislamiento social con relaciones y obligaciones personales mínimas. Se inclinan a comportarse de forma autista o cognitivamente confusa, piensan tangencialmente y a menudo parecen absortos en sí mismos y pensativos.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Ideas de referencia (no delirantes)</li>
                        <li>Creencias extrañas o pensamiento mágico</li>
                        <li>Experiencias perceptivas inhabituales</li>
                        <li>Pensamiento y lenguaje raros</li>
                        <li>Comportamiento o apariencia extraños</li>
                        <li>Falta de amigos íntimos</li>
                        <li>Ansiedad social excesiva</li>
                    </ul>
                    
                    <h4>Presentación Clínica</h4>
                    <p>Sus excentricidades son notables y a menudo los demás lo perciben como extraños o diferentes. Dependiendo de si su patrón básico es pasivo o activo muestran cautela ansiosa e hipersensibilidad o aplanamiento emocional y deficiencia de afecto.</p>
                    
                    <h4>Diferenciación de la Esquizofrenia</h4>
                    <p>A diferencia de la esquizofrenia, los síntomas esquizotípicos son menos graves y no incluyen delirios francos o alucinaciones prominentes, aunque pueden presentar experiencias perceptivas inusuales.</p>
                `
            },
            "C": {
                title: "Límite/Borderline (Escala C)",
                content: `
                    <h4>Descripción General</h4>
                    <p>La personalidad límite a menudo es la base de otros trastornos de personalidad menos graves. Cada variante límite tiene defectos estructurales y experimenta intensos estados de ánimo con periodos recurrentes de depresión y apatía, a menudo salpicados con rachas de ansiedad o euforia.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Inestabilidad en relaciones interpersonales</li>
                        <li>Autoimagen inestable</li>
                        <li>Impulsividad en áreas autodestructivas</li>
                        <li>Inestabilidad afectiva</li>
                        <li>Sentimientos crónicos de vacío</li>
                        <li>Esfuerzos frenéticos para evitar abandono</li>
                        <li>Comportamientos suicidas recurrentes</li>
                    </ul>
                    
                    <h4>Características Distintivas</h4>
                    <p>Lo que les distingue claramente de otros patrones graves (esquizotípicos y paranoide) es la falta de regulación del afecto, que se ve más claramente en la inestabilidad y labilidad de su estado de ánimo. Además, muchos tienen pensamientos recurrentes de automutilación y suicidio, parecen demasiado preocupados en asegurarse cariño, tienen dificultades en mantener un sentido claro de identidad y exhiben una ambivalencia cognitivo-afectiva que se hace evidente en sus sentimientos conflictivos de cólera, amor y culpa hacia los demás.</p>
                    
                    <h4>Manifestaciones Conductuales</h4>
                    <p>Esta inestabilidad se manifiesta en cambios bruscos de planes profesionales, valores, tipos de amigos y autoimagen, generando un patrón de vida caótico e impredecible.</p>
                `
            },
            "P": {
                title: "Paranoide (Escala P)",
                content: `
                    <h4>Descripción General</h4>
                    <p>La personalidad paranoide muestra una vigilante desconfianza hacia los demás y una actitud defensiva tensa ante la anticipación de las críticas y los engaños. Presentan una irritabilidad abusiva y tienden a provocar la exasperación y el enfado de otros.</p>
                    
                    <h4>Características Principales</h4>
                    <ul>
                        <li>Sospecha sin base suficiente</li>
                        <li>Preocupación por dudas injustificadas</li>
                        <li>Renuencia a confiar en otros</li>
                        <li>Percepción de ataques a su carácter</li>
                        <li>Rencor persistente</li>
                        <li>Percepción de significados ocultos en eventos benignos</li>
                        <li>Celos recurrentes sin justificación</li>
                    </ul>
                    
                    <h4>Dinámica Defensiva</h4>
                    <p>Los individuos paranoides a menudo expresan miedo a perder la independencia, lo que les lleva a resistirse vigorosamente a las influencias y al control externo, mientras los otros dos patrones graves se identifican ya sea por la inestabilidad de sus afectos (límite) ya por la falta de regulación de su cognición (esquizotípico). Los sujetos paranoides se distinguen por la inmutabilidad de sus sentimientos y la inflexibilidad de su pensamiento.</p>
                    
                    <h4>Relaciones Interpersonales</h4>
                    <p>Esta desconfianza generalizada dificulta enormemente la formación de relaciones cercanas y de confianza, generando un círculo vicioso donde su suspicacia genera rechazo que confirma sus sospechas.</p>
                `
            },
            "A": {
                title: "Trastornos de Ansiedad (Escala A)",
                content: ``
            },
            "H": {
                title: "Trastorno Somatoformo (Escala H)",
                content: ``
            },
                
        };
        
        // Función para abrir el modal con información de la escala
        function openScaleInfo(scaleId) {
            const modal = document.getElementById('scaleModal');
            const modalTitle = document.getElementById('modalScaleTitle');
            const modalContent = document.getElementById('modalScaleContent');
            
            if (scaleInfo[scaleId]) {
                modalTitle.textContent = scaleInfo[scaleId].title;
                modalContent.innerHTML = scaleInfo[scaleId].content;
            } else {
                modalTitle.textContent = `Escala ${scaleId}`;
                modalContent.innerHTML = `
                    <h4>Información de la Escala</h4>
                    <p>Información detallada sobre esta escala estará disponible próximamente.</p>
                    <p>Esta escala evalúa características específicas de personalidad según el modelo ICMM-III.</p>
                `;
            }
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // Función para cerrar el modal
        function closeModal() {
            const modal = document.getElementById('scaleModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById('scaleModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // También cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // 175 preguntas oficiales del ICMM-III
        const questions = [
            { id: 1, question: "Últimamente parece que me quedo sin fuerzas, incluso por la mañana" },
            { id: 2, question: "Me parece muy bien que haya normas porque son una buena guía a seguir" },
            { id: 3, question: "Disfruto haciendo tantas cosas diferentes que no puedo decidir por cuál empezar" },
            { id: 4, question: "Gran parte del tiempo me siento débil y cansado" },
            { id: 5, question: "Sé que soy superior a los demás, por eso no me importa lo que piensen" },
            { id: 6, question: "La gente nunca ha reconocido suficientemente las cosas que he hecho" },
            { id: 7, question: "Si mi familia me presiona, es probable que me enfade y me resista a hacer lo que ellos quieren" },
            { id: 8, question: "La gente se burla de mí a mis espaldas, hablando de lo que hago o parezco" },
            { id: 9, question: "Frecuentemente critico mucho a la gente que me irrita" },
            { id: 10, question: "Raramente exteriorizo las pocas emociones que suelo tener" },
            { id: 11, question: "Me resulta difícil mantener el equilibrio cuando camino" },
            { id: 12, question: "Muestro mis emociones fácil y rápidamente" },
            { id: 13, question: "En el pasado, mis hábitos de tomar drogas me han causado problemas a menudo" },
            { id: 14, question: "Algunas veces puedo ser bastante duro y desagradable con mi familia" },
            { id: 15, question: "Las cosas que hoy van bien no durarán mucho tiempo" },
            { id: 16, question: "Soy una persona muy agradable y sumisa" },
            { id: 17, question: "Cuando era adolescente, tuve muchos problemas por mi mal comportamiento en el colegio" },
            { id: 18, question: "Tengo miedo a acercarme mucho a otra persona porque podría acabar siendo ridiculizado o avergonzado" },
            { id: 19, question: "Parece que elijo amigos que terminan tratándome mal" },
            { id: 20, question: "He tenido pensamientos tristes gran parte de mi vida desde que era niño" },
            { id: 21, question: "Me gusta coquetear con las personas del otro sexo" },
            { id: 22, question: "Soy una persona muy variable y cambio de opiniones y sentimientos continuamente" },
            { id: 23, question: "Beber alcohol nunca me ha causado verdaderos problemas en mi trabajo" },
            { id: 24, question: "Hace unos años comencí a sentirme un fracasado" },
            { id: 25, question: "Me siento culpable muy a menudo sin ninguna razón" },
            { id: 26, question: "Los demás envidian mis capacidades" },
            { id: 27, question: "Cuando puedo elegir, prefiero hacer las cosas solo" },
            { id: 28, question: "Pienso que el comportamiento de mi familia debería ser estrictamente controlado" },
            { id: 29, question: "La gente normalmente piensa que soy una persona reservada y seria" },
            { id: 30, question: "Últimamente he comenzado a sentir deseos de destrozar cosas" },
            { id: 31, question: "Creo que soy una persona especial y merezco que los demás me presten una particular atención" },
            { id: 32, question: "Siempre estoy buscando hacer nuevos amigos y conocer gente nueva" },
            { id: 33, question: "Si alguien me criticase por cometer un error, rápidamente le señalaría sus propios errores" },
            { id: 34, question: "Últimamente he perdido los nervios" },
            { id: 35, question: "A menudo renuncio a hacer cosas porque temo no hacerlas bien" },
            { id: 36, question: "Muchas veces me dejo llevar por mis emociones de ira y luego me siento terriblemente culpable por ello" },
            { id: 37, question: "Muy a menudo pierdo mi capacidad para percibir sensaciones en partes de mi cuerpo" },
            { id: 38, question: "Hago lo que quiero sin preocuparme de las consecuencias que tenga en los demás" },
            { id: 39, question: "Tomar las llamadas 'drogas ilegales' puede ser imprudente, pero reconozco que en el pasado las he necesitado" },
            { id: 40, question: "Creo que soy una persona miedosa e inhibida" },
            { id: 41, question: "He hecho impulsivamente muchas cosas estúpidas que han llegado a causarme grandes problemas" },
            { id: 42, question: "Nunca perdono un insulto ni olvido una situación embarazosa que alguien me haya causado" },
            { id: 43, question: "A menudo me siento triste o tenso, inmediatamente después de que me haya pasado algo bueno" },
            { id: 44, question: "Ahora me siento terriblemente deprimido y triste gran parte del tiempo" },
            { id: 45, question: "Siempre hago lo posible por complacer a los demás, incluso a quienes no me gustan" },
            { id: 46, question: "Siempre he sentido menos interés por el sexo que la mayoría de la gente" },
            { id: 47, question: "Siempre tiendo a culparme a mí mismo cuando las cosas salen mal" },
            { id: 48, question: "Hace mucho tiempo decidí que lo mejor es tener poco que ver con la gente" },
            { id: 49, question: "Desde niño, siempre he tenido que tener cuidado con la gente que intentaba engañarme" },
            { id: 50, question: "No soporto a las personas influyentes que siempre piensan que pueden hacer las cosas mejor que yo" },
            { id: 51, question: "Cuando las cosas son aburridas, me gusta provocar algo interesante o divertido" },
            { id: 52, question: "Tengo un problema con el alcohol que nos ha creado dificultades a mi familia y a mí" },
            { id: 53, question: "Los castigos nunca me han impedido hacer lo que yo quería" },
            { id: 54, question: "Muchas veces me siento muy alegre y animado sin ninguna razón" },
            { id: 55, question: "En las últimas semanas me he sentido agotado sin ningún motivo especial" },
            { id: 56, question: "Últimamente me he sentido muy culpable porque ya no soy capaz de hacer nada bien" },
            { id: 57, question: "Pienso que soy una persona muy sociable y extravertida" },
            { id: 58, question: "Me he vuelto muy nervioso en las últimas semanas" },
            { id: 59, question: "Controlo muy bien mi dinero para estar preparado en caso de necesidad" },
            { id: 60, question: "Simplemente, no he tenido la suerte que otros han tenido en la vida" },
            { id: 61, question: "Algunas ideas me dan vueltas en la cabeza una y otra vez y no desaparecen" },
            { id: 62, question: "Desde hace uno o dos años, al pensar sobre la vida, me siento muy triste y desanimado" },
            { id: 63, question: "Mucha gente ha estado espiando mi vida privada durante años" },
            { id: 64, question: "No sé por qué pero, a veces, digo cosas crueles sólo para hacer sufrir a los demás" },
            { id: 65, question: "En el último año he cruzado el Atlántico en avión 30 veces" },
            { id: 66, question: "En el pasado, el hábito de abusar de las drogas me ha hecho faltar al trabajo" },
            { id: 67, question: "Tengo muchas ideas que son avanzadas para los tiempos actuales" },
            { id: 68, question: "Últimamente tengo que pensar las cosas una y otra vez sin ningún motivo" },
            { id: 69, question: "Evito la mayoría de las situaciones sociales porque creo que la gente va a criticarme o rechazarme" },
            { id: 70, question: "Muchas veces pienso que no merezco las cosas buenas que me pasan" },
            { id: 71, question: "Cuando estoy solo, a menudo noto cerca de mí la fuerte presencia de alguien que no puede ser visto" },
            { id: 72, question: "Me siento desorientado, sin objetivos, y no sé hacia dónde voy en la vida" },
            { id: 73, question: "A menudo dejo que los demás tomen por mí decisiones importantes" },
            { id: 74, question: "No puedo dormirme, y me levanto tan cansado como al acostarme" },
            { id: 75, question: "Últimamente sudo mucho y me siento muy tenso" },
            { id: 76, question: "Tengo una y otra vez pensamientos extraños de los que desearía poder librarme" },
            { id: 77, question: "Tengo muchos problemas para controlar el impulso de beber en exceso" },
            { id: 78, question: "Aunque esté despierto, parece que no me doy cuenta de la gente que está cerca de mí" },
            { id: 79, question: "Con frecuencia estoy irritado y de mal humor" },
            { id: 80, question: "Para mí es muy fácil hacer muchos amigos" },
            { id: 81, question: "Me avergüenzo de algunos de los abusos que sufrí cuando era joven" },
            { id: 82, question: "Siempre me aseguro de que mi trabajo esté bien planeado y organizado" },
            { id: 83, question: "Mis estados de ánimo cambian mucho de un día para otro" },
            { id: 84, question: "Me falta confianza en mí mismo para arriesgarme a probar algo nuevo" },
            { id: 85, question: "No culpo a quien se aprovecha de alguien que se lo permite" },
            { id: 86, question: "Desde hace algún tiempo me siento triste y deprimido y no consigo animarme" },
            { id: 87, question: "A menudo me enfado con la gente que hace las cosas lentamente" },
            { id: 88, question: "Cuando estoy en una fiesta nunca me aíslo de los demás" },
            { id: 89, question: "Observo a mi familia de cerca para saber en quién se puede confiar y en quién no" },
            { id: 90, question: "Algunas veces me siento confuso y molesto cuando la gente es amable conmigo" },
            { id: 91, question: "El consumo de 'drogas ilegales' me ha causado discusiones con mi familia" },
            { id: 92, question: "Estoy solo la mayoría del tiempo y lo prefiero así" },
            { id: 93, question: "Algunos miembros de mi familia dicen que soy egoísta y que sólo pienso en mí mismo" },
            { id: 94, question: "La gente puede hacerme cambiar de ideas fácilmente, incluso cuando pienso que ya había tomado una decisión" },
            { id: 95, question: "A menudo irrito a la gente cuando les doy órdenes" },
            { id: 96, question: "En el pasado la gente decía que yo estaba muy interesado y apasionado por demasiadas cosas" },
            { id: 97, question: "Estoy de acuerdo con el refrán: 'Al que madruga Dios le ayuda'" },
            { id: 98, question: "Mis sentimientos hacia las personas importantes en mi vida muchas veces oscilan entre el amor y el odio" },
            { id: 99, question: "Cuando estoy en una reunión social, en grupo, casi siempre me siento tenso y cohibido" },
            { id: 100, question: "Supongo que no soy diferente de mis padres ya que, hasta cierto punto, me he convertido en un alcohólico" },
            { id: 101, question: "Creo que no me tomo muchas de las responsabilidades familiares tan seriamente como debería" },
            { id: 102, question: "Desde que era niño he ido perdiendo contacto con la realidad" },
            { id: 103, question: "Gente mezquina intenta con frecuencia aprovecharse de lo que he realizado o ideado" },
            { id: 104, question: "No puedo experimentar mucho placer porque no creo merecerlo" },
            { id: 105, question: "Tengo pocos deseos de hacer amigos íntimos" },
            { id: 106, question: "He tenido muchos periodos en mi vida en los que he estado tan animado y he consumido tanta energía que luego me he sentido muy bajo de ánimo" },
            { id: 107, question: "He perdido completamente mi apetito y la mayoría de las noches tengo problemas para dormir" },
            { id: 108, question: "Me preocupa mucho que me dejen solo y tenga que cuidar de mí mismo" },
            { id: 109, question: "El recuerdo de una experiencia muy perturbadora de mi pasado sigue apareciendo en mis pensamientos" },
            { id: 110, question: "El año pasado aparecí en la portada de varias revistas" },
            { id: 111, question: "Parece que he perdido el interés en la mayoría de las cosas que solía encontrar placenteras, como el sexo" },
            { id: 112, question: "He estado abatido y triste mucho tiempo en mi vida desde que era bastante joven" },
            { id: 113, question: "Me he metido en problemas con la ley un par de veces" },
            { id: 114, question: "Una buena manera de evitar los errores es tener una rutina para hacer las cosas" },
            { id: 115, question: "A menudo otras personas me culpan de cosas que no he hecho" },
            { id: 116, question: "He tenido que ser realmente duro con algunas personas para mantenerlas a raya" },
            { id: 117, question: "La gente piensa que, a veces, hablo sobre cosas extrañas o diferentes a las de ellos" },
            { id: 118, question: "Ha habido veces en las que no he podido pasar el día sin tomar drogas" },
            { id: 119, question: "La gente está intentando hacerme creer que estoy loco" },
            { id: 120, question: "Haría algo desesperado para impedir que me abandonase una persona que quiero" },
            { id: 121, question: "Sigo dándome atracones de comida un par de veces a la semana" },
            { id: 122, question: "Parece que echo a perder las buenas oportunidades que se cruzan en mi camino" },
            { id: 123, question: "Siempre me ha resultado difícil dejar de sentirme deprimido y triste" },
            { id: 124, question: "Cuando estoy solo y lejos de casa, a menudo comienzo a sentirme tenso y lleno de pánico" },
            { id: 125, question: "A veces las personas se molestan conmigo porque dicen que hablo mucho o demasiado deprisa para ellas" },
            { id: 126, question: "Hoy, la mayoría de la gente de éxito ha sido afortunada o deshonesta" },
            { id: 127, question: "No me involucro con otras personas a no ser que esté seguro de que les voy a gustar" },
            { id: 128, question: "Me siento profundamente deprimido sin ninguna razón que se me ocurra" },
            { id: 129, question: "Años después, todavía tengo pesadillas acerca de un acontecimiento que supuso una amenaza real para mi vida" },
            { id: 130, question: "Ya no tengo energía para concentrarme en mis responsabilidades diarias" },
            { id: 131, question: "Beber alcohol me ayuda cuando me siento deprimido" },
            { id: 132, question: "Odio pensar en algunas de las formas en las que se abusó de mí cuando era un niño" },
            { id: 133, question: "Incluso en los buenos tiempos, siempre he tenido miedo de que las cosas pronto fuesen mal" },
            { id: 134, question: "Algunas veces, cuando las cosas empiezan a torcerse en mi vida, me siento como si estuviera loco o fuera de la realidad" },
            { id: 135, question: "Estar solo, sin la ayuda de alguien cercano de quien depender, realmente me asusta" },
            { id: 136, question: "Sé que he gastado más dinero del que debiera comprando 'drogas ilegales'" },
            { id: 137, question: "Siempre compruebo que he terminado mi trabajo antes de tomarme un descanso para actividades de ocio" },
            { id: 138, question: "Noto que la gente está hablando de mí cuando paso a su lado" },
            { id: 139, question: "Se me da muy bien inventar excusas cuando me meto en problemas" },
            { id: 140, question: "Creo que hay una conspiración contra mí" },
            { id: 141, question: "Siento que la mayoría de la gente tiene una mala opinión de mí" },
            { id: 142, question: "Frecuentemente siento que no hay nada dentro de mí, como si estuviera vacío y hueco" },
            { id: 143, question: "Algunas veces me obligo a vomitar después de comer" },
            { id: 144, question: "Creo que me esfuerzo mucho por conseguir que los demás admiren las cosas que hago o digo" },
            { id: 145, question: "Me paso la vida preocupándome por una cosa u otra" },
            { id: 146, question: "Siempre me pregunto cuál es la razón real de que alguien sea especialmente agradable conmigo" },
            { id: 147, question: "Ciertos pensamientos vuelven una y otra vez a mi mente" },
            { id: 148, question: "Pocas cosas en la vida me dan placer" },
            { id: 149, question: "Me siento tembloroso y tengo dificultades para conciliar el sueño debido a dolorosos recuerdos de un hecho pasado que pasan por mi cabeza repetidamente" },
            { id: 150, question: "Pensar en el futuro al comienzo de cada día me hace sentir terriblemente deprimido" },
            { id: 151, question: "Nunca he sido capaz de librarme de sentir que no valgo nada para los demás" },
            { id: 152, question: "Tengo un problema con la bebida que he tratado de solucionar sin éxito" },
            { id: 153, question: "Alguien ha estado intentando controlar mi mente" },
            { id: 154, question: "He intentado suicidarme" },
            { id: 155, question: "Estoy dispuesto a pasar hambre para estar aún más delgado de lo que estoy" },
            { id: 156, question: "No entiendo por qué algunas personas me sonríen" },
            { id: 157, question: "No he visto un coche en los últimos diez años" },
            { id: 158, question: "Me pongo muy tenso con las personas que no conozco bien, porque pueden querer hacerme daño" },
            { id: 159, question: "Alguien tendría que ser bastante excepcional para entender mis habilidades especiales" },
            { id: 160, question: "Mi alma está afectada por 'imágenes mentales' de algo terrible que me pasó" },
            { id: 161, question: "Parece que creo situaciones con los demás en las que acabo herido o me siento rechazado" },
            { id: 162, question: "A menudo me pierdo en mis pensamientos y me olvido de lo que está pasando a mi alrededor" },
            { id: 163, question: "La gente dice que soy una persona delgada, pero creo que mis muslos y mi trasero son demasiado grandes" },
            { id: 164, question: "Hay terribles hechos de mi pasado que vuelven repetidamente para perseguirme en mis pensamientos y sueños" },
            { id: 165, question: "No tengo amigos íntimos al margen de mi familia" },
            { id: 166, question: "Casi siempre actúo rápidamente y no pienso las cosas tanto como debiera" },
            { id: 167, question: "Tengo mucho cuidado en mantener mi vida como algo privado, de manera que nadie pueda aprovecharse de mí" },
            { id: 168, question: "Con mucha frecuencia oigo las cosas con tanta claridad que me molesta" },
            { id: 169, question: "Siempre estoy dispuesto a ceder en una riña o desacuerdo porque temo el enfado o rechazo de los demás" },
            { id: 170, question: "Repito ciertos comportamientos una y otra vez, algunas veces para reducir mi ansiedad y otras para evitar que pase algo malo" },
            { id: 171, question: "Recientemente he pensado muy en serio en quitarme de en medio" },
            { id: 172, question: "La gente me dice que soy una persona muy formal y moral" },
            { id: 173, question: "Todavía me aterrorizo cuando pienso en una experiencia traumática que tuve hace años" },
            { id: 174, question: "Aunque me da miedo hacer amistades, me gustaría tener más de las que tengo" },
            { id: 175, question: "A menudo me pierdo en mis pensamientos y me olvido de lo que está pasando a mi alrededor" }
        ];

        // Escalas del ICMM-III corregidas según los resultados del Excel
        const scales = {
            // Patrones clínicos de personalidad
            "1": { name: "Esquizoide", questions: [5,11,28,33,39,47,49,58,93,102,106,143,149,157,166,168] },
            "2A": { name: "Evitativo", questions: [19,41,48,49,58,70,81,85,100,128,142,147,149,152,159,175] },
            "2B": { name: "Depresivo", questions: [21,25,26,44,48,84,87,113,124,134,143,146,149,152,155] },
            "3": { name: "Dependiente", questions: [17,36,46,48,57,74,83,85,95,109,121,134,136,142,152,170] },
            "4": { name: "Histriónico", questions: [11,13,22,25,28,33,49,52,58,70,81,89,93,100,124,128,175] },
            "5": { name: "Narcisista", questions: [6,22,27,32,36,39,41,48,58,68,70,81,85,86,87,89,94,95,100,117,142,145,160,170] },
            "6A": { name: "Antisocial", questions: [8,14,15,18,22,39,42,53,54,94,102,114,123,137,140,167,173] },
            "6B": { name: "Agresivo-sádico", questions: [8,10,14,15,18,29,34,37,40,42,50,54,65,80,88,94,96,97,117,167] },
            "7": { name: "Compulsivo", questions: [3,8,15,23,30,42,54,60,73,83,98,102,115,138,140,167,173] },
            "8A": { name: "Negativista (pasivo-agresivo)", questions: [7,8,16,23,37,43,51,61,80,84,99,116,123,127,134,167] },
            "8B": { name: "Autodestructiva", questions: [19,20,25,26,36,41,44,71,91,99,105,123,149,162,170] },

            // Patología severa de personalidad
            "S": { name: "Esquizotípica", questions: [9,49,70,72,77,100,103,118,135,139,142,149,152,157,159,163] },
            "C": { name: "Límite", questions: [8,23,31,42,73,84,99,121,123,135,136,143,155,162,172,167] },
            "P": { name: "Paranoide", questions: [7,9,34,43,49,50,61,64,90,104,116,139,147,159,160,168,176] },

            // Síndromes clínicos
            "A": { name: "Trastornos de Ansiedad", questions: [41,59,62,76,77,109,110,125,136,146,148,150,165,171] },
            "H": { name: "Trastorno Somatoformo", questions: [2,5,12,38,56,75,76,108,112,131,146,149] },
            "N": { name: "Trastorno Bipolar", questions: [4,23,42,52,55,84,97,107,118,126,135,167,171] },
            "D": { name: "Trastorno Distímico", questions: [16,25,26,56,63,84,87,105,112,131,142,143,149] },
            "B": { name: "Dependencia del alcohol", questions: [15,24,42,53,65,78,94,101,102,114,123,132,140,153,167] },
            "T": { name: "Dependencia de sustancias", questions: [8,14,22,39,40,42,54,67,92,102,114,119,137,140] },
            "R": { name: "Trastorno estrés postraumático", questions: [63,77,84,110,124,130,134,143,148,149,150,152,155,161,165,174] },

            // Síndromes clínicos graves
            "SS": { name: "Desorden del pensamiento", questions: [23,35,57,62,69,73,77,79,84,103,118,135,143,149,152,163,169] },
            "CC": { name: "Depresión mayor", questions: [2,5,35,45,56,75,105,108,112,129,131,143,149,150,151,155,172] },
            "PP": { name: "Desorden delusional", questions: [6,39,50,64,68,90,104,120,139,141,154,160,176] },

            // Escalas Modificadores
            "X": { name: "Sinceridad", questions: [] },
            "Y": { name: "Deseabilidad Social", questions: [21,33,36,41,52,58,60,70,81,83,89,98,105,113,124,138,142,149,152,173] },
            "Z": { name: "Devaluación", questions: [2,5,9,16,23,25,31,35,37,45,56,57,59,63,64,71,75,76,77,84,85,87,100,112,124,129,134,135,143,146,151,172] },
            "V": { name: "Validez", questions: [66,111,158] }
        };

        // Tablas de conversión corregidas según los resultados exactos del Excel
        const conversionTable = {
            "1": { 0: 0, 1: 8, 2: 15, 3: 23, 4: 30, 5: 38, 6: 45, 7: 53, 8: 60, 9: 63, 10: 65, 11: 68, 12: 70, 13: 73, 14: 75, 15: 77, 16: 79, 17: 81, 18: 83, 19: 85, 20: 100, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "2A": { 0: 0, 1: 9, 2: 17, 3: 26, 4: 34, 5: 43, 6: 51, 7: 60, 8: 62, 9: 64, 10: 66, 11: 68, 12: 69, 13: 71, 14: 73, 15: 75, 16: 77, 17: 79, 18: 81, 19: 83, 20: 85, 21: 93, 22: 100, 23: 108, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "2B": { 0: 0, 1: 8, 2: 15, 3: 23, 4: 30, 5: 38, 6: 45, 7: 53, 8: 60, 9: 64, 10: 68, 11: 71, 12: 75, 13: 76, 14: 77, 15: 78, 16: 79, 17: 81, 18: 82, 19: 83, 20: 84, 21: 85, 22: 100, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "3": { 0: 0, 1: 7, 2: 13, 3: 20, 4: 27, 5: 33, 6: 40, 7: 47, 8: 53, 9: 60, 10: 62, 11: 64, 12: 66, 13: 69, 14: 71, 15: 73, 16: 75, 17: 77, 18: 79, 19: 81, 20: 83, 21: 85, 22: 100, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "4": { 0: 0, 1: 3, 2: 6, 3: 9, 4: 12, 5: 15, 6: 18, 7: 21, 8: 24, 9: 27, 10: 30, 11: 33, 12: 36, 13: 39, 14: 42, 15: 45, 16: 48, 17: 51, 18: 54, 19: 57, 20: 60, 21: 68, 22: 75, 23: 80, 24: 85, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "5": { 0: 0, 1: 6, 2: 12, 3: 18, 4: 24, 5: 30, 6: 36, 7: 42, 8: 48, 9: 54, 10: 60, 11: 62, 12: 63, 13: 65, 14: 67, 15: 68, 16: 70, 17: 72, 18: 73, 19: 75, 20: 78, 21: 82, 22: 85, 23: 91, 24: 97, 25: 103, 26: 109, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "6A": { 0: 0, 1: 9, 2: 17, 3: 26, 4: 34, 5: 43, 6: 51, 7: 60, 8: 62, 9: 63, 10: 65, 11: 67, 12: 68, 13: 70, 14: 72, 15: 73, 16: 75, 17: 78, 18: 80, 19: 83, 20: 85, 21: 95, 22: 105, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "6B": { 0: 0, 1: 7, 2: 15, 3: 22, 4: 30, 5: 38, 6: 45, 7: 52, 8: 60, 9: 62, 10: 64, 11: 65, 12: 66, 13: 67, 14: 68, 15: 69, 16: 70, 17: 71, 18: 72, 19: 73, 20: 74, 21: 75, 22: 80, 23: 85, 24: 100, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "7": { 0: 0, 1: 3, 2: 6, 3: 9, 4: 13, 5: 16, 6: 19, 7: 22, 8: 25, 9: 28, 10: 32, 11: 35, 12: 38, 13: 41, 14: 44, 15: 47, 16: 51, 17: 54, 18: 57, 19: 60, 20: 68, 21: 75, 22: 78, 23: 80, 24: 83, 25: 85, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "8A": { 0: 0, 1: 7, 2: 13, 3: 20, 4: 27, 5: 33, 6: 40, 7: 47, 8: 53, 9: 60, 10: 62, 11: 63, 12: 65, 13: 66, 14: 68, 15: 69, 16: 71, 17: 72, 18: 74, 19: 75, 20: 78, 21: 80, 22: 83, 23: 85, 24: 100, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "8B": { 0: 0, 1: 12, 2: 24, 3: 36, 4: 48, 5: 60, 6: 62, 7: 63, 8: 64, 9: 65, 10: 66, 11: 67, 12: 68, 13: 69, 14: 70, 15: 71, 16: 72, 17: 73, 18: 74, 19: 75, 20: 85, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "S": { 0: 0, 1: 12, 2: 24, 3: 36, 4: 48, 5: 60, 6: 62, 7: 63, 8: 64, 9: 65, 10: 66, 11: 67, 12: 68, 13: 69, 14: 70, 15: 71, 16: 72, 17: 73, 18: 73, 19: 74, 20: 75, 21: 85, 22: 93, 23: 101, 24: 108, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "C": { 0: 0, 1: 9, 2: 17, 3: 26, 4: 34, 5: 43, 6: 51, 7: 60, 8: 62, 9: 63, 10: 65, 11: 67, 12: 68, 13: 70, 14: 72, 15: 73, 16: 75, 17: 76, 18: 78, 19: 79, 20: 81, 21: 82, 22: 84, 23: 85, 24: 100, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "P": { 0: 0, 1: 10, 2: 20, 3: 30, 4: 40, 5: 50, 6: 60, 7: 62, 8: 64, 9: 66, 10: 68, 11: 69, 12: 71, 13: 73, 14: 75, 15: 76, 16: 78, 17: 79, 18: 80, 19: 81, 20: 83, 21: 84, 22: 85, 23: 93, 24: 100, 25: 108, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "A": { 0: 0, 1: 15, 2: 30, 3: 45, 4: 60, 5: 68, 6: 75, 7: 78, 8: 80, 9: 83, 10: 85, 11: 88, 12: 91, 13: 94, 14: 97, 15: 100, 16: 103, 17: 106, 18: 109, 19: 112, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "H": { 0: 0, 1: 17, 2: 31, 3: 46, 4: 60, 5: 62, 6: 64, 7: 65, 8: 68, 9: 69, 10: 71, 11: 73, 12: 75, 13: 80, 14: 85, 15: 100, 16: 115, 17: 115, 18: 115, 19: 115, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "N": { 0: 0, 1: 10, 2: 20, 3: 30, 4: 40, 5: 50, 6: 60, 7: 63, 8: 66, 9: 69, 10: 72, 11: 75, 12: 78, 13: 82, 14: 85, 15: 93, 16: 100, 17: 108, 18: 115, 19: 115, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "D": { 0: 0, 1: 10, 2: 20, 3: 30, 4: 40, 5: 50, 6: 60, 7: 62, 8: 64, 9: 66, 10: 68, 11: 70, 12: 71, 13: 73, 14: 74, 15: 75, 16: 80, 17: 85, 18: 95, 19: 105, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "B": { 0: 0, 1: 15, 2: 30, 3: 45, 4: 60, 5: 64, 6: 68, 7: 71, 8: 75, 9: 77, 10: 78, 11: 80, 12: 82, 13: 83, 14: 85, 15: 91, 16: 97, 17: 103, 18: 109, 19: 115, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "T": { 0: 0, 1: 15, 2: 30, 3: 45, 4: 60, 5: 63, 6: 65, 7: 68, 8: 70, 9: 73, 10: 75, 11: 77, 12: 79, 13: 81, 14: 83, 15: 85, 16: 91, 17: 97, 18: 103, 19: 109, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "R": { 0: 0, 1: 12, 2: 24, 3: 36, 4: 48, 5: 60, 6: 62, 7: 64, 8: 65, 9: 66, 10: 67, 11: 68, 12: 69, 13: 70, 14: 71, 15: 72, 16: 73, 17: 74, 18: 75, 19: 85, 20: 100, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "SS": { 0: 0, 1: 9, 2: 17, 3: 26, 4: 34, 5: 43, 6: 51, 7: 60, 8: 64, 9: 68, 10: 71, 11: 75, 12: 78, 13: 82, 14: 85, 15: 88, 16: 92, 17: 95, 18: 98, 19: 102, 20: 105, 21: 108, 22: 112, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "CC": { 0: 0, 1: 12, 2: 24, 3: 36, 4: 48, 5: 60, 6: 62, 7: 63, 8: 65, 9: 67, 10: 68, 11: 70, 12: 72, 13: 73, 14: 75, 15: 80, 16: 85, 17: 89, 18: 94, 19: 98, 20: 102, 21: 106, 22: 111, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "PP": { 0: 0, 1: 60, 2: 64, 3: 68, 4: 71, 5: 75, 6: 77, 7: 78, 8: 80, 9: 82, 10: 83, 11: 85, 12: 90, 13: 95, 14: 100, 15: 105, 16: 110, 17: 115, 18: 115, 19: 115, 20: 115, 21: 115, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "X": { 30: 0, 31: 0, 32: 0, 33: 0, 34: 0, 35: 0, 36: 0, 37: 0, 38: 0, 39: 1, 40: 2, 41: 3, 42: 4, 43: 5, 44: 6, 45: 7, 46: 8, 47: 10, 48: 11, 49: 12, 50: 13, 51: 14, 52: 15, 53: 16, 54: 17, 55: 18, 56: 19, 57: 20, 58: 21, 59: 22, 60: 23, 61: 24, 62: 25, 63: 27, 64: 28, 65: 29, 66: 30, 67: 31, 68: 32, 69: 33, 70: 34, 71: 35, 72: 36, 73: 36, 74: 37, 75: 38, 76: 38, 77: 39, 78: 40, 79: 41, 80: 41, 81: 42, 82: 43, 83: 43, 84: 44, 85: 45, 86: 45, 87: 46, 88: 47, 89: 47, 90: 48, 91: 49, 92: 49, 93: 50, 94: 51, 95: 52, 96: 52, 97: 53, 98: 54, 99: 54, 100: 55, 101: 56, 102: 56, 103: 57, 104: 58, 105: 58, 106: 59, 107: 60, 108: 61, 109: 61, 110: 62, 111: 63, 112: 63, 113: 64, 114: 65, 115: 65, 116: 66, 117: 67, 118: 67, 119: 68, 120: 69, 121: 69, 122: 70, 123: 71, 124: 72, 125: 72, 126: 73, 127: 74, 128: 74, 129: 75, 130: 75, 131: 76, 132: 76, 133: 77, 134: 77, 135: 78, 136: 78, 137: 79, 138: 79, 139: 80, 140: 80, 141: 81, 142: 81, 143: 82, 144: 82, 145: 83, 146: 83, 147: 84, 148: 84, 149: 85, 150: 85, 151: 86, 152: 86, 153: 87, 154: 87, 155: 88, 156: 88, 157: 89, 158: 89, 159: 89, 160: 89, 161: 90, 162: 90, 163: 91, 164: 91, 165: 92, 166: 92, 167: 93, 168: 93, 169: 93, 170: 94, 171: 94, 172: 95, 173: 95, 174: 96, 175: 96, 176: 96, 177: 97, 178: 97, 179: 98, 180: 98, 181: 99, 182: 99, 183: 100, 184: 100, 185: 100, 186: 100, 187: 100, 188: 100, 189: 100, 190: 100, 191: 100, 192: 100, 193: 100, 194: 100, 195: 100, 196: 100, 197: 100, 198: 100, 199: 100, 200: 100, 201: 100, 202: 100, 203: 100, 204: 100, 205: 100, 206: 100, 207: 100, 208: 100, 209: 100, 210: 100, 211: 100, 212: 100, 213: 100, 214: 100, 215: 100, 216: 100, 217: 100, 218: 100, 219: 100, 220: 100, 221: 100, 222: 100, 223: 100, 224: 100, 225: 100, 226: 100, 227: 100, 228: 100, 229: 100, 230: 100, 231: 100, 232: 100, 233: 100, 234: 100, 235: 100, 236: 100, 237: 100, 238: 100, 239: 100, 240: 100, 241: 100, 242: 100, 243: 100, 244: 100, 245: 100, 246: 100, 247: 100, 248: 100, 249: 100, 250: 100, 251: 100, 252: 100, 253: 100, 254: 100, 255: 100, 256: 100, 257: 100, 258: 100, 259: 100, 260: 100, 261: 100, 262: 100, 263: 100, 264: 100, 265: 100, 266: 100, 267: 100, 268: 100, 269: 100, 270: 100, 271: 100, 272: 100, 273: 100, 274: 100, 275: 100, 276: 100, 277: 100, 278: 100, 279: 100, 280: 100, 281: 100, 282: 100, 283: 100, 284: 100, 285: 100, 286: 100, 287: 100, 288: 100 },
            "Y": { 0: 0, 1: 0, 2: 0, 3: 7, 4: 14, 5: 21, 6: 28, 7: 35, 8: 39, 9: 44, 10: 48, 11: 53, 12: 57, 13: 62, 14: 66, 15: 71, 16: 75, 17: 80, 18: 85, 19: 90, 20: 95, 21: 100, 22: 115, 23: 115, 24: 115, 25: 115, 26: 115, 27: 115, 28: 115, 29: 115, 30: 115, 31: 115, 32: 115, 33: 115 },
            "Z": { 0: 0, 1: 35, 2: 37, 3: 40, 4: 42, 5: 44, 6: 47, 7: 49, 8: 51, 9: 54, 10: 56, 11: 59, 12: 61, 13: 63, 14: 66, 15: 68, 16: 70, 17: 73, 18: 75, 19: 77, 20: 79, 21: 81, 22: 83, 23: 85, 24: 87, 25: 89, 26: 91, 27: 93, 28: 95, 29: 97, 30: 99, 31: 100, 32: 100, 33: 100 },
            "V": { 0: 0, 1: 0, 2: 0, 3: 3 }
        };
        
        async function loadReport() {
            try {
                console.log('Cargando reporte con ID:', resultId);
                
                const response = await fetch('../php/get-report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_report',
                        result_id: resultId
                    })
                });
                
                const responseText = await response.text();
                console.log('Respuesta del servidor:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    throw new Error('El servidor devolvió una respuesta no válida');
                }
                
                if (data.success) {
                    console.log('Datos recibidos del servidor:', data.result);
                    data.result = recalculateBRIfNeeded(data.result);
                    
                    console.log('Datos después del recálculo BR:');
                    const escalas = ['1', '2a', '2b', '3', '4', '5', '6a', '6b', '7', '8a', '8b', 's', 'c', 'p'];
                    escalas.forEach(scale => {
                        console.log(`Escala ${scale}: Raw=${data.result[`raw_${scale}`]}, BR=${data.result[`br_${scale}`]}`);
                    });
                    
                    displayReport(data);
                } else {
                    showError(data.message || 'Error al cargar el reporte');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Error de conexión al cargar el reporte: ' + error.message);
            }
        }
        
        async function getPatientIdFromSession() {
            try {
                const response = await fetch('../php/check-patient-session.php');
                const data = await response.json();
                
                console.log('Datos de sesión:', data);
                
                if (data.logged_in && data.patient) {
                    return data.patient.id;
                }
                
                console.warn('No se encontró sesión de paciente válida');
                return null;
                
            } catch (error) {
                console.error('Error obteniendo sesión del paciente:', error);
                return null;
            }
        }
        
        function displayPatientInfo(patient) {
            const patientInfo = document.getElementById('patient-info');
            patientInfo.innerHTML = `
                <div class="info-item">
                    <strong>Entrevistado</strong>
                    ${patient.nombres} ${patient.apellidos}
                </div>
                <div class="info-item">
                    <strong>Cédula</strong>
                    ${patient.cedula}
                </div>
                <div class="info-item">
                    <strong>Fecha de Evaluación</strong>
                    <span id="evaluation-date">Cargando...</span>
                </div>
                <div class="info-item">
                    <strong>ID del Resultado</strong>
                    #${resultId}
                </div>
            `;
        }
        
        function displayReport(data) {
            const reportContent = document.getElementById('report-content');
            const result = data.result;
            
            displayPatientInfo(data.patient);
            document.getElementById('evaluation-date').textContent = 
                new Date(result.evaluation_date).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            
            reportContent.innerHTML = `
                <div class="results-grid">
                    <!-- Tabla de Resultados -->
                    <div class="results-table">
                        <h3><i class="fas fa-table"></i> Puntuaciones de las Escalas</h3>
                        <div class="scale-row header">
                            <div>Escala</div>
                            <div>Nombre</div>
                            <div>Raw</div>
                            <div>BR</div>
                            <div>Interpretación</div>
                            <div>Información</div>
                        </div>
                        <div id="scales-container">
                            ${generateScalesHTML(result)}
                        </div>
                    </div>
                    
                    <!-- Gráfico -->
                    <div class="chart-container">
                        <h3 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">
                            <i class="fas fa-chart-bar"></i> Perfil de Resultados
                        </h3>
                        <canvas id="resultsChart" height="400"></canvas>
                        <button class="btn-primary" style="margin: 20px auto; display: block;" onclick="downloadPDF()">
                            <i class="fas fa-file-pdf"></i> Observaciones Clínicas
                        </button>
                    </div>
                </div>
                
                <!-- Interpretación -->
                <div class="interpretation-section">
                    <h3><i class="fas fa-comment-medical"></i> Interpretación de Puntuaciones Base (BR)</h3>
                    <div class="interpretation-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Rango BR</th>
                                    <th>Interpretación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="elevated">
                                    <td><strong>Indicador Elevado</strong></td>
                                    <td>85 - 115</td>
                                    <td>Presencia muy probable del rasgo o síndrome</td>
                                </tr>
                                <tr class="prominent">
                                    <td><strong>Indicador Prominente</strong></td>
                                    <td>75 - 84</td>
                                    <td>Presencia probable del rasgo o síndrome</td>
                                </tr>
                                <tr class="present">
                                    <td><strong>Indicador Presente</strong></td>
                                    <td>60 - 74</td>
                                    <td>Presencia de rasgos o síntomas del síndrome</td>
                                </tr>
                                <tr class="low">
                                    <td><strong>Indicador Bajo</strong></td>
                                    <td>35 - 59</td>
                                    <td>Pocos rasgos o síntomas presentes</td>
                                </tr>
                                <tr class="absent">
                                    <td><strong>Indicador Ausente</strong></td>
                                    <td>0 - 34</td>
                                    <td>Ausencia del rasgo o síndrome</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Notas Clínicas -->
                ${result.clinical_notes ? `
                <div class="interpretation-section">
                    <h3><i class="fas fa-sticky-note"></i> Notas Clínicas</h3>
                    <p>${result.clinical_notes}</p>
                </div>
                ` : ''}
                
                <!-- Recomendaciones -->
                ${result.recommendations ? `
                <div class="interpretation-section">
                    <h3><i class="fas fa-lightbulb"></i> Recomendaciones</h3>
                    <p>${result.recommendations}</p>
                </div>
                ` : ''}
            `;
            
            generateChart(result);
        }
        
        function generateScalesHTML(result) {
            const scales = [
                { id: '1', name: 'Esquizoide' },
                { id: '2A', name: 'Evitativo', dbId: '2a' },
                { id: '2B', name: 'Depresivo', dbId: '2b' },
                { id: '3', name: 'Dependiente' },
                { id: '4', name: 'Histriónico' },
                { id: '5', name: 'Narcisista' },
                { id: '6A', name: 'Antisocial', dbId: '6a' },
                { id: '6B', name: 'Agresivo/Sádico', dbId: '6b' },
                { id: '7', name: 'Compulsivo' },
                { id: '8A', name: 'Pasivo/Agresivo', dbId: '8a' },
                { id: '8B', name: 'Autodestructivo', dbId: '8b' },
                { id: 'S', name: 'Esquizotípico', dbId: 's' },
                { id: 'C', name: 'Borderline', dbId: 'c' },
                { id: 'P', name: 'Paranoide', dbId: 'p' },
                { id: 'A', name: 'Trastornos de Ansiedad', dbId: 'a' },
                { id: 'H', name: 'Trastorno Somatoformo', dbId: 'h' },
                { id: 'N', name: 'Trastorno Bipolar', dbId: 'n' },
                { id: 'D', name: 'Trastorno Distímico', dbId: 'd' },
                { id: 'B', name: 'Dependencia del alcohol', dbId: 'b' },
                { id: 'T', name: 'Dependencia de sustancias', dbId: 't' },
                { id: 'R', name: 'Trastorno estrés postraumático', dbId: 'r' },
                { id: 'SS', name: 'Desorden del pensamiento', dbId: 'ss' },
                { id: 'CC', name: 'Depresión mayor', dbId: 'cc' },
                { id: 'PP', name: 'Desorden delusional', dbId: 'pp' },
                { id: 'X', name: 'Sinceridad', dbId: 'x' },
                { id: 'Y', name: 'Deseabilidad Social', dbId: 'y' },
                { id: 'Z', name: 'Devaluación', dbId: 'z' },
                { id: 'V', name: 'Validez', dbId: 'v' }

            ];
            
            return scales.map(scale => {
                const dbScaleId = scale.dbId || scale.id;
                const rawScore = result[`raw_${dbScaleId}`] || 0;
                const brScore = result[`br_${dbScaleId}`] || 0;
                const category = getCategory(brScore);
                const categoryName = getCategoryName(brScore);
                
                return `
                    <div class="scale-row">
                        <div style="font-weight: bold;">${scale.id}</div>
                        <div>${scale.name}</div>
                        <div>${rawScore}</div>
                        <div>${brScore}</div>
                        <div>
                            <div class="br-indicator">
                                <div class="br-fill ${category}" style="width: ${Math.min(brScore, 100)}%"></div>
                            </div>
                            <small>${categoryName}</small>
                        </div>
                        <div>
                            <button class="btn-info" onclick="openScaleInfo('${scale.id}')">
                                <i class="fas fa-info-circle"></i> Detalles
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        function recalculateBRIfNeeded(result) {
            const scalesToCheck = ['1', '2a', '2b', '3', '4', '5', '6a', '6b', '7', '8a', '8b', 's', 'c', 'p'];
            const allZero = scalesToCheck.every(scale => result[`br_${scale}`] === 0);
            
            if (allZero) {
                console.log('Recalculando puntuaciones BR desde raw scores...');
                
                scalesToCheck.forEach(scale => {
                    const rawScore = result[`raw_${scale}`] || 0;
                    let scaleId = scale.toUpperCase();
                    if (scale === '2a') scaleId = '2A';
                    if (scale === '2b') scaleId = '2B';
                    if (scale === '6a') scaleId = '6A';
                    if (scale === '6b') scaleId = '6B';
                    if (scale === '8a') scaleId = '8A';
                    if (scale === '8b') scaleId = '8B';
                    
                    try {
                        const brScore = interpolateValue(rawScore, scaleId);
                        result[`br_${scale}`] = Math.max(0, Math.min(115, brScore));
                        console.log(`Escala ${scaleId}: Raw=${rawScore} → BR=${result[`br_${scale}`]}`);
                    } catch (error) {
                        console.error(`Error calculando BR para escala ${scaleId}:`, error);
                        result[`br_${scale}`] = 0;
                    }
                });
                
                const personalidadScales = ["1", "2a", "2b", "3", "4", "5", "6a", "6b", "7", "8a", "8b"];
                let rawX = 0;
                personalidadScales.forEach(sid => {
                    rawX += result[`raw_${sid}`] || 0;
                });
                
                try {
                    const brX = interpolateValue(rawX, "X");
                    result['br_x'] = Math.max(0, Math.min(115, brX));
                    result['raw_x'] = rawX;
                    console.log(`Escala X: Raw=${rawX} → BR=${result['br_x']}`);
                } catch (error) {
                    console.error('Error calculando BR para escala X:', error);
                }
                
                const otrasEscalas = ['a', 'h', 'n', 'd', 'b', 't', 'r', 'ss', 'cc', 'pp', 'y', 'z', 'v'];
                otrasEscalas.forEach(scale => {
                    const rawScore = result[`raw_${scale}`] || 0;
                    const scaleId = scale.toUpperCase();
                    
                    try {
                        const brScore = interpolateValue(rawScore, scaleId);
                        result[`br_${scale}`] = Math.max(0, Math.min(115, brScore));
                        console.log(`Escala ${scaleId}: Raw=${rawScore} → BR=${result[`br_${scale}`]}`);
                    } catch (error) {
                        console.error(`Error calculando BR para escala ${scaleId}:`, error);
                    }
                });
            }
            
            return result;
        }
        
        function interpolateValue(score, scaleId) {
            const table = conversionTable[scaleId];
            if (!table) {
                console.error(`Tabla de conversión no encontrada para la escala ${scaleId}`);
                return 0;
            }

            const scores = Object.keys(table).map(Number).sort((a, b) => a - b);
            const min = scores[0];
            const max = scores[scores.length - 1];

            if (score <= min) return table[min];
            if (score >= max) return table[max];

            let lower = min;
            let upper = max;

            for (const s of scores) {
                if (s <= score && s > lower) lower = s;
                if (s >= score && s < upper) upper = s;
            }

            const lowerValue = table[lower];
            const upperValue = table[upper];
            let interpolatedValue;
            
            if (lower === upper) {
                interpolatedValue = lowerValue;
            } else {
                const ratio = (score - lower) / (upper - lower);
                interpolatedValue = lowerValue + ratio * (upperValue - lowerValue);
            }
            
            return Math.round(interpolatedValue);
        }
        
        function getCategory(br) {
            if (br >= 85) return 'br-elevated';
            if (br >= 75) return 'br-prominent';
            if (br >= 60) return 'br-present';
            if (br >= 35) return 'br-low';
            return 'br-absent';
        }

        function getCategoryName(br) {
            if (br >= 85) return 'Elevado';
            if (br >= 75) return 'Prominente';
            if (br >= 60) return 'Presente';
            if (br >= 35) return 'Bajo';
            return 'Ausente';
        }
        
        function generateChart(result) {
            const ctx = document.getElementById('resultsChart').getContext('2d');
            
            const scales = [
                { id: '1', dbId: '1', name: 'Esquizoide' },
                { id: '2A', dbId: '2a', name: 'Evitativo' },
                { id: '2B', dbId: '2b', name: 'Depresivo' },
                { id: '3', dbId: '3', name: 'Dependiente' },
                { id: '4', dbId: '4', name: 'Histriónico' },
                { id: '5', dbId: '5', name: 'Narcisista' },
                { id: '6A', dbId: '6a', name: 'Antisocial' },
                { id: '6B', dbId: '6b', name: 'Agresivo/Sádico' },
                { id: '7', dbId: '7', name: 'Compulsivo' },
                { id: '8A', dbId: '8a', name: 'Pasivo/Agresivo' },
                { id: '8B', dbId: '8b', name: 'Autodestructivo' },
                { id: 'S', dbId: 's', name: 'Esquizotípico' },
                { id: 'C', dbId: 'c', name: 'Borderline' },
                { id: 'P', dbId: 'p', name: 'Paranoide' },
                { id: 'A', dbId: 'a', name: 'Trastornos de Ansiedad' },
                { id: 'H', dbId: 'h', name: 'Trastorno Somatoformo' },
                { id: 'N', dbId: 'n', name: 'Trastorno Bipolar' },
                { id: 'D', dbId: 'd', name: 'Trastorno Distímico' },
                { id: 'B', dbId: 'b', name: 'Dependencia del alcohol' },
                { id: 'T', dbId: 't', name: 'Dependencia de sustancias' },
                { id: 'R', dbId: 'r', name: 'Trastorno estrés postraumático' },
                { id: 'SS', dbId: 'ss', name: 'Desorden del pensamiento' },
                { id: 'CC', dbId: 'cc', name: 'Depresión mayor' },
                { id: 'PP', dbId: 'pp', name: 'Desorden delusional' },
                { id: 'X', dbId: 'x', name: 'Sinceridad' },
                { id: 'Y', dbId: 'y', name: 'Deseabilidad Social' },
                { id: 'Z', dbId: 'z', name: 'Devaluación' },
                { id: 'V', dbId: 'v', name: 'Validez' }
            ];
            
            const labels = scales.map(scale => scale.id);
            const brScores = scales.map(scale => result[`br_${scale.dbId}`] || 0);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Puntuación BR',
                        data: brScores,
                        backgroundColor: brScores.map(score => {
                            if (score >= 85) return '#eb5757';
                            if (score >= 75) return '#f2994a';
                            if (score >= 60) return '#f2c94c';
                            if (score >= 35) return '#6fcf97';
                            return '#28a745';
                        }),
                        borderColor: '#2c3e50',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 115,
                            title: {
                                display: true,
                                text: 'Puntuación BR'
                            },
                            grid: {
                                color: '#f8f9fa'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Escalas Clínicas'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const scaleIndex = context.dataIndex;
                                    const scale = scales[scaleIndex];
                                    return `${scale.name}: ${context.parsed.y} BR`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function showError(message) {
            const reportContent = document.getElementById('report-content');
            reportContent.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error al cargar el reporte</h3>
                    <p>${message}</p>
                    <button onclick="loadReport()" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
        
        function printReport() {
            window.print();
        }
        
        document.addEventListener('DOMContentLoaded', loadReport);
    </script>
</body>
</html>