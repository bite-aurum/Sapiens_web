<?php
include 'conexion.php';
include 'funciones.php';

$pag = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = 'quienes'"));
$idp = $pag['id_pagina'];

$primera  = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $idp ORDER BY orden LIMIT 1"));
$ultima   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $idp ORDER BY orden DESC LIMIT 1"));
$tarjetas = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $idp
  AND id_seccion != $primera[id_seccion] AND id_seccion != $ultima[id_seccion] ORDER BY orden");

$titulo = "Quiénes somos";
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
      <h2>¿Qué puedes hacer?</h2>
      <div class="tarjetas">
        <?php while ($t = mysqli_fetch_assoc($tarjetas)) { ?>
          <div class="tarjeta">
            <h3><?= $t['titulo'] ?></h3>
            <p><?= $t['texto'] ?></p>
          </div>
        <?php } ?>
      </div>
    </section>

    <section class="seccion seccion-gris">
      <h2><?= $ultima['titulo'] ?></h2>
      <p><?= $ultima['texto'] ?></p>
      <a href="registro.php" class="btn btn-primario boton-final">Crear mi cuenta</a>
    </section>
  </main>
<?php include 'footer.php'; ?>
