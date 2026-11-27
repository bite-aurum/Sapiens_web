<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$proyectos  = contar("SELECT COUNT(*) FROM proyecto WHERE id_docente = $id AND estado = 'activo'");
$tareas     = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                      WHERE p.id_docente = $id AND t.estado != 'completada'");
$evidencias = contar("SELECT COUNT(*) FROM archivo a
                      JOIN subtarea s ON s.id_subtarea = a.id_subtarea
                      JOIN tarea t ON t.id_tarea = s.id_tarea
                      JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                      WHERE p.id_docente = $id AND s.estado != 'completada'");
$alumnos    = contar("SELECT COUNT(DISTINCT ap.id_alumno) FROM alumno_proyecto ap
                      JOIN proyecto p ON p.id_proyecto = ap.id_proyecto WHERE p.id_docente = $id");

$pag = (int) ($_GET['pag'] ?? 1);
if ($pag < 1) $pag = 1;
$inicio = ($pag - 1) * 5;

$totalRecientes = contar("SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto
                          WHERE p.id_docente = $id AND t.estado != 'completada'");
$recientes = mysqli_query($con, "SELECT t.*, p.nombre AS proyecto FROM tarea t
  JOIN proyecto p ON p.id_proyecto = t.id_proyecto
  WHERE p.id_docente = $id AND t.estado != 'completada' ORDER BY t.fecha_limite LIMIT $inicio, 5");

$pagina = 'inicio';
include 'menu.php';
?>
<div class="tarjeta mb-4">
  <h4 class="fw-bold m-0">Bienvenid@ <span class="subrayado"><?= $_SESSION['nombre'] ?></span></h4>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between mb-2"><small>Proyectos</small><span class="icono verde"><i class="bi bi-folder"></i></span></div>
      <span class="numero"><?= $proyectos ?></span> <small class="text-lowercase">activos</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between mb-2"><small>Tareas</small><span class="icono amarillo"><i class="bi bi-clipboard-check"></i></span></div>
      <span class="numero"><?= $tareas ?></span> <small class="text-lowercase">por gestionar</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between mb-2"><small>Por calificar</small><span class="icono verde"><i class="bi bi-file-earmark-check"></i></span></div>
      <span class="numero"><?= $evidencias ?></span> <small class="text-lowercase">evidencias</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between mb-2"><small>Total alumnos</small><span class="icono"><i class="bi bi-people"></i></span></div>
      <span class="numero"><?= $alumnos ?></span> <small class="text-lowercase">estudiantes</small>
    </div>
  </div>
</div>

<div class="tarjeta lista">
  <div class="d-flex justify-content-between align-items-center p-3 px-4">
    <h5 class="fw-bold m-0">Tareas recientes</h5>
    <a href="tareas.php" class="text-dark small fw-semibold">Ver todas <i class="bi bi-chevron-right"></i></a>
  </div>
  <?php if (mysqli_num_rows($recientes) == 0) { ?>
    <div class="vacio"><span class="icono"><i class="bi bi-clipboard"></i></span><p>No tienes tareas pendientes.</p></div>
  <?php } else { ?>
    <table class="table tabla">
      <tr><th class="ps-4">Tarea</th><th>Vence</th><th>Prioridad</th></tr>
      <?php while ($t = mysqli_fetch_assoc($recientes)) { ?>
        <tr>
          <td class="ps-4">
            <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="text-dark fw-semibold"><?= $t['titulo'] ?></a><br>
            <small class="text-muted">• <?= $t['proyecto'] ?></small>
          </td>
          <td><?= fecha($t['fecha_limite']) ?></td>
          <td><span class="etiqueta <?= $t['prioridad'] ?>"><?= ucfirst($t['prioridad']) ?></span></td>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
</div>
<?php paginacion($totalRecientes, $pag, "index.php?"); ?>
</main>
</body>
</html>
