<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_POST['enviar'])) {
  $alumno  = (int) $_POST['id_alumno'];
  $tipo    = limpiar($_POST['tipo']);
  $mensaje = limpiar($_POST['mensaje']);
  mysqli_query($con, "INSERT INTO notificacion (mensaje, tipo, id_alumno, id_docente) VALUES ('$mensaje', '$tipo', $alumno, $id)");
  header('Location: notificaciones.php?ok=1');
  exit;
}

$misProyectos = mysqli_query($con, "SELECT id_proyecto, nombre FROM proyecto WHERE id_docente = $id");
$idp = (int) ($_GET['p'] ?? 0);
if ($idp == 0) {
  $primero = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_proyecto FROM proyecto WHERE id_docente = $id LIMIT 1"));
  $idp = $primero ? $primero['id_proyecto'] : 0;
}
$alumnos = mysqli_query($con, "SELECT a.id_alumno, a.nombre, a.apellido_paterno, a.correo FROM alumno_proyecto ap
  JOIN alumno a ON a.id_alumno = ap.id_alumno WHERE ap.id_proyecto = $idp");

$enviadas = mysqli_query($con, "SELECT n.*, a.nombre, a.apellido_paterno FROM notificacion n
  JOIN alumno a ON a.id_alumno = n.id_alumno WHERE n.id_docente = $id ORDER BY n.fecha DESC");

$pagina = 'notificaciones';
include 'menu.php';
?>
<h1 class="titulo">Notificaciones</h1>
<p class="subtitulo mb-4">Envío de alertas y comunicados para proyectos y alumnos asignados.</p>
<?php if (isset($_GET['ok'])) { ?><div class="alert alert-success">Notificación enviada</div><?php } ?>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="tarjeta">
      <div class="titulo-seccion"><span><i class="bi bi-clock-history"></i> Enviadas</span><span class="chip"><?= mysqli_num_rows($enviadas) ?> registros</span></div>
      <?php if (mysqli_num_rows($enviadas) == 0) { ?>
        <div class="vista-previa vacio">
          <span class="icono bg-white"><i class="bi bi-envelope-check"></i></span>
          <p class="mb-1">Sin notificaciones enviadas</p>
          <small class="text-muted">Los comunicados y avisos que envíes a tus alumnos se registrarán aquí con su estado de lectura.</small>
        </div>
      <?php } ?>
      <?php while ($n = mysqli_fetch_assoc($enviadas)) { ?>
        <div class="caja bg-white mb-2">
          <div class="d-flex justify-content-between">
            <strong class="small"><?= $n['nombre'] . ' ' . $n['apellido_paterno'] ?></strong>
            <span class="etiqueta <?= $n['leida'] ? 'completada' : '' ?>"><?= $n['leida'] ? 'Leída' : 'No leída' ?></span>
          </div>
          <p class="small my-1"><?= $n['mensaje'] ?></p>
          <small class="text-muted"><?= ucfirst($n['tipo']) ?> · <?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></small>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="tarjeta">
      <div class="titulo-seccion"><span><i class="bi bi-send"></i> Enviar notificación</span><span class="normal text-muted">Campos con * son obligatorios</span></div>

      <form method="get">
        <label>Proyecto</label>
        <select name="p" class="form-select" onchange="this.form.submit()">
          <?php while ($p = mysqli_fetch_assoc($misProyectos)) { ?>
            <option value="<?= $p['id_proyecto'] ?>" <?= $p['id_proyecto'] == $idp ? 'selected' : '' ?>>PROY-2026-<?= $p['id_proyecto'] ?> — <?= $p['nombre'] ?></option>
          <?php } ?>
        </select>
        <small class="text-muted"><i class="bi bi-info-circle"></i> Los alumnos disponibles son del proyecto seleccionado</small>
      </form>

      <form method="post" class="mt-3">
        <label>Alumno destinatario <span class="text-danger">*</span></label>
        <select name="id_alumno" class="form-select mb-3" required>
          <?php while ($a = mysqli_fetch_assoc($alumnos)) { ?>
            <option value="<?= $a['id_alumno'] ?>"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?> — <?= $a['correo'] ?></option>
          <?php } ?>
        </select>
        <label>Tipo <span class="text-danger">*</span></label>
        <select name="tipo" class="form-select mb-3">
          <option value="alerta">Alerta</option>
          <option value="proyecto">Proyecto</option>
          <option value="tarea">Tarea</option>
          <option value="general">General</option>
        </select>
        <label>Mensaje <span class="text-danger">*</span></label>
        <textarea name="mensaje" class="form-control mb-3" rows="4" placeholder="Escribe el mensaje de la notificación..." required></textarea>
        <label>Fecha de envío</label>
        <input class="form-control" value="<?= date('d/m/Y h:i a') ?>" readonly>
        <small class="text-muted"><i class="bi bi-check-circle"></i> Se envía automáticamente al guardar</small>
        <button name="enviar" class="btn btn-primary w-100 mt-3"><i class="bi bi-send"></i> Enviar notificación</button>
      </form>
    </div>
  </div>
</div>
</main>
</body>
</html>
