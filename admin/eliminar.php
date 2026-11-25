<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'admin') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';

$clave = limpiar($_GET['p'] ?? 'inicio');
$pag   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = '$clave'"));
$idp   = $pag['id_pagina'];

if (isset($_GET['seccion'])) {
  $ids = (int) $_GET['seccion'];
  mysqli_query($con, "DELETE FROM seccion WHERE id_seccion = $ids");
  header("Location: eliminar.php?p=$clave&ok=1");
  exit;
}
if (isset($_GET['pregunta'])) {
  $idq = (int) $_GET['pregunta'];
  mysqli_query($con, "DELETE FROM pregunta WHERE id_pregunta = $idq");
  header("Location: eliminar.php?p=$clave&ok=1");
  exit;
}
if (isset($_GET['imagen'])) {
  mysqli_query($con, "UPDATE pagina SET imagen = NULL WHERE id_pagina = $idp");
  header("Location: eliminar.php?p=$clave&ok=1");
  exit;
}

$secciones = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $idp ORDER BY orden");
if ($clave == 'ayuda') {
  $preguntas = mysqli_query($con, "SELECT * FROM pregunta ORDER BY orden");
}

$pagina = 'eliminar';
include 'menu.php';
?>
<?php if (isset($_GET['ok'])) { ?><div class="alert alert-success">Apartado eliminado</div><?php } ?>

<div class="tarjeta lista">
  <div class="titulo-seccion">Apartados de esta página</div>
  <?php if ($pag['imagen']) { ?>
    <div class="fila">
      <span class="small">Imagen del encabezado: <?= $pag['imagen'] ?></span>
      <a href="eliminar.php?p=<?= $clave ?>&imagen=1" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Quitar la imagen?')"><i class="bi bi-trash"></i> Eliminar</a>
    </div>
  <?php } ?>
  <?php while ($s = mysqli_fetch_assoc($secciones)) { ?>
    <div class="fila">
      <span class="small"><strong>Sección:</strong> <?= $s['titulo'] ?></span>
      <a href="eliminar.php?p=<?= $clave ?>&seccion=<?= $s['id_seccion'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta sección?')"><i class="bi bi-trash"></i> Eliminar</a>
    </div>
  <?php } ?>
  <?php if ($clave == 'ayuda') { while ($q = mysqli_fetch_assoc($preguntas)) { ?>
    <div class="fila">
      <span class="small"><strong>Pregunta:</strong> <?= $q['pregunta'] ?></span>
      <a href="eliminar.php?p=<?= $clave ?>&pregunta=<?= $q['id_pregunta'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta pregunta?')"><i class="bi bi-trash"></i> Eliminar</a>
    </div>
  <?php } } ?>
  <div class="fila text-muted small">El encabezado de la página no se puede eliminar, solo editar.</div>
</div>
  </main>
</div>
</body>
</html>
