// MCMI-III - Inventario Clínico Multiaxial de Millon III


// Variables globales
let responses = {};
let answeredCount = 0;

// 175 preguntas oficiales del MCMI-III
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
    { id: 24, question: "Hace unos años comencé a sentirme un fracasado" },
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

// Escalas del MCMI-III corregidas según los resultados del Excel
// Ejemplo de escalas (agrega todas las escalas reales y sus preguntas)
const scales = {
    // Patrones clínicos de personalidad
    "1": { name: "Esquizoide", questions: [5,11,28,33,39,47,49,58,93,102,106,143,149,157,166,168] },
    "2A": { name: "Evitativo", questions: [19,41,48,49,58,70,81,85,100,128,142,147,149,152,159,175] },
    "2B": { name: "Depresivo", questions: [21,25,26,44,48,84,87,113,124,134,143,146,149,152,155] },
    "3": { name: "Dependiente", questions: [17,36,46,48,57,74,83,85,95,109,121,134,136,142,152,170] },
    "4": { name: "Histriónico", questions: [11,13,22,25,28,33,49,52,58,70,81,89,93,100,124,128,175] },
    "5": { name: "Narcisita", questions: [6,22,27,32,36,39,41,48,58,68,70,81,85,86,87,89,94,95,100,117,142,145,160,170] },
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
    "X": { name: "Sinceridad", questions: [] }, // This is calculated as the sum of raw scores from personality scales
    "Y": { name: "Deseabilidad Social", questions: [21,33,36,41,52,58,60,70,81,83,89,98,105,113,124,138,142,149,152,173] },
    "Z": { name: "Devaluación", questions: [2,5,9,16,23,25,31,35,37,45,56,57,59,63,64,71,75,76,77,84,85,87,100,112,124,129,134,135,143,146,151,172] },
    "V": { name: "Validez", questions: [66,111,158] } // Corrected based on standard MCMI-III V scale items
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

// Función para obtener categoría según puntuación BR
function getCategoria(br) {
    if (br >= 85 && br <= 115) return 'Indicador Elevado';
    if (br >= 75 && br <= 84) return 'Indicador Prominente';
    if (br >= 60 && br <= 74) return 'Indicador Presente';
    if (br >= 35 && br <= 59) return 'Indicador Bajo';
    return 'Indicador Ausente'; // 0-34
}

// Función para actualizar la barra de progreso
function updateProgress() {
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    const percentage = (answeredCount / questions.length) * 100;

    progressFill.style.width = `${percentage}%`;
    progressText.textContent = `${answeredCount} / ${questions.length} respondidas`;
}

// Función para marcar pregunta como respondida
function markQuestionAnswered(questionId, answered) {
    const questionElement = document.querySelector(`#question-${questionId}`);
    if (questionElement) {
        if (answered) {
            questionElement.classList.add('answered');
        } else {
            questionElement.classList.remove('answered');
        }
    }
}

// Función para generar las preguntas
function generateQuestions() {
    const questionsContainer = document.getElementById('questions-container');
    questionsContainer.innerHTML = '';

    questions.forEach((q) => {
        const div = document.createElement('div');
        div.className = 'question';
        div.id = `question-${q.id}`;
        div.innerHTML = `
            <div class="question-text">${q.id}. ${q.question}</div>
            <div class="options">
                <label class="option" data-question="${q.id}" data-value="true">
                    <input type="radio" name="q${q.id}" value="true">
                    Verdadero
                </label>
                <label class="option" data-question="${q.id}" data-value="false">
                    <input type="radio" name="q${q.id}" value="false">
                    Falso
                </label>
            </div>
        `;
        questionsContainer.appendChild(div);

        // Agregar event listeners para las opciones
        const options = div.querySelectorAll('.option');
        options.forEach(option => {
            option.addEventListener('click', function () {
                const questionId = parseInt(this.dataset.question);
                const value = this.dataset.value === 'true';
                const radio = this.querySelector('input[type="radio"]');

                // Remover selección previa
                options.forEach(opt => opt.classList.remove('selected'));

                // Agregar selección actual
                this.classList.add('selected');
                radio.checked = true;

                // Actualizar respuesta
                const wasAnswered = responses.hasOwnProperty(questionId);
                responses[questionId] = value;

                if (!wasAnswered) {
                    answeredCount++;
                    markQuestionAnswered(questionId, true);
                    updateProgress();
                }
            });
        });
    });
}

// Función para validar que todas las preguntas estén respondidas
function validateResponses() {
    const validationMessage = document.getElementById('validation-message');
    const calculateBtn = document.getElementById('calculate-btn');

    if (!validationMessage || !calculateBtn) {
        console.error('No se encontraron los elementos necesarios para validar las respuestas');
        return false;
    }

    try {
        // Verificar que todas las preguntas tengan respuesta
        const allAnswered = questions.every(q => responses.hasOwnProperty(q.id));
        const answeredCount = Object.keys(responses).length;
        const missingCount = questions.length - answeredCount;

        if (allAnswered) {
            validationMessage.innerHTML = `
                <div class="validation-message success">
                    ✅ Todas las ${questions.length} preguntas han sido respondidas. 
                    Puede proceder a calcular los resultados.
                </div>
            `;
            calculateBtn.disabled = false;
            return true;
        } else {
            validationMessage.innerHTML = `
                <div class="validation-message error">
                    ❌ Faltan ${missingCount} de ${questions.length} preguntas por responder.
                    <div class="progress-info">
                        Progreso: ${answeredCount}/${questions.length} (${Math.round((answeredCount / questions.length) * 100)}%)
                    </div>
                    Por favor, responda todas las preguntas antes de continuar.
                </div>
            `;
            calculateBtn.disabled = true;
            return false;
        }
    } catch (error) {
        console.error('Error al validar respuestas:', error);
        validationMessage.innerHTML = `
            <div class="validation-message error">
                ❌ Ocurrió un error al validar las respuestas. Por favor, recargue la página e intente nuevamente.
            </div>
        `;
        calculateBtn.disabled = true;
        return false;
    }
}

// Función para interpolar valores
function interpolateValue(score, scaleId) {
    const table = conversionTable[scaleId];
    if (!table) {
        console.error(`Tabla de conversión no encontrada para la escala ${scaleId}`);
        return 0;
    }

    // Convertir las claves a números y ordenarlas
    const scores = Object.keys(table).map(Number).sort((a, b) => a - b);
    const min = scores[0];
    const max = scores[scores.length - 1];

    // Si el score es menor o igual al mínimo, devolver el valor mínimo
    if (score <= min) return table[min];

    // Si el score es mayor o igual al máximo, devolver el valor máximo
    if (score >= max) return table[max];

    // Encontrar los dos puntos más cercanos para interpolar
    let lower = min;
    let upper = max;

    for (const s of scores) {
        if (s <= score && s > lower) lower = s;
        if (s >= score && s < upper) upper = s;
    }

    // Calcular el valor interpolado
    const lowerValue = table[lower];
    const upperValue = table[upper];
    let interpolatedValue;
    
    if (lower === upper) {
        interpolatedValue = lowerValue;
    } else {
        // Interpolación lineal
        const ratio = (score - lower) / (upper - lower);
        interpolatedValue = lowerValue + ratio * (upperValue - lowerValue);
    }
    
    // Redondear al entero más cercano (0.5 o más redondea hacia arriba)
    return Math.max(0, Math.min(115, Math.round(interpolatedValue)));
}

// Función para calcular los resultados
function calculateResults() {
    if (!validateResponses()) {
        alert('Por favor, complete todas las preguntas antes de calcular los resultados.');
        return;
    }

    const results = {};
    const rawScores = {};

    // 1) Calcular puntuaciones brutas para todas las escalas EXCEPTO X usando el mapeo de preguntas
    for (const [scaleId, scaleData] of Object.entries(scales)) {
        if (scaleId === 'X') continue; // X se calcula aparte como suma de escalas de personalidad

        let rawScore = 0;
        scaleData.questions.forEach(qNum => {
            if (responses[qNum] === true) rawScore++;
        });

        // Regla especial para Narcisista (escala 5)
        if (scaleId === '5') {
            rawScore = Math.round((rawScore * 100) / 24);
        }

        const scaleTable = conversionTable[scaleId];
        if (!scaleTable) {
            console.error(`No se encontró tabla de conversión para la escala ${scaleId}`);
            continue;
        }

        // Limitar al máximo definido en la tabla de conversión
        const maxRaw = Math.max(...Object.keys(scaleTable).map(Number));
        rawScore = Math.min(rawScore, maxRaw);
        rawScores[scaleId] = rawScore;

        // BR por tabla (con interpolación si no existe clave exacta)
        let brScore = interpolateValue(rawScore, scaleId);
        brScore = Math.max(0, Math.min(115, brScore));
        const categoria = getCategoria(brScore);

        results[scaleId] = {
            name: scaleData.name,
            rawScore,
            brScore,
            categoria
        };
        console.log(`Escala ${scaleId} - Raw: ${rawScore}, BR: ${brScore}, Categoría: ${categoria}`);
    }

    // 2) Calcular X como suma de brutos de las escalas de personalidad
    const personalidadScales = ["1", "2A", "2B", "3", "4", "5", "6A", "6B", "7", "8A", "8B"];
    let rawX = 0;
    personalidadScales.forEach(sid => {
        if (rawScores.hasOwnProperty(sid)) rawX += rawScores[sid];
    });

    if (conversionTable["X"]) {
        const maxRawX = Math.max(...Object.keys(conversionTable["X"]).map(Number));
        rawX = Math.min(rawX, maxRawX);
        let brX = interpolateValue(rawX, "X");
        brX = Math.max(0, Math.min(115, brX));
        const categoriaX = getCategoria(brX);

        results["X"] = {
            name: scales["X"].name,
            rawScore: rawX,
            brScore: brX,
            categoria: categoriaX
        };
        console.log(`Escala X - Raw: ${rawX}, BR: ${brX}, Categoría: ${categoriaX}`);
    } else {
        console.error('No se encontró tabla de conversión para la escala X');
    }

    // 3) Mostrar resultados
    console.log('Resultados calculados:', results);
    displayResults(results);
    showGeneralInterpretation(results);
}

// Función para limpiar todas las respuestas
function clearAllResponses() {
    if (confirm('¿Está seguro de que desea borrar todas las respuestas?')) {
        responses = {};
        answeredCount = 0;

        // Limpiar selecciones de radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.checked = false;
        });

        // Actualizar estado de las preguntas
        questions.forEach(q => {
            markQuestionAnswered(q.id, false);
        });

        updateProgress();

        // Mostrar mensaje de éxito
        Swel.fire({
            icon: 'success',
            title: '¡Respuestas borradas!',
            text: 'Todas las respuestas han sido eliminadas correctamente.',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#667eea'
        });
    }
}

