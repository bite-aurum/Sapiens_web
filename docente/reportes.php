<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['descargar'])) {
  $r   = (int) $_GET['descargar'];
  $rep = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM reporte WHERE id_reporte = $r AND id_docente = $id"));
  header('Content-Type: text/plain; charset=utf-8');
  header("Content-Disposition: attachment; filename=reporte_$r.txt");
  echo "Reporte de " . $rep['tipo'] . " - " . fecha($rep['fecha']) . "\n\n" . $rep['contenido'];
  exit;
}

if (isset($_POST['generar'])) {
  $tipo = limpiar($_POST['tipo']);
  $idp  = (int) $_POST['id_proyecto'];

  if ($idp > 0) {
    $proyectos = mysqli_query($con, "SELECT * FROM proyecto WHERE id_proyecto = $idp");
  } else {
    $proyectos = mysqli_query($con, "SELECT * FROM proyecto WHERE id_docente = $id");
  }

  $texto = "";
  while ($p = mysqli_fetch_assoc($proyectos)) {
    $tareas = contar("SELECT COUNT(*) FROM tarea WHERE id_proyecto = $p[id_proyecto]");
    $texto .= $p['nombre'] . ": " . avance($p['id_proyecto']) . "% (" . $tareas . " tareas)\n";
  }
  $texto  = limpiar($texto);
  $idpSql = $idp > 0 ? $idp : 'NULL';
  mysqli_query($con, "INSERT INTO reporte (tipo, contenido, id_docente, id_proyecto) VALUES ('$tipo', '$texto', $id, $idpSql)");
  header('Location: reportes.php?ver=' . mysqli_insert_id($con));
  exit;
}

$misProyectos = mysqli_query($con, "SELECT id_proyecto, nombre FROM proyecto WHERE id_docente = $id");
$historial    = mysqli_query($con, "SELECT * FROM reporte WHERE id_docente = $id ORDER BY id_reporte DESC");
$ver = null;
if (isset($_GET['ver'])) {
  $v   = (int) $_GET['ver'];
  $ver = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM reporte WHERE id_reporte = $v AND id_docente = $id"));
}

$pagina = 'reportes';
include 'menu.php';
?>
<h1 class="titulo">Reportes</h1>
<p class="subtitulo mb-4">Generación y descarga de informes de avance y rendimiento académico.</p>

<div class="row g-4">
  <div class="col-lg-6">
    <form method="post" class="tarjeta mb-4">
      <div class="titulo-seccion">Generar nuevo reporte</div>
      <label>Tipo de reporte</label>
      <select name="tipo" class="form-select mb-3"><option value="avance">Avance por proyecto</option></select>
      <label>Proyecto</label>
      <select name="id_proyecto" class="form-select mb-3">
        <option value="0">Todos mis proyectos</option>
        <?php while ($p = mysqli_fetch_assoc($misProyectos)) { ?>
          <option value="<?= $p['id_proyecto'] ?>"><?= $p['nombre'] ?></option>
        <?php } ?>
      </select>
      <label>Fecha de generación</label>
      <input class="form-control mb-3" value="<?= date('d/m/Y') ?>" readonly>
      <button name="generar" class="btn btn-blanco w-100"><i class="bi bi-bar-chart"></i> Generar reporte</button>
    </form>

    <div class="tarjeta">
      <div class="titulo-seccion">Vista previa</div>
      <div class="vista-previa mb-3">
        <?php if ($ver) { ?>
          <pre class="m-0"><?= $ver['contenido'] ?></pre>
        <?php } else { ?>
          <div class="text-center text-muted small pt-4"><i class="bi bi-file-earmark-bar-graph fs-3"></i><br>Previsualización disponible al generar un informe</div>
        <?php } ?>
      </div>
      <?php if ($ver) { ?>
        <a href="reportes.php?descargar=<?= $ver['id_reporte'] ?>" class="btn btn-primary w-100"><i class="bi bi-download"></i> Descargar reporte</a>
      <?php } else { ?>
        <button class="btn btn-primary w-100" disabled><i class="bi bi-download"></i> Descargar reporte</button>
      <?php } ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="tarjeta">
      <div class="titulo-seccion">Historial de reportes <span class="normal text-muted">Total: <?= mysqli_num_rows($historial) ?></span></div>
      <?php if (mysqli_num_rows($historial) == 0) { ?>
        <div class="caja bg-white">
          <p class="mb-1">Sin reportes generados.</p>
          <small class="text-muted">Los reportes creados durante el período académico se listarán aquí para consulta y descarga posterior.</small>
        </div>
      <?php } ?>
      <?php while ($r = mysqli_fetch_assoc($historial)) { ?>
        <div class="caja bg-white d-flex justify-content-between align-items-center mb-2">
          <div><strong class="small">Reporte #<?= $r['id_reporte'] ?> · <?= ucfirst($r['tipo']) ?></strong><br><small class="text-muted"><?= fecha($r['fecha']) ?></small></div>
          <div class="d-flex gap-2">
            <a href="reportes.php?ver=<?= $r['id_reporte'] ?>" class="btn btn-blanco btn-sm"><i class="bi bi-eye"></i></a>
            <a href="reportes.php?descargar=<?= $r['id_reporte'] ?>" class="btn btn-blanco btn-sm"><i class="bi bi-download"></i></a>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
</main>
</body>
</html>
