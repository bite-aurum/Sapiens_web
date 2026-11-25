<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'coordinador') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$f      = $_GET['f'] ?? '';
$buscar = limpiar($_GET['buscar'] ?? '');
$doc    = (int) ($_GET['docente'] ?? 0);

$filtro = "";
if ($f == 'activo')     $filtro .= " AND p.estado = 'activo'";
if ($f == 'finalizado') $filtro .= " AND p.estado = 'finalizado'";
if ($f == 'retraso')    $filtro .= " AND p.fecha_fin < CURDATE() AND p.estado != 'finalizado'";
if ($buscar != '')      $filtro .= " AND p.nombre LIKE '%$buscar%'";
if ($doc > 0)           $filtro .= " AND p.id_docente = $doc";

$proyectos = mysqli_query($con, "SELECT p.*, d.nombre AS docente, d.apellido_paterno AS docente_ap FROM proyecto p
  JOIN docente d ON d.id_docente = p.id_docente
  WHERE d.id_coordinador = $id $filtro ORDER BY p.id_proyecto DESC");

$mios        = "FROM proyecto p JOIN docente d ON d.id_docente = p.id_docente WHERE d.id_coordinador = $id";
$total       = contar("SELECT COUNT(*) $mios");
$activos     = contar("SELECT COUNT(*) $mios AND p.estado = 'activo'");
$finalizados = contar("SELECT COUNT(*) $mios AND p.estado = 'finalizado'");
$conRetraso  = contar("SELECT COUNT(*) $mios AND p.fecha_fin < CURDATE() AND p.estado != 'finalizado'");
$cumplimiento = $total == 0 ? 0 : round($finalizados * 100 / $total);

$yo       = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM coordinador WHERE id_coordinador = $id"));
$docentes = mysqli_query($con, "SELECT id_docente, nombre, apellido_paterno FROM docente WHERE id_coordinador = $id");

$pagina = 'proyectos';
include 'menu.php';
?>
<span class="chip small">COORDINACIÓN ACADÉMICA</span>
<h1 class="titulo mt-2">Todos los proyectos</h1>
<p class="small text-muted mb-4"><?= $yo['nombre'] ?> · <span class="chip"><?= $yo['codigo_vinculo'] ?></span> · Ciclo lectivo activo <?= date('Y') ?></p>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Proyectos totales</small><div class="numero"><?= $total ?></div></div>
      <span class="icono"><i class="bi bi-folder-check"></i></span>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tarjeta dato d-flex justify-content-between align-items-center">
      <div><small>Tasa de cumplimiento</small><div class="numero"><?= $cumplimiento ?>%</div></div>
      <span class="icono"><i class="bi bi-graph-up-arrow"></i></span>
    </div>
  </div>
</div>

<div class="tarjeta py-2 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div class="segmento">
    <a href="proyectos.php" class="<?= $f == '' ? 'activo' : '' ?>">Todos (<?= $total ?>)</a>
    <a href="proyectos.php?f=activo" class="<?= $f == 'activo' ? 'activo' : '' ?>">Activos (<?= $activos ?>)</a>
    <a href="proyectos.php?f=finalizado" class="<?= $f == 'finalizado' ? 'activo' : '' ?>">Finalizados (<?= $finalizados ?>)</a>
    <a href="proyectos.php?f=retraso" class="<?= $f == 'retraso' ? 'activo' : '' ?>">Con retraso (<?= $conRetraso ?>)</a>
  </div>
  <form class="d-flex gap-2">
    <input type="hidden" name="f" value="<?= $f ?>">
    <input name="buscar" class="form-control form-control-sm" placeholder="Buscar proyectos..." value="<?= $buscar ?>">
    <select name="docente" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="0">Todos los docentes</option>
      <?php while ($d = mysqli_fetch_assoc($docentes)) { ?>
        <option value="<?= $d['id_docente'] ?>" <?= $doc == $d['id_docente'] ? 'selected' : '' ?>><?= $d['nombre'] . ' ' . $d['apellido_paterno'] ?></option>
      <?php } ?>
    </select>
  </form>
</div>

<div class="tarjeta lista">
  <?php if (mysqli_num_rows($proyectos) == 0) { ?>
    <div class="vacio"><span class="icono"><i class="bi bi-folder2-open"></i></span><h6 class="fw-bold">Sin proyectos registrados por tus docentes.</h6></div>
  <?php } ?>
  <?php while ($p = mysqli_fetch_assoc($proyectos)) {
    $av = avance($p['id_proyecto']);
    $tarde = $p['fecha_fin'] && $p['fecha_fin'] < date('Y-m-d') && $p['estado'] != 'finalizado';
  ?>
    <div class="fila">
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <strong><?= $p['nombre'] ?></strong>
          <span class="etiqueta <?= $tarde ? 'retraso' : $p['estado'] ?>"><?= $tarde ? 'Con retraso' : ucfirst($p['estado']) ?></span>
        </div>
        <small class="text-muted">PROY-2026-<?= $p['id_proyecto'] ?> · Prof. <?= $p['docente'] . ' ' . $p['docente_ap'] ?> · Fin: <?= fecha($p['fecha_fin']) ?></small>
        <div class="d-flex align-items-center gap-3 mt-2">
          <div class="barra-avance flex-grow-1"><div style="width: <?= $av ?>%"></div></div>
          <small class="fw-semibold"><?= $av ?>%</small>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
</main>
</body>
</html>