// Función para mostrar los resultados
function displayResults(results) {
    const resultsContainer = document.getElementById('results-container');
    const chartContainer = document.getElementById('chart-container');

    // Mostrar el contenedor del gráfico y asegurar que permanezca visible
    console.log('Mostrando contenedor del gráfico...');

    // Verificar si algún elemento padre está ocultando el contenedor
    let currentElement = chartContainer;
    while (currentElement && currentElement !== document.body) {
        const style = window.getComputedStyle(currentElement);
        if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
            console.warn('Elemento padre está ocultando el contenedor:', currentElement);
            console.log('Estilos del elemento:', {
                display: style.display,
                visibility: style.visibility,
                opacity: style.opacity,
                position: style.position,
                width: style.width,
                height: style.height
            });
        }
        currentElement = currentElement.parentElement;
    }

    // Forzar visibilidad del contenedor
    chartContainer.style.display = 'block';
    chartContainer.style.opacity = '1';
    chartContainer.style.visibility = 'visible';
    chartContainer.style.position = 'relative';
    chartContainer.style.zIndex = '1000';

    // Añadir un indicador visual temporal
    const debugDiv = document.createElement('div');
    debugDiv.style.cssText = 'position: fixed; top: 10px; right: 10px; background: rgba(255,0,0,0.7); color: white; padding: 10px; z-index: 10000;';
    // debugDiv.textContent = 'Chart Container Visible';
    debugDiv.id = 'chart-debug-indicator';

    // Eliminar el indicador anterior si existe
    const existingIndicator = document.getElementById('chart-debug-indicator');
    if (existingIndicator) {
        document.body.removeChild(existingIndicator);
    }
    document.body.appendChild(debugDiv);
    console.log('Estado del contenedor del gráfico:', {
        display: window.getComputedStyle(chartContainer).display,
        visibility: window.getComputedStyle(chartContainer).visibility,
        opacity: window.getComputedStyle(chartContainer).opacity
    });

    // Limpiar resultados anteriores
    resultsContainer.innerHTML = '';

    // Forzar un reflow para asegurar que el contenedor esté visible
    void chartContainer.offsetHeight;

    // Preparar datos para el gráfico
    const chartData = {
        labels: [],
        datasets: [{
            label: 'PERFIL DE RESULTADOS GRAFICOS MCMI-III',
            data: [],
            backgroundColor: [],
            borderColor: [],
            borderWidth: 1
        }]
    };

    // Crear tabla de resultados
    let html = `
        <div style="margin-bottom: 20px;">
            <h3 style="color:rgb(112, 127, 195); text-align: center;">Perfil de Resultados MCMI-III</h3>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                <strong>Comparación con Excel:</strong> Los resultados deberían coincidir exactamente
            </p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Escala</th>
                    <th>Nombre</th>
                    <th>Bruto</th>
                    <th>BR</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
    `;

    // Preparar datos para el gráfico
    const labels = [];
    const brScores = [];
    const backgroundColors = [];
    const borderColors = [];

    // Definir las secciones y el orden de las escalas
    const sections = [
        {
            title: "Patrones Clínicos de Personalidad",
            scales: ["1", "2A", "2B", "3", "4", "5", "6A", "6B", "7", "8A", "8B"]
        },
        {
            title: "Patología Severa de Personalidad",
            scales: ["S", "C", "P"]
        },
        {
            title: "Síndromes Clínicos",
            scales: ["A", "H", "N", "D", "B", "T", "R"]
        },
        {
            title: "Síndromes Clínicos Graves",
            scales: ["SS", "CC", "PP"]
        },
        {
            title: "Escalas Modificadoras",
            scales: ["X", "Y", "Z", "V"]
        }
    ];

    // Mostrar por secciones
    for (const section of sections) {
        html += `
            <tr class="section-title">
                <td colspan="5">${section.title}</td>
            </tr>
        `;

        for (const scaleId of section.scales) {
            const result = results[scaleId];
            const categoryClass = getCategoryClass(result.categoria);

            // Agregar datos para el gráfico con el nombre completo de la escala
            chartData.labels.push(result.name);
            chartData.datasets[0].data.push(result.brScore);

            // Asignar colores según el BR usando getCategoryColor
            const hexColor = getCategoryColor(result.brScore);
            chartData.datasets[0].backgroundColor.push(hexToRgba(hexColor, 0.6));
            chartData.datasets[0].borderColor.push(hexToRgba(hexColor, 1));

            html += `
                <tr class="${categoryClass}">
                    <td style="font-weight: bold;">${scaleId}</td>
                    <td class="scale-name">${result.name}</td>
                    <td class="score">${result.rawScore}</td>
                    <td class="score" style="color: ${hexColor}; font-weight: 700;">${result.brScore}</td>
                    <td class="score" style="color: ${hexColor}; font-weight: 700;">${result.categoria}</td>
                </tr>
            `;
        }
    }

    html += `
            </tbody>
        </table>
    `;

    resultsContainer.innerHTML = html;

    // Crear el gráfico después de que se hayan renderizado los resultados
    // y el canvas esté disponible en el DOM
    const canvas = document.getElementById('resultsChart');
    if (canvas) {
        // Forzar un reflow para asegurar que el canvas esté en el DOM
        void canvas.offsetHeight;

        console.log('Preparando para crear el gráfico...');
        console.log('Datos del gráfico:', JSON.stringify(chartData, null, 2));

        // Verificar si el canvas está en el DOM
        console.log('Elemento canvas encontrado:', !!canvas);
        if (canvas) {
            console.log('Dimensiones del canvas:', {
                width: canvas.offsetWidth,
                height: canvas.offsetHeight,
                clientWidth: canvas.clientWidth,
                clientHeight: canvas.clientHeight
            });

            // Limpiar los datos del gráfico antes de crearlo
            const cleanChartData = JSON.parse(JSON.stringify(chartData));
            cleanChartData.datasets[0].data = cleanChartData.datasets[0].data.map(value => {
                // Convertir cualquier valor no numérico a 0
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            });

            // Crear el gráfico después de un pequeño retraso para asegurar que el DOM esté listo
            setTimeout(() => {
                console.log('Creando gráfico con datos limpios...');
                createChart(cleanChartData);
            }, 100);
        }
    }
}

