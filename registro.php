<?php
session_start();
$destino = !empty($_SESSION['usuario_id']) ? 'index.php' : 'login.php';
header('Location: ' . $destino);
exit;
?>
