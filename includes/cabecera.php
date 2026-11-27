<?php // cabecera de las paginas publicas, antes de incluirla se pone $titulo ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> - SAPIENS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/img/logo-completo.png" alt="SAPIENS" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPublico">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="quienes.php">Quiénes somos</a></li>
                <li class="nav-item"><a class="nav-link" href="antecedentes.php">Antecedentes</a></li>
                <li class="nav-item"><a class="nav-link" href="ayuda.php">Ayuda</a></li>
            </ul>
            <a href="index.php#login" class="btn btn-outline-primary me-2">Iniciar sesión</a>
            <a href="registro.php" class="btn btn-primary">Registrarse</a>
        </div>
    </div>
</nav>
