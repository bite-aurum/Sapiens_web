<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$idt = (int) $_GET['id'];

if (isset($_GET['estado'])) {
  $s = (int) $_GET['sub'];
  $e = limpiar($_GET['estado']);
  mysqli_query($con, "UPDATE subtarea SET estado = '$e' WHERE id_subtarea = $s");
  header("Location: tarea.php?id=$idt");
  exit;
}

$t = mysqli_fetch_assoc(mysqli_query($con, "SELECT t.*, p.nombre AS proyecto FROM tarea t
  JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE t.id_tarea = $idt"));
$subtareas = mysqli_query($con, "SELECT s.*, a.nombre, a.apellido_paterno FROM subtarea s
  LEFT JOIN alumno_subtarea x ON x.id_subtarea = s.id_subtarea
  LEFT JOIN alumno a ON a.id_alumno = x.id_alumno
  WHERE s.id_tarea = $idt");
$total  = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $idt");
$hechas = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $idt AND estado = 'completada'");
$av = $total == 0 ? 0 : round($hechas * 100 / $total);
$equipo = mysqli_query($con, "SELECT a.nombre, a.apellido_paterno, ap.rol FROM alumno_proyecto ap
  JOIN alumno a ON a.id_alumno = ap.id_alumno WHERE ap.id_proyecto = $t[id_proyecto]");

$pagina = 'tareas';
include 'menu.php';
?>
<a href="tareas.php" class="fw-semibold small"><i class="bi bi-chevron-left"></i> Volver a Tareas</a>
<div class="d-flex justify-content-between align-items-center mt-3">
  <h1 class="titulo"><?= $t['titulo'] ?></h1>
  <a href="tarea_form.php?id=<?= $idt ?>" class="btn btn-blanco btn-sm"><i class="bi bi-pencil"></i> Editar tarea</a>
</div>
<p class="mt-2 mb-4">
  <span class="etiqueta <?= $t['prioridad'] ?>"><?= ucfirst($t['prioridad']) ?></span>
  <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
  <span class="text-muted small"><?= $t['proyecto'] ?> · Vence <?= fecha($t['fecha_limite']) ?></span>
</p>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="tarjeta mb-4">
      <small class="text-muted text-uppercase fw-semibold">Descripción</small>
      <p class="mt-2 mb-0"><?= nl2br($t['descripcion']) ?></p>
    </div>

    <h5 class="fw-semibold mb-3">Subtareas y evidencias</h5>
    <?php if ($total == 0) { ?>
      <div class="tarjeta text-muted">Esta tarea no tiene subtareas. <a href="tarea_form.php?id=<?= $idt ?>">Agregar subtareas</a></div>
    <?php } ?>
    <?php while ($s = mysqli_fetch_assoc($subtareas)) {
      $ids = $s['id_subtarea'];
      $archivos = mysqli_query($con, "SELECT * FROM archivo WHERE id_subtarea = $ids");
    ?>
      <div class="tarjeta mb-3">
        <div class="d-flex justify-content-between">
          <div>
            <strong><?= $s['titulo'] ?></strong><br>
            <small class="text-muted"><?= $s['nombre'] ? $s['nombre'] . ' ' . $s['apellido_paterno'] : 'Sin alumno asignado' ?></small>
          </div>
          <span class="etiqueta <?= $s['estado'] ?>"><?= ucfirst($s['estado']) ?></span>
        </div>

        <?php if (mysqli_num_rows($archivos) == 0) { ?>
          <p class="text-muted small mt-3 mb-0">Aún no se ha subido evidencia.</p>
        <?php } ?>
        <?php while ($a = mysqli_fetch_assoc($archivos)) {
          $ruta = "../" . $a['url'];
          $kb = file_exists($ruta) ? round(filesize($ruta) / 1024) : 0;
        ?>
          <div class="caja d-flex align-items-center gap-3 mt-3">
            <span class="icono bg-white"><i class="bi bi-file-earmark-text"></i></span>
            <div class="flex-grow-1">
              <strong class="small"><?= $a['nombre_archivo'] ?></strong><br>
              <small class="text-muted"><?= strtoupper(pathinfo($a['nombre_archivo'], PATHINFO_EXTENSION)) ?> · <?= $kb ?> KB · subido el <?= fecha($a['fecha_subida']) ?></small>
            </div>
            <a href="<?= $ruta ?>" target="_blank" class="btn btn-blanco btn-sm"><i class="bi bi-eye"></i> Ver</a>
            <a href="<?= $ruta ?>" download class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Descargar</a>
          </div>
        <?php } ?>

        <div class="text-end mt-3">
          <?php if ($s['estado'] == 'completada') { ?>
            <a href="tarea.php?id=<?= $idt ?>&sub=<?= $ids ?>&estado=pendiente" class="small">Marcar como pendiente</a>
          <?php } else { ?>
            <a href="tarea.php?id=<?= $idt ?>&sub=<?= $ids ?>&estado=completada" class="small"><i class="bi bi-check-lg"></i> Marcar como completada</a>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="col-lg-4">
    <div class="tarjeta mb-3">
      <small class="text-muted text-uppercase fw-semibold">Avance</small>
      <p class="mt-2 mb-2"><?= $hechas ?> de <?= $total ?> subtareas completadas</p>
      <div class="barra-avance"><div style="width: <?= $av ?>%"></div></div>
      <strong class="text-acento small"><?= $av ?>%</strong>
    </div>
    <div class="tarjeta">
      <small class="text-muted text-uppercase fw-semibold">Equipo</small>
      <?php while ($a = mysqli_fetch_assoc($equipo)) { ?>
        <div class="d-flex justify-content-between mt-2 small">
          <span><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?></span>
          <span class="text-muted"><?= ucfirst($a['rol']) ?></span>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
</main>
</body>
</html>
