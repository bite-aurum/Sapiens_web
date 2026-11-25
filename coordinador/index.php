<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'coordinador') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$proyectos = contar("SELECT COUNT(*) FROM proyecto p JOIN docente d ON d.id_docente = p.id_docente WHERE d.id_coordinador = $id");
$docentes  = contar("SELECT COUNT(*) FROM docente WHERE id_coordinador = $id AND activo = 1");
$tareas    = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                     JOIN docente d ON d.id_docente = p.id_docente WHERE d.id_coordinador = $id");
$vencidas  = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                     JOIN docente d ON d.id_docente = p.id_docente
                     WHERE d.id_coordinador = $id AND t.fecha_limite < CURDATE() AND t.estado != 'completada'");
$aTiempo   = $tareas == 0 ? 100 : round(($tareas - $vencidas) * 100 / $tareas);
$retraso   = contar("SELECT COUNT(*) FROM proyecto p JOIN docente d ON d.id_docente = p.id_docente
                     WHERE d.id_coordinador = $id AND p.fecha_fin < CURDATE() AND p.estado != 'finalizado'");

$listaDocentes  = mysqli_query($con, "SELECT * FROM docente WHERE id_coordinador = $id");
$listaProyectos = mysqli_query($con, "SELECT p.*, d.nombre AS docente FROM proyecto p
  JOIN docente d ON d.id_docente = p.id_docente WHERE d.id_coordinador = $id");

$pagina = 'inicio';
include 'menu.php';
?>
<h1 class="titulo mb-4">Panel de coordinación</h1>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Total proyectos</small><div class="numero"><?= $proyectos ?></div></div>
      <span class="icono verde"><i class="bi bi-folder"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Docentes activos</small><div class="numero"><?= $docentes ?></div></div>
      <span class="icono verde"><i class="bi bi-people"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Tareas a tiempo</small><div class="numero"><?= $aTiempo ?>%</div></div>
      <span class="icono verde"><i class="bi bi-speedometer2"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Proyectos con retraso</small><div class="numero"><?= $retraso ?></div></div>
      <span class="icono amarillo"><i class="bi bi-exclamation-triangle"></i></span>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="tarjeta h-100">
      <div class="titulo-seccion justify-content-start"><span class="text-acento">●</span> Rendimiento por docente</div>
      <?php if (mysqli_num_rows($listaDocentes) == 0) { ?>
        <div class="vacio"><span class="icono"><i class="bi bi-people"></i></span><p>Sin docentes registrados.</p></div>
      <?php } ?>
      <?php while ($d = mysqli_fetch_assoc($listaDocentes)) {
        $idd    = $d['id_docente'];
        $total  = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = $idd");
        $hechas = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = $idd AND t.estado = 'completada'");
        $pct    = $total == 0 ? 0 : round($hechas * 100 / $total);
      ?>
        <div class="d-flex align-items-center gap-3 mb-3">
          <span class="avatar"><?= iniciales($d['nombre']) ?></span>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between small"><strong><?= $d['nombre'] . ' ' . $d['apellido_paterno'] ?></strong><span><?= $pct ?>%</span></div>
            <div class="barra-avance mt-1"><div style="width: <?= $pct ?>%"></div></div>
            <small class="text-muted"><?= $hechas ?> de <?= $total ?> tareas completadas</small>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="tarjeta h-100">
      <div class="titulo-seccion justify-content-start"><span class="text-acento">●</span> Avance por proyecto</div>
      <?php if (mysqli_num_rows($listaProyectos) == 0) { ?>
        <div class="vacio"><span class="icono"><i class="bi bi-bar-chart"></i></span><p>Sin proyectos registrados.</p></div>
      <?php } ?>
      <?php while ($p = mysqli_fetch_assoc($listaProyectos)) { $av = avance($p['id_proyecto']); ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between small"><strong><?= $p['nombre'] ?></strong><span><?= $av ?>%</span></div>
          <div class="barra-avance mt-1"><div style="width: <?= $av ?>%"></div></div>
          <small class="text-muted">Prof. <?= $p['docente'] ?></small>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
</main>
</body>
</html>
