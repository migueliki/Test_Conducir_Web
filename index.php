<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conducir Online</title>
    <!-- Usar Roboto para toda la interfaz -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="theme-metal">
    <div class="container">
        <header>
            <h1>Test de Conducir Online</h1>
            <p>Practica para tu examen teórico de conducir</p>
        </header>
        
        <?php include 'menu.php'; ?>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>