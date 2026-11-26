<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['borrar'])) {
  $t = (int) $_GET['borrar'];
  mysqli_query($con, "DELETE t FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                      WHERE t.id_tarea = $t AND p.id_docente = $id");
  header('Location: tareas.php');
  exit;
}

$total     = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = $id");
$proyectos = mysqli_query($con, "SELECT * FROM proyecto WHERE id_docente = $id ORDER BY nombre");
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

<?php while ($p = mysqli_fetch_assoc($proyectos)) {
  $tareas = mysqli_query($con, "SELECT * FROM tarea WHERE id_proyecto = $p[id_proyecto] ORDER BY fecha_limite");
  if (mysqli_num_rows($tareas) == 0) continue;
?>
  <p class="fw-bold mt-4 mb-2"><span class="text-acento">●</span> <?= $p['nombre'] ?></p>
  <div class="tarjeta lista">
    <?php while ($t = mysqli_fetch_assoc($tareas)) { ?>
      <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="fila">
        <span><?= $t['titulo'] ?></span>
        <span class="d-flex gap-2">
          <span class="etiqueta <?= $t['prioridad'] ?>"><?= ucfirst($t['prioridad']) ?></span>
          <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
        </span>
      </a>
    <?php } ?>
  </div>
<?php } ?>
</main>
</body>
</html>
