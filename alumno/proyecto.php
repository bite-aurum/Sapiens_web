<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id  = $_SESSION['id'];
$idp = (int) $_GET['id'];

$p = mysqli_fetch_assoc(mysqli_query($con, "SELECT p.*, d.nombre AS docente, d.apellido_paterno AS docente_ap
  FROM proyecto p
  JOIN alumno_proyecto ap ON ap.id_proyecto = p.id_proyecto
  JOIN docente d ON d.id_docente = p.id_docente
  WHERE p.id_proyecto = $idp AND ap.id_alumno = $id"));
if (!$p) { header('Location: proyectos.php'); exit; }

$av     = avance($idp);
$equipo = mysqli_query($con, "SELECT a.id_alumno, a.nombre, a.apellido_paterno, ap.rol FROM alumno_proyecto ap
  JOIN alumno a ON a.id_alumno = ap.id_alumno WHERE ap.id_proyecto = $idp");
$evidencias = mysqli_query($con, "SELECT ar.* FROM archivo ar
  JOIN subtarea s ON s.id_subtarea = ar.id_subtarea
  JOIN tarea t ON t.id_tarea = s.id_tarea WHERE t.id_proyecto = $idp");
$tareas = mysqli_query($con, "SELECT * FROM tarea WHERE id_proyecto = $idp ORDER BY fecha_limite");

$pagina = 'proyectos';
include 'menu.php';
?>
<a href="proyectos.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
<h1 class="titulo mb-4"><?= $p['nombre'] ?></h1>

<div class="tarjeta mb-4">
  <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
    <small class="text-muted">PROY-2026-<?= $idp ?> · Prof. <?= $p['docente'] . ' ' . $p['docente_ap'] ?> · Inicio: <?= fecha($p['fecha_inicio']) ?> · Fin estimado: <?= fecha($p['fecha_fin']) ?></small>
    <span class="etiqueta <?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span>
  </div>
  <p class="small mb-2"><strong class="me-3">Problemática:</strong> <?= $p['problematica'] ?></p>
  <p class="small"><strong class="me-4">Objetivos:</strong> <?= $p['objetivos'] ?></p>
  <div class="d-flex align-items-center gap-3">
    <small class="text-muted">Avance general</small>
    <div class="barra-avance flex-grow-1"><div style="width: <?= $av ?>%"></div></div>
    <small class="fw-semibold"><?= $av ?>%</small>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="tarjeta h-100">
      <div class="titulo-seccion">Equipo</div>
      <?php while ($a = mysqli_fetch_assoc($equipo)) { ?>
        <div class="caja bg-white d-flex align-items-center gap-3 mb-2">
          <span class="avatar verde"><?= iniciales($a['nombre']) ?></span>
          <strong class="small flex-grow-1"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?><?= $a['id_alumno'] == $id ? ' - Tú' : '' ?></strong>
          <span class="etiqueta <?= $a['rol'] ?>"><?= $a['rol'] == 'lider' ? 'Líder' : 'Miembro' ?></span>
        </div>
      <?php } ?>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="tarjeta mb-4">
      <div class="titulo-seccion">Evidencias subidas</div>
      <?php if (mysqli_num_rows($evidencias) == 0) { ?><p class="small text-muted m-0">Sin evidencias.</p><?php } ?>
      <?php while ($e = mysqli_fetch_assoc($evidencias)) { ?>
        <p class="small mb-2"><i class="bi bi-file-earmark-text text-muted"></i> <a href="../<?= $e['url'] ?>" target="_blank"><?= $e['nombre_archivo'] ?></a> <span class="text-muted">· <?= fecha($e['fecha_subida']) ?></span></p>
      <?php } ?>
    </div>
    <div class="tarjeta">
      <div class="titulo-seccion">Tareas</div>
      <?php if (mysqli_num_rows($tareas) == 0) { ?><p class="small text-muted m-0">Sin tareas.</p><?php } ?>
      <?php while ($t = mysqli_fetch_assoc($tareas)) { ?>
        <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="d-flex justify-content-between text-dark small mb-2">
          <span><?= $t['titulo'] ?></span>
          <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
        </a>
      <?php } ?>
    </div>
  </div>
</div>
</main>
</body>
</html>
