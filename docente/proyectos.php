<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['borrar'])) {
  $p = (int) $_GET['borrar'];
  mysqli_query($con, "DELETE FROM proyecto WHERE id_proyecto = $p AND id_docente = $id");
  header('Location: proyectos.php');
  exit;
}

$pag = (int) ($_GET['pag'] ?? 1);
if ($pag < 1) $pag = 1;
$inicio = ($pag - 1) * 5;

$total     = contar("SELECT COUNT(*) FROM proyecto WHERE id_docente = $id");
$proyectos = mysqli_query($con, "SELECT * FROM proyecto WHERE id_docente = $id ORDER BY id_proyecto DESC LIMIT $inicio, 5");
$pagina = 'proyectos';
include 'menu.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="titulo">Mis proyectos</h1>
  <a href="proyecto_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo proyecto</a>
</div>

<?php if (mysqli_num_rows($proyectos) == 0) { ?>
  <div class="tarjeta vacio"><span class="icono"><i class="bi bi-folder"></i></span><p>Aún no tienes proyectos.</p></div>
<?php } ?>

<?php while ($p = mysqli_fetch_assoc($proyectos)) {
  $idp        = $p['id_proyecto'];
  $alumnos    = contar("SELECT COUNT(*) FROM alumno_proyecto WHERE id_proyecto = $idp");
  $tareas     = contar("SELECT COUNT(*) FROM tarea WHERE id_proyecto = $idp");
  $evidencias = contar("SELECT COUNT(*) FROM archivo a JOIN subtarea s ON s.id_subtarea = a.id_subtarea
                        JOIN tarea t ON t.id_tarea = s.id_tarea WHERE t.id_proyecto = $idp");
  $av = avance($idp);
?>
  <div class="tarjeta mb-3">
    <div class="d-flex justify-content-between">
      <h5 class="fw-bold mb-1"><?= $p['nombre'] ?></h5>
      <span class="etiqueta <?= $p['estado'] ?>">● <?= ucfirst($p['estado']) ?></span>
    </div>
    <p class="text-muted small">PROY-2026-<?= $idp ?> · Inicio: <?= fecha($p['fecha_inicio']) ?> · Fin estimado: <?= fecha($p['fecha_fin']) ?></p>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <span class="chip"><i class="bi bi-person"></i> <?= $alumnos ?> Alumnos</span>
      <span class="chip"><i class="bi bi-files"></i> <?= $tareas ?> Tareas</span>
      <span class="chip"><i class="bi bi-upload"></i> <?= $evidencias ?> Evidencias</span>
    </div>
    <div class="d-flex align-items-center gap-3 border-top pt-3">
      <strong class="small">Avance</strong>
      <div class="barra-avance flex-grow-1"><div style="width: <?= $av ?>%"></div></div>
      <strong class="small"><?= $av ?>%</strong>
    </div>
    <div class="text-end mt-3">
      <a href="proyecto_form.php?id=<?= $idp ?>" class="small text-dark fw-semibold">Ver detalles del proyecto <i class="bi bi-chevron-right"></i></a>
    </div>
  </div>
<?php } ?>

<?php paginacion($total, $pag, "proyectos.php?"); ?>
</main>
</body>
</html>
