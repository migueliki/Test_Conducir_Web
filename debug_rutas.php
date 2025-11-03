<?php
echo "<h2>Debug de Rutas</h2>";

$base_dir = __DIR__;
echo "<p>Directorio actual: <strong>$base_dir</strong></p>";

$archivos = [
    'tests_normales.json' => $base_dir . '/data/tests_normales.json',
    'tests_senales.json' => $base_dir . '/data/tests_senales.json',
    'test_adas.json' => $base_dir . '/data/test_adas.json'
];

foreach ($archivos as $nombre => $ruta) {
    echo "<p><strong>$nombre:</strong> ";
    if (file_exists($ruta)) {
        echo "✅ EXISTE en: $ruta";
        // Verificar si es JSON válido
        $contenido = file_get_contents($ruta);
        if (json_decode($contenido)) {
            echo " ✅ JSON VÁLIDO";
        } else {
            echo " ❌ JSON INVÁLIDO";
        }
    } else {
        echo "❌ NO EXISTE. Buscado en: $ruta";
    }
    echo "</p>";
}
?>