<?php
// guarda una cuenta nueva, hace lo mismo que registrar_usuario.php de la app movil
require_once '../config/conexion.php';

$rol = $_POST['rol'] ?? '';
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';
$volver = '../registro.php';

// revisamos los datos
if (!in_array($rol, ['alumno', 'docente', 'coordinador'])) {
    ir($volver, 'Selecciona si eres alumno, docente o coordinador', 'error');
}
if ($nombre_completo === '' || $correo === '' || $contrasena === '') {
    ir($volver, 'Completa todos los campos', 'error');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    ir($volver, 'El correo no es válido', 'error');
}
if (strlen($contrasena) < 8) {
    ir($volver, 'La contraseña debe tener al menos 8 caracteres', 'error');
}
if ($contrasena !== $confirmar) {
    ir($volver, 'Las contraseñas no coinciden', 'error');
}

// separamos el nombre de los apellidos
$palabras = preg_split('/\s+/', $nombre_completo);
if (count($palabras) < 2) {
    ir($volver, 'Escribe tu nombre y al menos un apellido', 'error');
}
if (count($palabras) == 2) {
    $nombre = $palabras[0];
    $apellido_paterno = $palabras[1];
    $apellido_materno = '';
} else {
    $apellido_materno = array_pop($palabras);
    $apellido_paterno = array_pop($palabras);
    $nombre = implode(' ', $palabras);
}

// el correo no debe existir en ningun rol
foreach (['alumno', 'docente', 'coordinador'] as $tabla) {
    if (uno("SELECT correo FROM $tabla WHERE correo = ?", "s", [$correo])) {
        ir($volver, 'Ya existe una cuenta con ese correo', 'error');
    }
}

$cifrada = password_hash($contrasena, PASSWORD_DEFAULT);

if ($rol === 'coordinador') {
    // el coordinador lleva un codigo para vincular a sus docentes
    $codigo = 'SAP-' . strtoupper(substr(md5(uniqid()), 0, 5));
    ejecutar("INSERT INTO coordinador (nombre, apellido_paterno, apellido_materno, correo, contrasena, codigo_vinculo) VALUES (?, ?, ?, ?, ?, ?)",
        "ssssss", [$nombre, $apellido_paterno, $apellido_materno, $correo, $cifrada, $codigo]);
} else {
    ejecutar("INSERT INTO $rol (nombre, apellido_paterno, apellido_materno, correo, contrasena) VALUES (?, ?, ?, ?, ?)",
        "sssss", [$nombre, $apellido_paterno, $apellido_materno, $correo, $cifrada]);
}

ir('../index.php', 'Cuenta creada, ya puedes iniciar sesión');
