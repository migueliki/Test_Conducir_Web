<?php
session_start();
session_destroy();
echo "Sesión limpiada. <a href='index.php'>Volver al inicio</a>";
?>