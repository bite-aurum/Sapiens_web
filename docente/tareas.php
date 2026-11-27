<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['borrar'])) {
  $t = (int) $_GET['borrar'];
  mysqli_query($con, "DELETE FROM tarea WHERE id_tarea = $t AND id_proyecto IN (SELECT id_proyecto FROM proyecto WHERE id_docente = $id)");
  header('Location: tareas.php');
  exit;
}

$pag = (int) ($_GET['pag'] ?? 1);
if ($pag < 1) $pag = 1;
$inicio = ($pag - 1) * 5;

$total  = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = $id");
$tareas = mysqli_query($con, "SELECT t.*, p.nombre AS proyecto FROM tarea t
  JOIN proyecto p ON p.id_proyecto = t.id_proyecto
  WHERE p.id_docente = $id ORDER BY p.nombre, t.fecha_limite LIMIT $inicio, 5");

$pagina = 'tareas';
include 'menu.php';
?>
<div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
  <h1 class="titulo">Tareas</h1>
  <a href="tarea_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva tarea</a>
</div>

<?php if ($total == 0) { ?>
  <div class="tarjeta vacio"><span class="icono"><i class="bi bi-clipboard-check"></i></span><p>Aún no tienes tareas.</p></div>
<?php } ?>

<?php
$proyectoAnterior = '';
while ($t = mysqli_fetch_assoc($tareas)) {
?>
  <?php if ($t['proyecto'] != $proyectoAnterior) { ?>
    <p class="fw-bold mt-4 mb-2"><span class="text-acento">●</span> <?= $t['proyecto'] ?></p>
    <?php $proyectoAnterior = $t['proyecto']; ?>
  <?php } ?>

  <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="tarjeta fila mb-2">
    <span><?= $t['titulo'] ?></span>
    <span class="d-flex gap-2">
      <span class="etiqueta <?= $t['prioridad'] ?>"><?= ucfirst($t['prioridad']) ?></span>
      <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
    </span>
  </a>
<?php } ?>

<?php paginacion($total, $pag, "tareas.php?"); ?>
</main>
</body>
</html>
