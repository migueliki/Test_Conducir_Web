<?php
session_start();

if (!isset($_SESSION['test_data'])) {
    header('Location: index.php');
    exit();
}

$test_data = $_SESSION['test_data'];
$test_id = $test_data['test_id'];
$category = $test_data['category'];

// Cargar el test desde la sesión
if (!isset($_SESSION['tests_data'][$category])) {
    header('Location: index.php');
    exit();
}

$tests = $_SESSION['tests_data'][$category];
$test = $tests[$test_id] ?? null;

if (!$test) {
    header('Location: index.php');
    exit();
}

// Calcular resultados
$correctas = 0;
$incorrectas = 0;
$no_contestadas = 0;

foreach ($test['preguntas'] as $index => $pregunta) {
    if ($test_data['respuestas'][$index] === null) {
        $no_contestadas++;
    } elseif ($test_data['respuestas'][$index] == $pregunta['respuesta_correcta']) {
        $correctas++;
    } else {
        $incorrectas++;
    }
}

$total_preguntas = count($test['preguntas']);
$porcentaje = ($correctas / $total_preguntas) * 100;
$aprobado = $correctas >= 27; // 27/30 para aprobar

// Calcular tiempo utilizado
$tiempo_utilizado = time() - $test_data['tiempo_inicio'];
$minutos = floor($tiempo_utilizado / 60);
$segundos = $tiempo_utilizado % 60;

// Limpiar sesión del test
unset($_SESSION['test_data']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - Test de Conducir</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Resultados del Test</h1>
            <p><?php echo $test['nombre']; ?></p>
        </header>
        
        <div class="results active">
            <h2><?php echo $aprobado ? '¡Felicidades! Has aprobado el test.' : 'No has aprobado el test.'; ?></h2>
            
            <div class="score"><?php echo $correctas; ?>/<?php echo $total_preguntas; ?></div>
            <div class="percentage"><?php echo number_format($porcentaje, 1); ?>%</div>
            
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
            
            <div class="stats">
                <div class="stat correct">
                    <div class="stat-value"><?php echo $correctas; ?></div>
                    <div class="stat-label">Correctas</div>
                </div>
                <div class="stat incorrect">
                    <div class="stat-value"><?php echo $incorrectas; ?></div>
                    <div class="stat-label">Incorrectas</div>
                </div>
                <div class="stat unanswered">
                    <div class="stat-value"><?php echo $no_contestadas; ?></div>
                    <div class="stat-label">Sin contestar</div>
                </div>
                <div class="stat time">
                    <div class="stat-value"><?php echo $minutos; ?>:<?php echo str_pad($segundos, 2, '0', STR_PAD_LEFT); ?></div>
                    <div class="stat-label">Tiempo</div>
                </div>
            </div>
            
            <div class="result-details">
                <h3>Detalle de respuestas:</h3>
                <div class="answers-grid">
                    <?php foreach ($test['preguntas'] as $index => $pregunta): ?>
                        <div class="answer-item <?php 
                            echo $test_data['respuestas'][$index] === null ? 'unanswered' : 
                                ($test_data['respuestas'][$index] == $pregunta['respuesta_correcta'] ? 'correct' : 'incorrect'); 
                        ?>">
                            <span class="question-number"><?php echo $index + 1; ?></span>
                            <span class="answer-status">
                                <?php 
                                if ($test_data['respuestas'][$index] === null) {
                                    echo 'No contestada';
                                } elseif ($test_data['respuestas'][$index] == $pregunta['respuesta_correcta']) {
                                    echo 'Correcta';
                                } else {
                                    echo 'Incorrecta';
                                }
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($incorrectas > 0): ?>
                    <div class="incorrect-answers">
                        <h3>Preguntas incorrectas:</h3>
                        <?php foreach ($test['preguntas'] as $index => $pregunta): ?>
                            <?php if ($test_data['respuestas'][$index] !== null && $test_data['respuestas'][$index] != $pregunta['respuesta_correcta']): ?>
                                <div class="incorrect-question">
                                    <h4>Pregunta <?php echo $index + 1; ?></h4>
                                    <p class="question-text"><?php echo $pregunta['pregunta']; ?></p>
                                    
                                    <?php if (!empty($pregunta['imagen'])): ?>
                                        <div class="question-image">
                                            <img src="images/preguntas/<?php echo $pregunta['imagen']; ?>" alt="Imagen de la pregunta">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="options-review">
                                        <?php foreach ($pregunta['opciones'] as $opcion_index => $opcion): ?>
                                            <div class="option-review <?php 
                                                echo $opcion_index == $pregunta['respuesta_correcta'] ? 'correct' : 
                                                    ($opcion_index == $test_data['respuestas'][$index] ? 'incorrect' : ''); 
                                            ?>">
                                                <?php echo chr(65 + $opcion_index); ?>) <?php echo $opcion; ?>
                                                <?php if ($opcion_index == $pregunta['respuesta_correcta']): ?>
                                                    <span class="correct-mark">✓ (Correcta)</span>
                                                <?php elseif ($opcion_index == $test_data['respuestas'][$index]): ?>
                                                    <span class="incorrect-mark">✗ (Tu respuesta)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="test.php?test_id=<?php echo $test_id; ?>&category=<?php echo $category; ?>" class="btn btn-primary">Repetir Test</a>
                <a href="index.php" class="btn btn-success">Volver al Menú</a>
            </div>
        </div>
    </div>
</body>
</html>