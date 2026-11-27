<?php
$sinLeer = contar("SELECT COUNT(*) FROM notificacion WHERE id_alumno = $_SESSION[id] AND leida = 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (isset($base)) { ?><base href="<?= $base ?>"><?php } ?>
  <title>SAPIENS | Alumno</title>
  <link rel="icon" href="../assets/img/isotipo.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/panel.css?v=3">
</head>
<body>
<header class="barra">
  <a href="index.php"><img class="logo" src="../assets/img/logo-completo.png" alt="SAPIENS"></a>
  <nav class="menu">
    <a href="index.php" class="<?= $pagina == 'inicio' ? 'activo' : '' ?>">Inicio</a>
    <a href="proyectos.php" class="<?= $pagina == 'proyectos' ? 'activo' : '' ?>">Mis proyectos</a>
    <a href="tareas.php" class="<?= $pagina == 'tareas' ? 'activo' : '' ?>">Mis tareas</a>
    <a href="notificaciones.php" class="<?= $pagina == 'notificaciones' ? 'activo' : '' ?>">Notificaciones</a>
  </nav>
  <div class="usuario">
    <a href="notificaciones.php" class="campana"><i class="bi bi-bell"></i><?php if ($sinLeer > 0) { ?><span class="punto"></span><?php } ?></a>
    <a href="../perfil.php" class="d-flex align-items-center gap-2">
      <span class="avatar verde"><?= iniciales($_SESSION['nombre']) ?></span>
      <span><?= $_SESSION['nombre'] ?><small>Alumno</small></span>
    </a>
  </div>
</header>
<main class="contenido">
