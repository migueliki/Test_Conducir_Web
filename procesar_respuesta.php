<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_index = (int)$_POST['question_index'];
    $respuesta = isset($_POST['respuesta']) ? (int)$_POST['respuesta'] : null;
    $test_id = $_POST['test_id'];
    $category = $_POST['category'];
    
    if (isset($_SESSION['test_data'])) {
        $_SESSION['test_data']['respuestas'][$question_index] = $respuesta;
        
        // El tiempo restante se mantiene sin cambios ya que se gestiona en el frontend
        // No necesitamos actualizarlo aquí para que no se reinicie
    }
    
    // Redirigir a la siguiente pregunta o mantener en la actual
    header("Location: test.php?test_id=$test_id&category=$category&question=$question_index");
    exit();
}

header('Location: index.php');
exit();