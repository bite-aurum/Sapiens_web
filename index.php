<?php
include 'conexion.php';
include 'funciones.php';
$pag   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = 'inicio'"));
$datos = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $pag[id_pagina] ORDER BY orden");
$titulo = "Inicio";
include 'header.php';
?>
  <main class="hero" id="inicio">
    <div class="hero-texto">
      <small><?= $pag['etiqueta'] ?></small>
      <h1><?= $pag['titulo'] ?></h1>
      <p><?= $pag['subtitulo'] ?></p>
      <div class="stats">
        <?php while ($d = mysqli_fetch_assoc($datos)) { ?>
          <div><strong><?= $d['titulo'] ?></strong><span><?= $d['texto'] ?></span></div>
        <?php } ?>
      </div>
      <?php if ($pag['imagen']) { ?><img class="portada-img" src="assets/img/<?= $pag['imagen'] ?>"><?php } ?>
    </div>

    <form class="login" id="login" method="post" action="login.php">
      <h2>Iniciar sesión</h2>
      <p class="sub">Ingresa con tu cuenta institucional</p>

      <label>Correo</label>
      <input type="text" name="correo" placeholder="tucorreo@uthh.edu.mx" required>

      <label>Contraseña</label>
      <input type="password" name="pass" placeholder="••••••••••" required>

      <div class="fila">
        <label class="check"><input type="checkbox"> Recordarme</label>
        <a href="pagina.php?clave=contacto">¿Olvidaste tu contraseña?</a>
      </div>

      <?php if (isset($_GET['error'])) { ?>
        <p class="mensaje error">Correo o contraseña incorrectos</p>
      <?php } ?>
      <button type="submit" class="btn btn-primario">Entrar</button>

      <p class="registro">¿Aún no tienes cuenta? <a href="registro.php">Regístrate</a></p>
      <p class="legal">Al continuar aceptas los <a href="pagina.php?clave=terminos">Términos y condiciones</a> y la <a href="pagina.php?clave=privacidad">Política de privacidad</a>.</p>
    </form>
  </main>
<?php include 'footer.php'; ?>
