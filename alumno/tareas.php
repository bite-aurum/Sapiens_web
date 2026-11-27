<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$estado = limpiar($_GET['estado'] ?? '');
$filtro = $estado != '' ? "AND t.estado = '$estado'" : "";

$pag = (int) ($_GET['pag'] ?? 1);
if ($pag < 1) $pag = 1;
$inicio = ($pag - 1) * 5;

$total  = contar("SELECT COUNT(*) FROM tarea t JOIN alumno_proyecto ap ON ap.id_proyecto = t.id_proyecto
                  WHERE ap.id_alumno = $id $filtro");
$tareas = mysqli_query($con, "SELECT t.*, p.nombre AS proyecto FROM tarea t
  JOIN alumno_proyecto ap ON ap.id_proyecto = t.id_proyecto
  JOIN proyecto p ON p.id_proyecto = t.id_proyecto
  WHERE ap.id_alumno = $id $filtro ORDER BY t.fecha_limite LIMIT $inicio, 5");

$pagina = 'tareas';
include 'menu.php';
?>
<h1 class="titulo mb-3">Mis tareas</h1>
<div class="filtros mb-3">
  <a href="tareas.php" class="<?= $estado == '' ? 'activo' : '' ?>">Todas</a>
  <a href="tareas.php?estado=pendiente" class="<?= $estado == 'pendiente' ? 'activo' : '' ?>">Pendientes</a>
  <a href="tareas.php?estado=en%20progreso" class="<?= $estado == 'en progreso' ? 'activo' : '' ?>">En progreso</a>
  <a href="tareas.php?estado=completada" class="<?= $estado == 'completada' ? 'activo' : '' ?>">Completadas</a>
</div>

<div class="tarjeta lista">
  <?php if (mysqli_num_rows($tareas) == 0) { ?>
    <div class="vacio"><span class="icono"><i class="bi bi-clipboard-check"></i></span><p>No hay tareas aquí.</p></div>
  <?php } ?>
  <?php while ($t = mysqli_fetch_assoc($tareas)) { ?>
    <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="fila">
      <div>
        <strong><?= $t['titulo'] ?></strong><br>
        <small class="text-muted"><?= $t['proyecto'] ?> · Vence <?= fecha($t['fecha_limite']) ?></small>
      </div>
      <span class="d-flex gap-2">
        <span class="etiqueta <?= $t['prioridad'] ?>"><?= ucfirst($t['prioridad']) ?></span>
        <span class="etiqueta <?= str_replace(' ', '-', $t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
      </span>
    </a>
  <?php } ?>
</div>

<?php paginacion($total, $pag, "tareas.php?estado=$estado&"); ?>
</main>
</body>
</html>
