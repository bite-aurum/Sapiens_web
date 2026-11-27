<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id  = $_SESSION['id'];
$idt = (int) $_GET['id'];
$msg = "";

$t = mysqli_fetch_assoc(mysqli_query($con, "SELECT t.*, p.nombre AS proyecto, d.nombre AS docente, d.apellido_paterno AS docente_ap
  FROM tarea t
  JOIN proyecto p ON p.id_proyecto = t.id_proyecto
  JOIN docente d ON d.id_docente = p.id_docente
  JOIN alumno_proyecto ap ON ap.id_proyecto = p.id_proyecto
  WHERE t.id_tarea = $idt AND ap.id_alumno = $id"));
if (!$t) { header('Location: tareas.php'); exit; }

if (isset($_GET['marcar'])) {
  $s = (int) $_GET['marcar'];
  $mia = contar("SELECT COUNT(*) FROM alumno_subtarea WHERE id_subtarea = $s AND id_alumno = $id");
  if ($mia > 0) {
    $sub = mysqli_fetch_assoc(mysqli_query($con, "SELECT estado FROM subtarea WHERE id_subtarea = $s"));
    if ($sub['estado'] == 'completada') {
      $nuevo = 'pendiente';
    } else {
      $nuevo = 'completada';
    }
    mysqli_query($con, "UPDATE subtarea SET estado = '$nuevo' WHERE id_subtarea = $s");
  }
  header("Location: tarea.php?id=$idt");
  exit;
}

if (isset($_POST['subir'])) {
  $ids     = (int) $_POST['id_subtarea'];
  $nombre  = basename($_FILES['archivo']['name']);
  $ext     = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
  $validas = ['pdf', 'zip', 'sql', 'docx', 'png', 'jpg'];

  if (!in_array($ext, $validas)) {
    $msg = "Solo se permiten archivos PDF, ZIP, SQL, DOCX, PNG o JPG";
  } elseif ($_FILES['archivo']['size'] > 10 * 1024 * 1024) {
    $msg = "El archivo pesa más de 10 MB";
  } else {
    $url = "uploads/" . time() . "_" . $nombre;
    move_uploaded_file($_FILES['archivo']['tmp_name'], "../$url");
    $nombre = limpiar($nombre);
    $tipo   = limpiar($_FILES['archivo']['type']);
    mysqli_query($con, "INSERT INTO archivo (nombre_archivo, url, tipo_mime, id_alumno, id_subtarea)
                        VALUES ('$nombre', '$url', '$tipo', $id, $ids)");
    header("Location: tarea.php?id=$idt");
    exit;
  }
}

$subtareas = mysqli_query($con, "SELECT s.*, x.id_alumno FROM subtarea s
  LEFT JOIN alumno_subtarea x ON x.id_subtarea = s.id_subtarea AND x.id_alumno = $id
  WHERE s.id_tarea = $idt");
$total  = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $idt");
$hechas = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $idt AND estado = 'completada'");
$av = $total == 0 ? 0 : round($hechas * 100 / $total);
$mias = mysqli_query($con, "SELECT s.* FROM subtarea s JOIN alumno_subtarea x ON x.id_subtarea = s.id_subtarea
  WHERE s.id_tarea = $idt AND x.id_alumno = $id");
$misArchivos = mysqli_query($con, "SELECT ar.* FROM archivo ar JOIN subtarea s ON s.id_subtarea = ar.id_subtarea
  WHERE s.id_tarea = $idt AND ar.id_alumno = $id");
$entregada = mysqli_num_rows($misArchivos) > 0;

$pagina = 'tareas';
include 'menu.php';
?>
<div class="angosto" style="max-width: 860px">
  <a href="tareas.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
  <h1 class="titulo"><?= $t['titulo'] ?></h1>
  <p class="text-muted mb-4">Proyecto: PROY-2026-<?= $t['id_proyecto'] ?> · <?= $t['proyecto'] ?> &nbsp;•&nbsp; Docente: Prof. <?= $t['docente'] . ' ' . $t['docente_ap'] ?></p>

  <div class="tarjeta mb-4">
    <div class="d-flex flex-wrap justify-content-between gap-2 border-bottom pb-3 mb-3">
      <span>
        <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
        <span class="etiqueta <?= $t['prioridad'] ?>">Prioridad <?= ucfirst($t['prioridad']) ?></span>
      </span>
      <small class="text-muted">Asignado: <?= fecha($t['fecha_asignacion']) ?> &nbsp;•&nbsp; Fecha límite: <strong class="text-danger"><?= fecha($t['fecha_limite']) ?></strong></small>
    </div>
    <small class="text-muted text-uppercase fw-semibold">Descripción de la actividad</small>
    <p class="mt-2"><?= nl2br($t['descripcion']) ?></p>
    <?php if ($t['criterios']) { ?>
      <small class="text-muted text-uppercase fw-semibold">Criterios de aceptación</small>
      <?php foreach (explode("\n", $t['criterios']) as $c) { if (trim($c) == '') continue; ?>
        <p class="mb-1 mt-2"><i class="bi bi-check-lg text-acento"></i> <?= $c ?></p>
      <?php } ?>
    <?php } ?>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="tarjeta h-100">
        <div class="titulo-seccion">Subtareas <span class="chip normal"><?= $hechas ?> de <?= $total ?> completadas</span></div>
        <?php while ($s = mysqli_fetch_assoc($subtareas)) {
          $hecha = $s['estado'] == 'completada';
          $icono = $hecha ? 'bi-check-square-fill text-dark' : 'bi-square text-muted';
        ?>
          <p class="mb-3">
            <?php if ($s['id_alumno']) { ?>
              <a href="tarea.php?id=<?= $idt ?>&marcar=<?= $s['id_subtarea'] ?>"><i class="bi <?= $icono ?>"></i></a>
            <?php } else { ?>
              <i class="bi <?= $icono ?>"></i>
            <?php } ?>
            <span class="<?= $hecha ? 'text-decoration-line-through text-muted' : '' ?>"><?= $s['titulo'] ?></span>
          </p>
        <?php } ?>
        <div class="d-flex justify-content-between border-top pt-3 small text-muted">
          <span>Progreso de pasos</span><span><?= $av ?>%</span>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <form method="post" enctype="multipart/form-data" class="tarjeta h-100">
        <div class="titulo-seccion">Subir evidencia
          <span class="etiqueta <?= $entregada ? 'entregada' : 'en-progreso' ?> normal"><?= $entregada ? 'Entregada' : 'Pendiente' ?></span>
        </div>
        <?php if ($msg) { ?><div class="alert alert-danger py-2"><?= $msg ?></div><?php } ?>
        <?php if (mysqli_num_rows($mias) == 0) { ?>
          <p class="text-muted small">No tienes subtareas asignadas en esta tarea.</p>
        <?php } else { ?>
          <select name="id_subtarea" class="form-select mb-3">
            <?php while ($s = mysqli_fetch_assoc($mias)) { ?>
              <option value="<?= $s['id_subtarea'] ?>"><?= $s['titulo'] ?></option>
            <?php } ?>
          </select>
          <div class="zona-archivo mb-3">
            <span class="icono bg-white mb-2"><i class="bi bi-cloud-upload"></i></span>
            <p class="fw-semibold small mb-0">Subir archivo o documento</p>
            <small class="text-muted">PDF, ZIP o SQL hasta 10 MB</small>
            <input type="file" name="archivo" class="form-control form-control-sm mt-3" required>
          </div>
          <button name="subir" class="btn btn-primary w-100"><i class="bi bi-send"></i> Subir evidencia</button>
        <?php } ?>
        <?php while ($a = mysqli_fetch_assoc($misArchivos)) { ?>
          <p class="small mt-3 mb-0"><i class="bi bi-file-earmark-text text-muted"></i> <a href="../<?= $a['url'] ?>" target="_blank"><?= $a['nombre_archivo'] ?></a> <span class="text-muted">· <?= fecha($a['fecha_subida']) ?></span></p>
        <?php } ?>
      </form>
    </div>
  </div>
</div>
</main>
</body>
</html>
