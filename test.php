<?php
session_start();

// Verificar si se ha seleccionado un test
if (!isset($_GET['test_id']) || !isset($_GET['category'])) {
    header('Location: index.php');
    exit();
}

$test_id = $_GET['test_id'];
$category = $_GET['category'];

// Cargar el test seleccionado desde la sesión
if (!isset($_SESSION['tests_data'][$category])) {
    header('Location: index.php');
    exit();
}

$tests = $_SESSION['tests_data'][$category];

if (!isset($tests[$test_id])) {
    header('Location: index.php');
    exit();
}

$test = $tests[$test_id];
$total_preguntas = count($test['preguntas']);

    // Inicializar sesión del test
if (!isset($_SESSION['test_data'])) {
    $_SESSION['test_data'] = [
        'test_id' => $test_id,
        'category' => $category,
        'respuestas' => array_fill(0, $total_preguntas, null),
        'tiempo_inicio' => time(),
        'tiempo_restante' => 30 * 60 // 30 minutos en segundos
    ];
} else if (isset($_SESSION['test_data']['tiempo_inicio'])) {
    // Calcular el tiempo restante basado en el tiempo transcurrido
    $tiempo_transcurrido = time() - $_SESSION['test_data']['tiempo_inicio'];
    $tiempo_total = 30 * 60; // 30 minutos en segundos
    $_SESSION['test_data']['tiempo_restante'] = max(0, $tiempo_total - $tiempo_transcurrido);
}$current_question = isset($_GET['question']) ? (int)$_GET['question'] : 0;
$pregunta_actual = $test['preguntas'][$current_question];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test <?php echo $test_id; ?> - Conducir Online</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo $test['nombre']; ?></h1>
            <p>Test <?php echo $test_id; ?> - <?php echo $category == 'normales' ? 'Test Normal' : ($category == 'senales' ? 'Señales Nuevas' : 'ADAS'); ?></p>
        </header>
        
        <div class="test-section active">
            <div class="timer" id="timer">30:00</div>
            <div class="question-counter">Pregunta <?php echo $current_question + 1; ?> de <?php echo $total_preguntas; ?></div>
            
            <div class="question">
                <div class="question-text">
                    <?php echo $pregunta_actual['pregunta']; ?>
                </div>
                
                <?php if (!empty($pregunta_actual['imagen'])): ?>
                <div class="question-image">
                    <img src="images/preguntas/<?php echo $pregunta_actual['imagen']; ?>" alt="Imagen de la pregunta">
                </div>
                <?php endif; ?>
                
                <form action="procesar_respuesta.php" method="POST" class="options-form">
                    <input type="hidden" name="question_index" value="<?php echo $current_question; ?>">
                    <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
                    <input type="hidden" name="category" value="<?php echo $category; ?>">
                    
                    <div class="options">
                        <?php foreach ($pregunta_actual['opciones'] as $index => $opcion): ?>
                            <div class="option <?php echo $_SESSION['test_data']['respuestas'][$current_question] == $index ? 'selected' : ''; ?>">
                                <input type="radio" 
                                       name="respuesta" 
                                       value="<?php echo $index; ?>" 
                                       id="opcion<?php echo $index; ?>"
                                       <?php echo $_SESSION['test_data']['respuestas'][$current_question] == $index ? 'checked' : ''; ?>>
                                <label for="opcion<?php echo $index; ?>">
                                    <?php echo chr(65 + $index); ?>) <?php echo $opcion; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="navigation">
                        <?php if ($current_question > 0): ?>
                            <a href="test.php?test_id=<?php echo $test_id; ?>&category=<?php echo $category; ?>&question=<?php echo $current_question - 1; ?>" class="btn btn-secondary">Anterior</a>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary" disabled>Anterior</button>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-primary">Guardar Respuesta</button>
                        
                        <?php if ($current_question < $total_preguntas - 1): ?>
                            <a href="test.php?test_id=<?php echo $test_id; ?>&category=<?php echo $category; ?>&question=<?php echo $current_question + 1; ?>" class="btn btn-primary">Siguiente</a>
                        <?php else: ?>
                            <a href="resultados.php" class="btn btn-warning">Finalizar Test</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="progress-section">
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo (($current_question + 1) / $total_preguntas) * 100; ?>%"></div>
                </div>
                <p><?php echo $current_question + 1; ?>/<?php echo $total_preguntas; ?> preguntas respondidas</p>
            </div>
        </div>
        
        <a href="index.php" class="btn btn-menu">Volver al Menú</a>
    </div>
    
    <script src="js/script.js"></script>
    <script>
        // Temporizador
        let tiempoRestante = <?php echo $_SESSION['test_data']['tiempo_restante']; ?>;
        
        function actualizarTemporizador() {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            document.getElementById('timer').textContent = 
                `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
            
            if (tiempoRestante <= 0) {
                window.location.href = 'resultados.php';
            }
            
            tiempoRestante--;
        }
        
        setInterval(actualizarTemporizador, 1000);
        actualizarTemporizador();
    </script>
</body>
</html>