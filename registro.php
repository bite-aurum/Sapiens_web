<?php
include 'conexion.php';
include 'funciones.php';
$msg = "";
$ok  = false;

if ($_POST) {
  $rol    = $_POST['rol'];
  $nombre = limpiar($_POST['nombre']);
  $ap     = limpiar($_POST['apellido_paterno']);
  $am     = limpiar($_POST['apellido_materno']);
  $correo = limpiar($_POST['correo']);
  $pass   = $_POST['pass'];
  $pass2  = $_POST['pass2'];
  $clave  = limpiar($_POST['clave']);

  $existe = contar("SELECT
      (SELECT COUNT(*) FROM alumno WHERE correo = '$correo') +
      (SELECT COUNT(*) FROM docente WHERE correo = '$correo') +
      (SELECT COUNT(*) FROM coordinador WHERE correo = '$correo') +
      (SELECT COUNT(*) FROM administrador WHERE correo = '$correo')");

  if ($pass != $pass2) {
    $msg = "Las contraseñas no coinciden";
  } elseif ($existe > 0) {
    $msg = "Ese correo ya está registrado";
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    if ($rol == 'alumno') {
      mysqli_query($con, "INSERT INTO alumno (nombre, apellido_paterno, apellido_materno, correo, contrasena)
                          VALUES ('$nombre', '$ap', '$am', '$correo', '$hash')");
    }

    if ($rol == 'coordinador') {
      $codigo = 'SAP-' . strtoupper(substr(md5(time()), 0, 5));
      mysqli_query($con, "INSERT INTO coordinador (nombre, apellido_paterno, apellido_materno, correo, contrasena, codigo_vinculo)
                          VALUES ('$nombre', '$ap', '$am', '$correo', '$hash', '$codigo')");
    }

    if ($rol == 'docente') {
      $coord = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_coordinador FROM coordinador WHERE codigo_vinculo = '$clave'"));
      if (!$coord) {
        $msg = "La clave del coordinador no existe";
      } else {
        mysqli_query($con, "INSERT INTO docente (nombre, apellido_paterno, apellido_materno, correo, contrasena, id_coordinador)
                            VALUES ('$nombre', '$ap', '$am', '$correo', '$hash', $coord[id_coordinador])");
      }
    }

    if ($msg == "") {
      $msg = "Cuenta creada, ya puedes iniciar sesión";
      $ok  = true;
    }
  }
}

$titulo = "Registrarme";
include 'header.php';
?>
  <main class="registro-fondo">
    <form class="login" method="post">
      <h2>Crear cuenta</h2>
      <p class="sub">Llena tus datos para registrarte</p>

      <label>Soy</label>
      <select name="rol">
        <option value="alumno">Alumno</option>
        <option value="docente">Docente</option>
        <option value="coordinador">Coordinador</option>
      </select>

      <label>Nombre(s)</label>
      <input type="text" name="nombre" placeholder="Tu nombre" required>

      <label>Apellido paterno</label>
      <input type="text" name="apellido_paterno" required>

      <label>Apellido materno</label>
      <input type="text" name="apellido_materno">

      <label>Correo institucional</label>
      <input type="text" name="correo" placeholder="tucorreo@uthh.edu.mx" required>

      <label>Contraseña</label>
      <input type="password" name="pass" placeholder="••••••••••" required>

      <label>Confirmar contraseña</label>
      <input type="password" name="pass2" placeholder="••••••••••" required>

      <label>Clave del coordinador (solo docentes)</label>
      <input type="text" name="clave" placeholder="SAP-XXXXX">

      <?php if ($msg) { ?>
        <p class="mensaje <?= $ok ? 'ok' : 'error' ?>"><?= $msg ?></p>
      <?php } ?>
      <button type="submit" class="btn btn-primario">Registrarme</button>

      <p class="registro">¿Ya tienes cuenta? <a href="index.php#login">Inicia sesión</a></p>
    </form>
  </main>
<?php include 'footer.php'; ?>
