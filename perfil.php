<?php
session_start();
$rol = $_SESSION['rol'] ?? '';
if (!in_array($rol, ['alumno', 'docente', 'coordinador'])) { header('Location: index.php'); exit; }
include 'conexion.php';
include 'funciones.php';

$tabla = $rol;
$col   = "id_" . $rol;
$id    = $_SESSION['id'];
$msg   = "";

if (isset($_POST['datos'])) {
  $nombre = limpiar($_POST['nombre']);
  $ap     = limpiar($_POST['apellido_paterno']);
  $am     = limpiar($_POST['apellido_materno']);
  $correo = limpiar($_POST['correo']);
  mysqli_query($con, "UPDATE $tabla SET nombre='$nombre', apellido_paterno='$ap', apellido_materno='$am', correo='$correo' WHERE $col = $id");
  $_SESSION['nombre'] = $_POST['nombre'];
  $msg = "Datos guardados";
}

if (isset($_POST['cambiar'])) {
  $u = mysqli_fetch_assoc(mysqli_query($con, "SELECT contrasena FROM $tabla WHERE $col = $id"));
  if (!password_verify($_POST['actual'], $u['contrasena'])) {
    $msg = "La contraseña actual no es correcta";
  } elseif (strlen($_POST['nueva']) < 8) {
    $msg = "La nueva contraseña debe tener mínimo 8 caracteres";
  } elseif ($_POST['nueva'] != $_POST['confirmar']) {
    $msg = "Las contraseñas no coinciden";
  } else {
    $hash = password_hash($_POST['nueva'], PASSWORD_DEFAULT);
    mysqli_query($con, "UPDATE $tabla SET contrasena = '$hash' WHERE $col = $id");
    $msg = "Contraseña actualizada";
  }
}

if (isset($_POST['ligar']) && $rol == 'docente') {
  $clave = limpiar($_POST['clave']);
  $coord = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_coordinador FROM coordinador WHERE codigo_vinculo = '$clave'"));
  if ($coord) {
    mysqli_query($con, "UPDATE docente SET id_coordinador = $coord[id_coordinador] WHERE id_docente = $id");
    $msg = "Te ligaste al coordinador";
  } else {
    $msg = "Esa clave de vinculación no existe";
  }
}

if (isset($_POST['desligar']) && $rol == 'docente') {
  mysqli_query($con, "UPDATE docente SET id_coordinador = NULL WHERE id_docente = $id");
  $msg = "Te desligaste del coordinador";
}

$u = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM $tabla WHERE $col = $id"));

$detalle = "";
if ($rol == 'alumno')      $detalle = $u['carrera'] ? $u['carrera'] . ' · ' . $u['grado'] . '°' . $u['grupo'] : '';
if ($rol == 'docente')     $detalle = $u['departamento'];
if ($rol == 'coordinador') $detalle = 'Clave de vinculación: ' . $u['codigo_vinculo'];

if ($rol == 'docente') {
  $miCoord = mysqli_fetch_assoc(mysqli_query($con, "SELECT nombre, apellido_paterno FROM coordinador WHERE id_coordinador = " . (int) $u['id_coordinador']));
}

$base = "$rol/";
$pagina = 'perfil';
include "$rol/menu.php";
?>
<div class="angosto">
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="titulo">Configuración</h1>
      <p class="subtitulo">Administra tus datos personales y la seguridad de tu cuenta.</p>
    </div>
    <a href="../logout.php" class="btn btn-blanco text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
  </div>
  <?php if ($msg) { ?><div class="alert alert-info"><?= $msg ?></div><?php } ?>

  <form method="post" id="perfil" class="tarjeta mb-4">
    <div class="titulo-seccion">Perfil</div>
    <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
      <span class="avatar grande"><?= iniciales($u['nombre']) ?></span>
      <div>
        <strong><?= $u['nombre'] . ' ' . $u['apellido_paterno'] ?></strong> <span class="etiqueta media"><?= ucfirst($rol) ?></span><br>
        <small class="text-muted"><?= $detalle ?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 mb-3"><label>Nombre</label><input name="nombre" class="form-control" value="<?= $u['nombre'] ?>" required></div>
      <div class="col-md-4 mb-3"><label>Apellido paterno</label><input name="apellido_paterno" class="form-control" value="<?= $u['apellido_paterno'] ?>" required></div>
      <div class="col-md-4 mb-3"><label>Apellido materno</label><input name="apellido_materno" class="form-control" value="<?= $u['apellido_materno'] ?>"></div>
    </div>
    <label>Correo electrónico</label>
    <input name="correo" class="form-control" value="<?= $u['correo'] ?>" required>
  </form>

  <form method="post" class="tarjeta mb-4">
    <div class="titulo-seccion"><span><i class="bi bi-key"></i> Cambiar contraseña</span><span class="normal text-muted small">Mínimo 8 caracteres</span></div>
    <label>Contraseña actual</label>
    <input type="password" name="actual" class="form-control mb-3" placeholder="Ingresa tu contraseña actual" required>
    <div class="row">
      <div class="col-md-6 mb-3"><label>Nueva contraseña</label><input type="password" name="nueva" class="form-control" placeholder="Nueva contraseña" required></div>
      <div class="col-md-6 mb-3"><label>Confirmar contraseña</label><input type="password" name="confirmar" class="form-control" placeholder="Confirma la nueva contraseña" required></div>
    </div>
    <button name="cambiar" class="btn btn-blanco text-acento"><i class="bi bi-key"></i> Actualizar contraseña</button>
  </form>

  <?php if ($rol == 'docente') { ?>
    <form method="post" class="tarjeta mb-4">
      <div class="titulo-seccion"><span><i class="bi bi-link-45deg"></i> Vincular coordinador</span><span class="normal text-muted small">Solo docentes</span></div>
      <p class="caja d-inline-block small mb-3" style="background: #e0f2ef">Coordinador actual: <strong><?= $miCoord ? $miCoord['nombre'] . ' ' . $miCoord['apellido_paterno'] : 'Sin coordinador' ?></strong></p>
      <label class="d-block">Clave de vinculación</label>
      <input name="clave" class="form-control mb-2" placeholder="Ej. SAP-7K2X9">
      <small class="text-muted d-block mb-3">Pídele la clave a tu coordinador. Puedes ligarte o desligarte cuando quieras.</small>
      <button name="ligar" class="btn btn-blanco text-acento"><i class="bi bi-link-45deg"></i> Ligarme al coordinador</button>
      <?php if ($miCoord) { ?><button name="desligar" class="btn btn-blanco text-danger">Desligarme</button><?php } ?>
    </form>
  <?php } ?>

  <div class="text-end">
    <button form="perfil" name="datos" class="btn btn-primary"><i class="bi bi-save"></i> Guardar cambios</button>
  </div>
</div>
</main>
</body>
</html>
