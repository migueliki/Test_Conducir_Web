<?php
// Cargar información de tests disponibles con manejo de errores
$tests_normales = [];
$tests_senales = [];
$test_adas = [];

// Función para cargar JSON de forma segura
function cargarTests($archivo) {
    if (file_exists($archivo)) {
        $datos = file_get_contents($archivo);
        $resultado = json_decode($datos, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($resultado)) {
            return $resultado;
        }
    }
    return [];
}

// Cargar los archivos JSON
$tests_normales = cargarTests('data/tests_normales.json');
$tests_senales = cargarTests('data/tests_senales.json');
$test_adas = cargarTests('data/test_adas.json');

// Si no hay tests normales, mostrar mensaje de error
if (empty($tests_normales)) {
    echo "<div class='error-message'>";
    echo "<h3>Error: No se pudieron cargar los tests</h3>";
    echo "<p>Verifica que el archivo 'data/tests_normales.json' exista y tenga formato JSON válido.</p>";
    echo "</div>";
}

// Guardar en sesión para otras páginas
$_SESSION['tests_data'] = [
    'normales' => $tests_normales,
    'senales' => $tests_senales,
    'adas' => $test_adas
];
?>

<!-- Menú principal -->
<div class="menu-buttons">
    <button class="menu-btn active" data-section="normales">Tests Normales (<?php echo count($tests_normales); ?>)</button>
    <button class="menu-btn" data-section="senales">Tests de Señales Nuevas (<?php echo count($tests_senales); ?>)</button>
    <button class="menu-btn" data-section="adas">Test de ADAS (<?php echo count($test_adas); ?>)</button>
</div>

<!-- Sección de Tests Normales -->
<div class="menu-section active" id="normales">
    <h2 class="menu-title">Tests Normales</h2>
    <p>Selecciona uno de los <?php echo count($tests_normales); ?> tests normales para practicar:</p>
    <div class="test-grid">
        <?php foreach ($tests_normales as $id => $test): ?>
            <div class="test-card" data-test-id="<?php echo $id; ?>" data-category="normales">
                <h3>Test <?php echo str_replace('test_', '', $id); ?></h3>
                <p><?php echo $test['nombre']; ?></p>
                <small><?php echo count($test['preguntas']); ?> preguntas</small>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Sección de Tests de Señales Nuevas -->
<div class="menu-section" id="senales">
    <h2 class="menu-title">Tests de Señales Nuevas</h2>
    <p>Selecciona uno de los <?php echo count($tests_senales); ?> tests de señales nuevas:</p>
    <div class="test-grid">
        <?php foreach ($tests_senales as $id => $test): ?>
            <div class="test-card" data-test-id="<?php echo $id; ?>" data-category="senales">
                <h3>Test Señales <?php echo str_replace('senal_', '', $id); ?></h3>
                <p><?php echo $test['nombre']; ?></p>
                <small><?php echo count($test['preguntas']); ?> preguntas</small>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Sección de Test de ADAS -->
<div class="menu-section" id="adas">
    <h2 class="menu-title">Test de ADAS</h2>
    <p>Realiza el test especial sobre sistemas ADAS:</p>
    <div class="test-grid">
        <?php foreach ($test_adas as $id => $test): ?>
            <div class="test-card" data-test-id="<?php echo $id; ?>" data-category="adas">
                <h3>Test ADAS</h3>
                <p><?php echo $test['nombre']; ?></p>
                <small><?php echo count($test['preguntas']); ?> preguntas</small>
            </div>
        <?php endforeach; ?>
    </div>
</div>