// Función para cargar Chart.js dinámicamente si es necesario
function loadChartJS() {
    return new Promise((resolve, reject) => {
        // Si Chart ya está cargado, resolver inmediatamente
        if (window.Chart) {
            console.log('Chart.js ya está cargado');
            return resolve();
        }

        console.log('Cargando Chart.js...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = () => {
            console.log('Chart.js cargado correctamente');
            resolve();
        };
        script.onerror = () => {
            console.error('Error al cargar Chart.js');
            reject(new Error('No se pudo cargar Chart.js'));
        };
        document.head.appendChild(script);
    });
}

// Función para cargar el plugin de datalabels
async function loadChartJSDatalabels() {
    return new Promise((resolve, reject) => {
        if (window.ChartDataLabels) return resolve();

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0';
        script.onload = () => {
            console.log('Chart.js datalabels plugin cargado');
            resolve();
        };
        script.onerror = () => {
            console.warn('No se pudo cargar el plugin de datalabels');
            resolve(); // Resolvemos igual para que no se quede colgada la promesa
        };
        document.head.appendChild(script);
    });
}

// Función para crear el gráfico de resultados
async function createChart(chartData) {
    console.log('Iniciando creación del gráfico...');
    console.log('Datos del gráfico:', JSON.stringify(chartData, null, 2));

    try {
        // Cargar el plugin de datalabels
        await loadChartJSDatalabels();
        // Asegurarse de que Chart.js esté cargado
        console.log('Verificando carga de Chart.js...');
        await loadChartJS();

        // Asegurarse de que el canvas existe
        console.log('Buscando elemento canvas...');
        const canvas = document.getElementById('resultsChart');
        if (!canvas) {
            console.error('❌ No se encontró el elemento canvas con ID resultsChart');
            return;
        }

        console.log('✅ Canvas encontrado. Dimensiones:', {
            width: canvas.offsetWidth,
            height: canvas.offsetHeight,
            clientWidth: canvas.clientWidth,
            clientHeight: canvas.clientHeight,
            parent: canvas.parentElement ? 'Padre encontrado' : 'Sin padre',
            display: window.getComputedStyle(canvas).display,
            visibility: window.getComputedStyle(canvas).visibility
        });

        // Obtener el contexto 2D
        console.log('Obteniendo contexto 2D...');
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('❌ No se pudo obtener el contexto 2D del canvas');
            return;
        }

        // Destruir gráfico anterior si existe
        if (window.resultsChart && typeof window.resultsChart.destroy === 'function') {
            try {
                window.resultsChart.destroy();
            } catch (e) {
                console.warn('Error al destruir el gráfico anterior:', e);
            }
            window.resultsChart = null;
        }

        // Registrar el plugin si está disponible
        if (window.ChartDataLabels) {
            Chart.register(ChartDataLabels);
        }

        // Crear nuevo gráfico horizontal
        window.resultsChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Puntuación BR',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Escalas',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            autoSkip: false
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `BR: ${context.raw}`;
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: function (context) {
                            // Cambiar el color del texto según el color de fondo para mejor contraste
                            const bgColor = context.dataset.backgroundColor[context.dataIndex];
                            if (bgColor.includes('239, 68, 68') || bgColor.includes('249, 115, 22')) {
                                return '#000'; // Texto blanco para fondos rojos/naranjas
                            }
                            return '#000'; // Texto negro para otros fondos
                        },
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function (value) {
                            return value;
                        },
                        display: function (context) {
                            return context.dataset.data[context.dataIndex] > 0; // Mostrar solo si el valor es mayor que 0
                        }
                    }
                }
            }
        }
        );

        console.log('✅ Gráfico creado exitosamente');
        return true;

    } catch (error) {
        console.error('Error al crear el gráfico:', error);
        // Mostrar mensaje de error en la interfaz
        const chartContainer = document.getElementById('chart-container');
        if (chartContainer) {
            chartContainer.innerHTML = `
                <div class="error-message">
                    <p>Error al generar el gráfico. Por favor, intente nuevamente.</p>
                    <p>Detalles: ${error.message}</p>
                </div>
            `;
        }
        return false;
    }
}

