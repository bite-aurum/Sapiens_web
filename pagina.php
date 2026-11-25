<?php
include 'conexion.php';
include 'funciones.php';
$clave = limpiar($_GET['clave'] ?? '');
$pag   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = '$clave'"));
if (!$pag) { header('Location: index.php'); exit; }
$secciones = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $pag[id_pagina] ORDER BY orden");
$titulo = $pag['titulo'];
include 'header.php';
?>
  <main>
    <section class="acerca-portada">
      <small><?= $pag['etiqueta'] ?></small>
      <h1><?= $pag['titulo'] ?></h1>
      <p><?= $pag['subtitulo'] ?></p>
      <?php if ($pag['imagen']) { ?><img class="portada-img" src="assets/img/<?= $pag['imagen'] ?>"><?php } ?>
    </section>

    <section class="seccion">
      <div class="tarjetas">
        <?php while ($s = mysqli_fetch_assoc($secciones)) { ?>
          <div class="tarjeta">
            <h3><?= $s['titulo'] ?></h3>
            <p><?= $s['texto'] ?></p>
          </div>
        <?php } ?>
      </div>
    </section>
  </main>
<?php include 'footer.php'; ?>
