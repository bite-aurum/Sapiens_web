<?php
session_start();
include 'conexion.php';
include 'funciones.php';

$correo = limpiar($_POST['correo']);
$pass   = $_POST['pass'];

$usuarios = [
  ['administrador', 'id_admin',       'admin'],
  ['coordinador',   'id_coordinador', 'coordinador'],
  ['docente',       'id_docente',     'docente'],
  ['alumno',        'id_alumno',      'alumno'],
];

foreach ($usuarios as $u) {
  $res  = mysqli_query($con, "SELECT * FROM $u[0] WHERE correo = '$correo'");
  $fila = mysqli_fetch_assoc($res);

  if ($fila && password_verify($pass, $fila['contrasena'])) {
    $_SESSION['id']     = $fila[$u[1]];
    $_SESSION['rol']    = $u[2];
    $_SESSION['nombre'] = $fila['nombre'];
    header("Location: $u[2]/index.php");
    exit;
  }
}

header('Location: index.php?error=1#login');