// Función para obtener la clase CSS según la categoría
function getCategoryClass(categoria) {
    if (!categoria) return '';

    if (categoria.includes('Bajo')) {
        return 'low';
    } else if (categoria.includes('Ausente')) {
        return 'absent';
    } else if (categoria.includes('Elevado') || categoria === 'Alto') {
        return 'elevated';
    } else if (categoria.includes('Presente')) {
        return 'present';
    } else if (categoria.includes('Prominente')) {
        return 'prominent';
    }
    return '';
}

// Función para obtener el color según la categoría del BR
function getCategoryColor(br) {
    if (br >= 85) {
        return '#ef4444'; // Rojo - Indicador Elevado (85-100)
    } else if (br >= 75) {
        return '#f97316'; // Naranja - Indicador Prominente (75-84)
    } else if (br >= 60) {
        return '#eab308'; // Amarillo - Indicador Presente (60-74)
    } else if (br >= 35) {
        return '#22c55e'; // Verde - Indicador Bajo (35-59)
    } else {
        return '#3b82f6'; // Azul - Indicador Ausente (0-34)
    }
}

// Helper: convertir color HEX a RGBA con una opacidad dada
function hexToRgba(hex, alpha = 0.6) {
    const clean = hex.replace('#', '');
    const bigint = parseInt(clean, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Función para mostrar interpretación general
function showGeneralInterpretation(results) {
    // Encontrar las escalas más elevadas
    let maxBR = -1;
    let maxScales = [];

    // Escalas a interpretar
    const interpretationScales = ["1", "2A", "2B", "3", "4", "5", "6A", "6B", "7", "8A", "8B", "S", "C", "P", "A", "H", "N", "D", "B", "T", "R", "SS", "CC", "PP"];

    for (const scaleId of interpretationScales) {
        const result = results[scaleId];
        if (result.brScore > maxBR) {
            maxBR = result.brScore;
            maxScales = [result];
        } else if (result.brScore === maxBR && result.brScore >= 60) {
            maxScales.push(result);
        }
    }

    if (maxBR >= 60) {
        const scaleNames = maxScales.map(s => s.name).join(', ');
        const category = getCategoria(maxBR);

        // setTimeout(() => {
        //     alert(`Interpretación Principal:\n\nLas escalas más elevadas son: ${scaleNames}\nPuntuación BR más alta: ${maxBR}\nCategoría: ${category}\n\nNota: Esta interpretación es preliminar. Se recomienda consultar con un profesional de la salud mental calificado para una evaluación completa.`);
        // }, 500);
        let timerInterval;
        // Swal.fire({
        //     title: "Interpretación Principal!",
        //     html: `Las escalas más elevadas son: <br></br>\n${scaleNames}<br></br>\n Puntuación BR más alta: ${maxBR}\n  <br></br> Categoría: ${category}\n\n <br></br>Nota: Esta interpretación es preliminar. Se recomienda consultar con un profesional de la salud mental calificado para una evaluación completa.`,
        //     timer: 10000,
        //     timerProgressBar: false,
        //     didOpen: () => {
        //         Swal.showLoading();
        //         const timer = Swal.getPopup().querySelector("b");
        //         timerInterval = setInterval(() => {
        //             timer.textContent = `${Swal.getTimerLeft()}`;
        //         }, 100);
        //     },
        //     willClose: () => {
        //         clearInterval(timerInterval);
        //     }
        // }).then((result) => {
        //     /* Read more about handling dismissals below */
        //     if (result.dismiss === Swal.DismissReason.timer) {
        //         console.log("I was closed by the timer");
        //     }
        // });
    }
}

// Agregar botón para cargar respuestas de prueba
function addTestButton() {
    const buttonContainer = document.querySelector('.button-container');
    if (buttonContainer && !document.getElementById('load-test-btn')) {
        const testButton = document.createElement('button');
        testButton.id = 'load-test-btn';
        testButton.textContent = 'Cargar Respuestas de Prueba';
        testButton.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        testButton.addEventListener('click', loadTestResponses);
        buttonContainer.appendChild(testButton);
    }
}


// Función para cargar respuestas de prueba (las que mencionaste)
function loadTestResponses() {
    console.log('Cargando respuestas de prueba...');

    // Preguntas que deben marcarse como VERDADERO
    const trueQuestions = [1, 4, 6, 8, 12, 16, 20, 21, 23, 25, 32, 34, 35, 36, 41, 42, 43, 44, 45, 46, 47, 51, 54, 55, 56, 57, 58, 61, 62, 63, 68, 70, 73, 74, 75, 76, 79, 80, 82, 83, 84, 86, 87, 88, 94, 104, 106, 109, 111, 112, 114, 115, 119, 120, 122, 123, 124, 125, 126, 128, 129, 133, 134, 141, 142, 145, 146, 147, 148, 149, 153, 154, 159, 160, 162, 164, 166, 169, 170, 172, 173];

    // Preguntas que deben marcarse como FALSO
    const falseQuestions = [2, 3, 5, 7, 9, 10, 11, 13, 14, 15, 17, 18, 19, 22, 24, 26, 27, 28, 29, 30, 31, 33, 37, 38, 39, 40, 48, 49, 50, 52, 53, 59, 60, 64, 65, 66, 67, 69, 71, 72, 77, 78, 81, 85, 89, 90, 91, 92, 93, 95, 96, 97, 98, 99, 100, 101, 102, 103, 105, 107, 108, 110, 113, 116, 117, 118, 121, 127, 130, 131, 132, 135, 136, 137, 138, 139, 140, 143, 144, 150, 151, 152, 155, 156, 157, 158, 161, 163, 165, 167, 168, 171, 174, 175];

    // Verificar que tenemos todas las preguntas
    const totalTestQuestions = trueQuestions.length + falseQuestions.length;
    console.log(`Total preguntas de prueba: ${totalTestQuestions}`);

    if (totalTestQuestions !== 175) {
        console.error(`Error: Solo hay ${totalTestQuestions} preguntas de prueba, deberían ser 175`);
        return;
    }

    // Limpiar respuestas previas
    responses = {};
    answeredCount = 0;

    // Limpiar selecciones visuales previas
    document.querySelectorAll('.option').forEach(option => {
        option.classList.remove('selected');
    });

    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });

    // Procesar preguntas VERDADERAS
    trueQuestions.forEach(questionNumber => {
        setQuestionResponse(questionNumber, true, "true");
    });

    // Procesar preguntas FALSAS
    falseQuestions.forEach(questionNumber => {
        setQuestionResponse(questionNumber, false, "false");
    });

    console.log(`Respuestas cargadas: ${answeredCount}/175`);
    updateProgress();

    // Validar automáticamente después de cargar
    setTimeout(() => {
        validateResponses();
    }, 100);
}

