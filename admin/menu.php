<?php
$paginas = [
  'inicio' => 'Inicio', 'acerca' => 'Acerca de S.', 'quienes' => 'Quiénes somos', 'antecedentes' => 'Antecedentes', 'ayuda' => 'Ayuda',
  'guia' => 'Guía', 'contacto' => 'Contacto', 'privacidad' => 'Privacidad', 'terminos' => 'Términos',
  'cookies' => 'Cookies', 'proteccion' => 'Protección',
];

$ver = "../pagina.php?clave=$clave";
if ($clave == 'inicio') $ver = "../index.php";
if ($clave == 'quienes') $ver = "../acerca.php";
if (in_array($clave, ['acerca', 'antecedentes', 'ayuda'])) $ver = "../$clave.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SAPIENS | Administrador</title>
  <link rel="icon" href="../assets/img/isotipo.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/panel.css?v=3">
</head>
<body>
<header class="barra">
  <strong><span class="logo-s">S</span>SAPIENS</strong>
  <a href="../logout.php" class="small text-muted"><i class="bi bi-box-arrow-right"></i> Salir</a>
</header>
<div class="admin">
  <aside class="lateral">
    <a href="index.php?p=<?= $clave ?>" class="<?= $pagina == 'editar' ? 'activo' : '' ?>"><i class="bi bi-pencil"></i> Editar</a>
    <a href="eliminar.php?p=<?= $clave ?>" class="<?= $pagina == 'eliminar' ? 'activo' : '' ?>"><i class="bi bi-trash"></i> Eliminar</a>
    <a href="<?= $ver ?>" target="_blank"><i class="bi bi-eye"></i> Ver</a>
  </aside>
  <main class="flex-grow-1" style="max-width: 720px">
    <h4 class="fw-bold mb-3">Bienvenido administrador</h4>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
      <small class="fw-semibold"><i class="bi bi-pencil-square text-acento"></i> Página a editar:</small>
      <span class="filtros bg-white rounded-pill p-1">
        <?php foreach ($paginas as $c => $nombre) { ?>
          <a href="<?= $pagina == 'editar' ? 'index' : 'eliminar' ?>.php?p=<?= $c ?>" class="<?= $c == $clave ? 'activo' : '' ?>"><?= $nombre ?></a>
        <?php } ?>
      </span>
    </div>
