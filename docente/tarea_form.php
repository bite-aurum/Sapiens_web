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
  $prioridad = limpiar($_POST['prioridad']);
  $estado    = limpiar($_POST['estado']);

  if ($idt == 0) {
    mysqli_query($con, "INSERT INTO tarea (titulo, descripcion, criterios, fecha_limite, prioridad, estado, id_proyecto)
                        VALUES ('$titulo', '$desc', '$criterios', '$fecha', '$prioridad', '$estado', $proyecto)");
    $idt = mysqli_insert_id($con);
  } else {
    mysqli_query($con, "UPDATE tarea SET titulo='$titulo', descripcion='$desc', criterios='$criterios', fecha_limite='$fecha',
                        prioridad='$prioridad', estado='$estado', id_proyecto=$proyecto WHERE id_tarea = $idt");
  }
  header("Location: tarea_form.php?id=$idt&ok=1");
  exit;
}

if (isset($_POST['agregar'])) {
  $titulo = limpiar($_POST['sub_titulo']);
  $desc   = limpiar($_POST['sub_descripcion']);
  $alumno = (int) $_POST['id_alumno'];
  mysqli_query($con, "INSERT INTO subtarea (titulo, descripcion, id_tarea) VALUES ('$titulo', '$desc', $idt)");
  $ids = mysqli_insert_id($con);
  if ($alumno > 0) {
    mysqli_query($con, "INSERT INTO alumno_subtarea (id_alumno, id_subtarea) VALUES ($alumno, $ids)");
  }
  header("Location: tarea_form.php?id=$idt");
  exit;
}

if (isset($_GET['quitar'])) {
  $s = (int) $_GET['quitar'];
  mysqli_query($con, "DELETE FROM subtarea WHERE id_subtarea = $s");
  header("Location: tarea_form.php?id=$idt");
  exit;
}

$t = ['id_proyecto' => 0, 'titulo' => '', 'descripcion' => '', 'criterios' => '', 'fecha_limite' => '', 'prioridad' => 'media', 'estado' => 'pendiente'];
if ($idt > 0) {
  $t = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM tarea WHERE id_tarea = $idt"));
  $subtareas = mysqli_query($con, "SELECT * FROM subtarea WHERE id_tarea = $idt");
  $equipo = mysqli_query($con, "SELECT a.id_alumno, a.nombre, a.apellido_paterno FROM alumno_proyecto ap
    JOIN alumno a ON a.id_alumno = ap.id_alumno WHERE ap.id_proyecto = $t[id_proyecto]");
}
$proyectos = mysqli_query($con, "SELECT id_proyecto, nombre FROM proyecto WHERE id_docente = $id");

$pagina = 'tareas';
include 'menu.php';
?>
<div class="angosto">
  <a href="tareas.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
  <h1 class="titulo mb-4"><?= $idt == 0 ? 'Nueva tarea' : 'Editar tarea' ?></h1>
  <?php if (isset($_GET['ok'])) { ?><div class="alert alert-success">Tarea guardada</div><?php } ?>

  <div class="tarjeta">
    <form method="post" id="tarea">
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
        <div class="col-md-6 mb-3"><label>Prioridad</label>
          <select name="prioridad" class="form-select">
            <option value="alta" <?= $t['prioridad'] == 'alta' ? 'selected' : '' ?>>Alta</option>
            <option value="media" <?= $t['prioridad'] == 'media' ? 'selected' : '' ?>>Media</option>
            <option value="baja" <?= $t['prioridad'] == 'baja' ? 'selected' : '' ?>>Baja</option>
          </select>
        </div>
      </div>
      <label>Estado</label>
      <select name="estado" class="form-select mb-4">
        <option value="pendiente" <?= $t['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="en progreso" <?= $t['estado'] == 'en progreso' ? 'selected' : '' ?>>En progreso</option>
        <option value="completada" <?= $t['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
      </select>
    </form>

    <div class="titulo-seccion">Subtareas del equipo</div>
    <?php if ($idt == 0) { ?>
      <p class="text-muted">Guarda la tarea para poder agregar subtareas.</p>
    <?php } else { ?>
      <div class="row g-3">
        <div class="col-md-6">
          <form method="post" class="caja">
            <label>Nombre</label>
            <input name="sub_titulo" class="form-control mb-2" placeholder="Nombre de la subtarea" required>
            <label>Descripción</label>
            <input name="sub_descripcion" class="form-control mb-2" placeholder="Descripción breve">
            <label>Asignar a</label>
            <select name="id_alumno" class="form-select mb-3">
              <option value="0">Sin asignar</option>
              <?php while ($a = mysqli_fetch_assoc($equipo)) { ?>
                <option value="<?= $a['id_alumno'] ?>"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?></option>
              <?php } ?>
            </select>
            <button name="agregar" class="btn btn-blanco w-100"><i class="bi bi-plus-lg"></i> Agregar subtarea</button>
          </form>
        </div>
        <div class="col-md-6">
          <label>Subtareas agregadas</label>
          <?php while ($s = mysqli_fetch_assoc($subtareas)) { ?>
            <div class="caja bg-white d-flex justify-content-between align-items-center mb-2">
              <div><span class="small fw-semibold"><?= $s['titulo'] ?></span><br><small class="text-muted"><?= $s['descripcion'] ?></small></div>
              <a href="tarea_form.php?id=<?= $idt ?>&quitar=<?= $s['id_subtarea'] ?>" class="text-muted" onclick="return confirm('¿Quitar subtarea?')"><i class="bi bi-trash"></i></a>
            </div>
          <?php } ?>
        </div>
      </div>
    <?php } ?>

    <div class="d-flex justify-content-between border-top pt-3 mt-4">
      <a href="tareas.php" class="btn btn-blanco"><i class="bi bi-x-lg"></i> Cancelar</a>
      <div class="d-flex gap-2">
        <?php if ($idt > 0) { ?>
          <a href="tareas.php?borrar=<?= $idt ?>" class="btn btn-outline-danger" onclick="return confirm('¿Borrar la tarea?')"><i class="bi bi-trash"></i> Eliminar</a>
        <?php } ?>
        <button form="tarea" name="guardar" class="btn btn-primary">Guardar tarea</button>
      </div>
    </div>
  </div>
</div>
</main>
</body>
</html>
