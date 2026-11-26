<?php
include 'conexion.php';
include 'funciones.php';
$pag = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = 'antecedentes'"));
$res = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $pag[id_pagina] ORDER BY orden");
$secciones = mysqli_fetch_all($res, MYSQLI_ASSOC);

$primera  = array_shift($secciones);
$ultima   = array_pop($secciones);
$tarjetas = $secciones;

$titulo = "Antecedentes";
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
      <h2><?= $primera['titulo'] ?></h2>
      <p><?= $primera['texto'] ?></p>
    </section>

    <section class="seccion seccion-gris">
      <h2>¿Qué cambió en la versión web?</h2>
      <div class="tarjetas">
        <?php foreach ($tarjetas as $t) { ?>
          <div class="tarjeta">
            <h3><?= $t['titulo'] ?></h3>
            <p><?= $t['texto'] ?></p>
          </div>
        <?php } ?>
      </div>
    </section>

    <section class="seccion">
      <h2><?= $ultima['titulo'] ?></h2>
      <p><?= $ultima['texto'] ?></p>
      <a href="registro.php" class="btn btn-primario boton-final">Crear mi cuenta</a>
    </section>
  </main>
<?php include 'footer.php'; ?>
