<?php
// cierra la sesion de cualquier rol
session_start();
$era_admin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
session_destroy();

header('Location: ' . ($era_admin ? '../admin/login.php' : '../index.php'));
exit;
