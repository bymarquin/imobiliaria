<?php
session_start();
$_SESSION = [];    // apaga os dados do usuário logado
session_destroy(); // encerra a sessão
header('Location: login.php');
exit;
