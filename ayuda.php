<?php
include 'conexion.php';
include 'funciones.php';
$pag       = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = 'ayuda'"));
$preguntas = mysqli_query($con, "SELECT * FROM pregunta ORDER BY orden");
$titulo = "Ayuda";
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
      <h2>Preguntas frecuentes</h2>
      <?php while ($p = mysqli_fetch_assoc($preguntas)) { ?>
        <details>
          <summary><?= $p['pregunta'] ?></summary>
          <p><?= $p['respuesta'] ?></p>
        </details>
      <?php } ?>
    </section>
  </main>
<?php include 'footer.php'; ?>
