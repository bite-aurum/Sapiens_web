<?php
// parte de arriba de los paneles, antes de incluirla se pone $titulo
$menus = [
    'alumno' => [
        'panel.php' => 'Inicio',
        'proyectos.php' => 'Mis proyectos',
        'tareas.php' => 'Mis tareas',
        'notificaciones.php' => 'Notificaciones'
    ],
    'docente' => [
        'panel.php' => 'Inicio',
        'proyectos.php' => 'Proyectos',
        'tareas.php' => 'Tareas',
        'reportes.php' => 'Reportes',
        'notificaciones.php' => 'Notificaciones'
    ],
    'coordinador' => [
        'panel.php' => 'Inicio',
        'rendimiento.php' => 'Rendimiento',
        'docentes.php' => 'Docentes',
        'proyectos.php' => 'Proyectos'
    ],
    'admin' => [
        'panel.php' => 'Inicio',
        'paginas.php' => 'Páginas públicas',
        'legales.php' => 'Páginas legales',
        'ayuda.php' => 'Ayuda',
        'pie.php' => 'Footer'
    ]
];
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> - SAPIENS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../<?= $rol ?>/panel.php">SAPIENS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPanel">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPanel">
            <ul class="navbar-nav me-auto">
                <?php foreach ($menus[$rol] as $archivo => $texto): ?>
                    <li class="nav-item"><a class="nav-link" href="../<?= $rol ?>/<?= $archivo ?>"><?= $texto ?></a></li>
                <?php endforeach; ?>
            </ul>
            <span class="navbar-text me-3">Hola, <?= e($_SESSION['nombre']) ?></span>
            <?php if ($rol !== 'admin'): ?>
                <a class="btn btn-outline-light btn-sm me-2" href="../cuenta/perfil.php">Mi perfil</a>
            <?php endif; ?>
            <a class="btn btn-outline-light btn-sm" href="../acciones/logout.php">Cerrar sesión</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <!-- mensajes que mandan las acciones -->
    <?php if (!empty($_GET['mensaje'])): ?>
        <div class="alert alert-success"><?= e($_GET['mensaje']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= e($_GET['error']) ?></div>
    <?php endif; ?>
