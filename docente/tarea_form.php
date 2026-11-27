<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id  = $_SESSION['id'];
$idt = (int) ($_GET['id'] ?? 0);

if (isset($_POST['guardar'])) {
  $proyecto  = (int) $_POST['id_proyecto'];
  $titulo    = limpiar($_POST['titulo']);
  $desc      = limpiar($_POST['descripcion']);
  $criterios = limpiar($_POST['criterios']);
  $fecha     = limpiar($_POST['fecha_limite']);
  $estado    = limpiar($_POST['estado']);
  $prior     = prioridad($fecha, $estado);

  if ($idt == 0) {
    mysqli_query($con, "INSERT INTO tarea (titulo, descripcion, criterios, fecha_limite, prioridad, estado, id_proyecto)
                        VALUES ('$titulo', '$desc', '$criterios', '$fecha', '$prior', '$estado', $proyecto)");
    $idt = mysqli_insert_id($con);
  } else {
    mysqli_query($con, "UPDATE tarea SET titulo='$titulo', descripcion='$desc', criterios='$criterios', fecha_limite='$fecha',
                        prioridad='$prior', estado='$estado', id_proyecto=$proyecto WHERE id_tarea = $idt");
  }

  $quitar = $_POST['quitar'] ?? [];
  foreach ($quitar as $s) {
    $s = (int) $s;
    mysqli_query($con, "DELETE FROM subtarea WHERE id_subtarea = $s AND id_tarea = $idt");
  }

  $titulos = $_POST['sub_titulo'] ?? [];
  for ($i = 0; $i < count($titulos); $i++) {
    $subTitulo = limpiar($titulos[$i]);
    if ($subTitulo == '') continue;
    $subDesc = limpiar($_POST['sub_descripcion'][$i]);
    $alumno  = (int) $_POST['sub_alumno'][$i];

    mysqli_query($con, "INSERT INTO subtarea (titulo, descripcion, id_tarea) VALUES ('$subTitulo', '$subDesc', $idt)");
    $ids = mysqli_insert_id($con);
    if ($alumno > 0) {
      mysqli_query($con, "INSERT INTO alumno_subtarea (id_alumno, id_subtarea) VALUES ($alumno, $ids)");
    }
  }

  header("Location: tarea.php?id=$idt");
  exit;
}

$t = ['id_proyecto' => 0, 'titulo' => '', 'descripcion' => '', 'criterios' => '', 'fecha_limite' => '', 'estado' => 'pendiente'];
if ($idt > 0) {
  $t = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM tarea WHERE id_tarea = $idt"));
}
$proyectos = mysqli_query($con, "SELECT id_proyecto, nombre FROM proyecto WHERE id_docente = $id");
$subtareas = mysqli_query($con, "SELECT * FROM subtarea WHERE id_tarea = $idt");
$alumnos   = mysqli_query($con, "SELECT DISTINCT a.id_alumno, a.nombre, a.apellido_paterno, p.nombre AS proyecto
  FROM alumno_proyecto ap
  JOIN alumno a ON a.id_alumno = ap.id_alumno
  JOIN proyecto p ON p.id_proyecto = ap.id_proyecto
  WHERE p.id_docente = $id ORDER BY p.nombre, a.nombre");

$pagina = 'tareas';
include 'menu.php';
?>
<div class="angosto">
  <a href="tareas.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
  <h1 class="titulo mb-4"><?= $idt == 0 ? 'Nueva tarea' : 'Editar tarea' ?></h1>

  <form method="post" class="tarjeta">
    <div class="titulo-seccion">Datos de la tarea</div>
    <label>Proyecto</label>
    <select name="id_proyecto" class="form-select mb-3" required>
      <option value="">Seleccionar proyecto...</option>
      <?php while ($p = mysqli_fetch_assoc($proyectos)) { ?>
        <option value="<?= $p['id_proyecto'] ?>" <?= $p['id_proyecto'] == $t['id_proyecto'] ? 'selected' : '' ?>><?= $p['nombre'] ?></option>
      <?php } ?>
    </select>
    <label>Nombre de la tarea</label>
    <input name="titulo" class="form-control mb-3" placeholder="Ingresa el nombre de la tarea" value="<?= $t['titulo'] ?>" required>
    <label>Descripción</label>
    <textarea name="descripcion" class="form-control mb-3" rows="3" placeholder="Detalles de la tarea"><?= $t['descripcion'] ?></textarea>
    <label>Criterios de aceptación</label>
    <textarea name="criterios" class="form-control mb-3" rows="3" placeholder="Uno por renglón"><?= $t['criterios'] ?></textarea>
    <div class="row">
      <div class="col-md-6 mb-3"><label>Fecha límite</label><input type="date" name="fecha_limite" class="form-control" value="<?= $t['fecha_limite'] ?>" required></div>
      <div class="col-md-6 mb-3"><label>Estado</label>
        <select name="estado" class="form-select">
          <option value="pendiente" <?= $t['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
          <option value="en progreso" <?= $t['estado'] == 'en progreso' ? 'selected' : '' ?>>En progreso</option>
          <option value="completada" <?= $t['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
        </select>
      </div>
    </div>

    <div class="titulo-seccion mt-3">Subtareas del equipo</div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="caja">
          <div id="subtareas">
            <div class="nueva-subtarea border-bottom pb-2 mb-2">
              <label>Nombre</label>
              <input name="sub_titulo[]" class="form-control mb-2" placeholder="Nombre de la subtarea">
              <label>Descripción</label>
              <input name="sub_descripcion[]" class="form-control mb-2" placeholder="Descripción breve">
              <label>Asignar a</label>
              <select name="sub_alumno[]" class="form-select mb-2">
                <option value="0">Sin asignar</option>
                <?php while ($a = mysqli_fetch_assoc($alumnos)) { ?>
                  <option value="<?= $a['id_alumno'] ?>"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?> — <?= $a['proyecto'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <button type="button" class="btn btn-blanco w-100" onclick="agregarSubtarea()"><i class="bi bi-plus-lg"></i> Agregar subtarea</button>
        </div>
      </div>
      <div class="col-md-6">
        <label>Subtareas agregadas</label>
        <?php if (mysqli_num_rows($subtareas) == 0) { ?>
          <p class="text-muted small">Todavía no hay subtareas.</p>
        <?php } ?>
        <?php while ($s = mysqli_fetch_assoc($subtareas)) { ?>
          <div class="caja bg-white d-flex justify-content-between align-items-center mb-2">
            <div><span class="small fw-semibold"><?= $s['titulo'] ?></span><br><small class="text-muted"><?= $s['descripcion'] ?></small></div>
            <label class="small text-danger text-nowrap"><input type="checkbox" name="quitar[]" value="<?= $s['id_subtarea'] ?>"> Quitar</label>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="d-flex justify-content-between border-top pt-3 mt-4">
      <a href="tareas.php" class="btn btn-blanco"><i class="bi bi-x-lg"></i> Cancelar</a>
      <div class="d-flex gap-2">
        <?php if ($idt > 0) { ?>
          <a href="tareas.php?borrar=<?= $idt ?>" class="btn btn-outline-danger" onclick="return confirm('¿Borrar la tarea?')"><i class="bi bi-trash"></i> Eliminar</a>
        <?php } ?>
        <button name="guardar" class="btn btn-primary">Guardar tarea</button>
      </div>
    </div>
  </form>
</div>

<script>
function agregarSubtarea() {
  var nueva = document.querySelector('.nueva-subtarea').cloneNode(true);
  nueva.querySelectorAll('input').forEach(function (campo) {
    campo.value = '';
  });
  document.getElementById('subtareas').appendChild(nueva);
}
</script>
</main>
</body>
</html>