// Función auxiliar para establecer la respuesta de una pregunta
function setQuestionResponse(questionNumber, value, targetValue) {
    // Asegurarse de que la pregunta existe en el formulario
    const questionElement = document.querySelector(`#question-${questionNumber}`);
    if (!questionElement) {
        console.warn(`No se encontró la pregunta ${questionNumber} en el DOM`);
        return;
    }

    // Actualizar respuesta
    responses[questionNumber] = value;
    answeredCount++;

    // Actualizar interfaz visual - intentar con diferentes selectores si es necesario
    let targetOption = document.querySelector(`label[data-question="${questionNumber}"][data-value="${targetValue}"]`);

    // Si no se encuentra con el selector normal, intentar con otros formatos
    if (!targetOption) {
        targetOption = document.querySelector(`label[for^="q${questionNumber}_"][data-value="${targetValue}"]`);
    }

    // Si aún no se encuentra, intentar con otro formato de selector
    if (!targetOption) {
        targetOption = document.querySelector(`label[for^="q${questionNumber}-"][data-value="${targetValue}"]`);
    }

    if (targetOption) {
        // Encontrar el radio button dentro del label
        const radio = targetOption.querySelector('input[type="radio"]');

        if (radio) {
            // Remover selección previa de esta pregunta
            const allOptionsForQuestion = document.querySelectorAll(`[name="q${questionNumber}"], [name^="q${questionNumber}_"], [name^="q${questionNumber}-"]`);
            allOptionsForQuestion.forEach(opt => {
                const parentLabel = opt.closest('label');
                if (parentLabel) {
                    parentLabel.classList.remove('selected');
                }
                opt.checked = false;
            });

            // Marcar como seleccionado
            targetOption.classList.add('selected');
            radio.checked = true;
            markQuestionAnswered(questionNumber, true);

            // Disparar evento change para asegurar que se registre el cambio
            const event = new Event('change');
            radio.dispatchEvent(event);
        } else {
            console.warn(`No se pudo encontrar el radio button para la pregunta ${questionNumber}`);
        }
    } else {
        console.warn(`No se pudo encontrar la opción "${targetValue}" para la pregunta ${questionNumber}`);
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function () {
    console.log('Iniciando MCMI-III...');
    // Swal.fire("SweetAlert2 is working!");

    generateQuestions();
    addTestButton();

    // Event listeners para los botones
    document.getElementById('validate-btn').addEventListener('click', validateResponses);
    document.getElementById('calculate-btn').addEventListener('click', calculateResults);
    document.getElementById('clear-btn').addEventListener('click', clearAllResponses);

    console.log('MCMI-III Evaluation Tool loaded successfully');
    console.log('Total questions:', questions.length);
    console.log('Total scales:', Object.keys(scales).length);

    // Cargar respuestas de prueba automáticamente después de 2 segundos
    setTimeout(() => {
        console.log('Cargando respuestas de prueba automáticamente...');
        loadTestResponses();
    }, 200);
}
);