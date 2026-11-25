<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (isset($base)) { ?><base href="<?= $base ?>"><?php } ?>
  <title>SAPIENS | Docente</title>
  <link rel="icon" href="../assets/img/isotipo.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/panel.css?v=2">
</head>
<body>
<header class="barra">
  <a href="index.php"><img class="logo" src="../assets/img/logo-completo.png" alt="SAPIENS"></a>
  <nav class="menu">
    <a href="index.php" class="<?= $pagina == 'inicio' ? 'activo' : '' ?>">Inicio</a>
    <a href="proyectos.php" class="<?= $pagina == 'proyectos' ? 'activo' : '' ?>">Proyectos</a>
    <a href="tareas.php" class="<?= $pagina == 'tareas' ? 'activo' : '' ?>">Tareas</a>
    <a href="reportes.php" class="<?= $pagina == 'reportes' ? 'activo' : '' ?>">Reportes</a>
    <a href="notificaciones.php" class="<?= $pagina == 'notificaciones' ? 'activo' : '' ?>">Notificaciones</a>
  </nav>
  <div class="usuario">
    <a href="notificaciones.php" class="campana"><i class="bi bi-bell"></i></a>
    <a href="../perfil.php" class="d-flex align-items-center gap-2">
      <span class="avatar"><?= iniciales($_SESSION['nombre']) ?></span>
      <span>Prof. <?= $_SESSION['nombre'] ?><small>Docente</small></span>
    </a>
  </div>
</header>
<main class="contenido">
