<?php
$archivo = 'data/tests_normales.json';
if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $json = json_decode($contenido, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ JSON VÁLIDO<br>";
        echo "Número de tests: " . count($json) . "<br>";
        foreach ($json as $test_id => $test) {
            echo "Test: $test_id - " . $test['nombre'] . " (" . count($test['preguntas']) . " preguntas)<br>";
        }
    } else {
        echo "❌ JSON INVÁLIDO: " . json_last_error_msg();
    }
} else {
    echo "❌ Archivo no encontrado: $archivo";
}
?>