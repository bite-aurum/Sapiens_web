<?php
// recibe el login de alumnos, docentes y coordinadores
session_start();
require_once '../config/conexion.php';

$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if ($correo === '' || $contrasena === '') {
    ir('../index.php', 'Escribe tu correo y tu contraseña', 'error');
}

$roles = ['alumno', 'docente', 'coordinador'];

foreach ($roles as $rol) {
    $usuario = uno("SELECT id_$rol AS id, nombre, contrasena FROM $rol WHERE correo = ?", "s", [$correo]);

    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['rol'] = $rol;
        $_SESSION['nombre'] = $usuario['nombre'];
        ir("../$rol/panel.php");
    }
}

ir('../index.php', 'Correo o contraseña incorrectos', 'error');
