<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id  = $_SESSION['id'];
$idp = (int) ($_GET['id'] ?? 0);
$msg = "";

if (isset($_POST['guardar'])) {
  $nombre = limpiar($_POST['nombre']);
  $desc   = limpiar($_POST['descripcion']);
  $prob   = limpiar($_POST['problematica']);
  $obj    = limpiar($_POST['objetivos']);
  $ini    = limpiar($_POST['fecha_inicio']);
  $fin    = limpiar($_POST['fecha_fin']);
  $estado = limpiar($_POST['estado']);

  if ($idp == 0) {
    mysqli_query($con, "INSERT INTO proyecto (nombre, descripcion, problematica, objetivos, fecha_inicio, fecha_fin, estado, id_docente)
                        VALUES ('$nombre', '$desc', '$prob', '$obj', '$ini', '$fin', '$estado', $id)");
    $idp = mysqli_insert_id($con);
  } else {
    mysqli_query($con, "UPDATE proyecto SET nombre='$nombre', descripcion='$desc', problematica='$prob', objetivos='$obj',
                        fecha_inicio='$ini', fecha_fin='$fin', estado='$estado' WHERE id_proyecto = $idp AND id_docente = $id");
  }
  header("Location: proyecto_form.php?id=$idp&ok=1");
  exit;
}

if (isset($_POST['agregar'])) {
  $correo = limpiar($_POST['correo']);
  $rol    = limpiar($_POST['rol']);
  $al = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_alumno FROM alumno WHERE correo = '$correo'"));
  if ($al) {
    mysqli_query($con, "INSERT IGNORE INTO alumno_proyecto (id_alumno, id_proyecto, rol) VALUES ($al[id_alumno], $idp, '$rol')");
    header("Location: proyecto_form.php?id=$idp");
    exit;
  }
  $msg = "No existe un alumno con ese correo";
}

if (isset($_GET['quitar'])) {
  $a = (int) $_GET['quitar'];
  mysqli_query($con, "DELETE FROM alumno_proyecto WHERE id_alumno = $a AND id_proyecto = $idp");
  header("Location: proyecto_form.php?id=$idp");
  exit;
}

$p = ['nombre' => '', 'descripcion' => '', 'problematica' => '', 'objetivos' => '', 'fecha_inicio' => date('Y-m-d'), 'fecha_fin' => '', 'estado' => 'activo'];
$total = 0;
if ($idp > 0) {
  $p = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM proyecto WHERE id_proyecto = $idp AND id_docente = $id"));
  $equipo = mysqli_query($con, "SELECT a.*, ap.rol FROM alumno_proyecto ap JOIN alumno a ON a.id_alumno = ap.id_alumno WHERE ap.id_proyecto = $idp");
  $total = mysqli_num_rows($equipo);
}

$pagina = 'proyectos';
include 'menu.php';
?>
<div class="angosto">
  <a href="proyectos.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
  <h1 class="titulo mb-4"><?= $idp == 0 ? 'Nuevo proyecto' : 'Editar proyecto' ?></h1>
  <?php if (isset($_GET['ok'])) { ?><div class="alert alert-success">Proyecto guardado</div><?php } ?>

  <form method="post" id="proyecto" class="tarjeta mb-4">
    <div class="titulo-seccion">Datos del proyecto</div>
    <label>Título</label>
    <input name="nombre" class="form-control mb-3" value="<?= $p['nombre'] ?>" required>
    <label>Descripción</label>
    <textarea name="descripcion" class="form-control mb-3" rows="3"><?= $p['descripcion'] ?></textarea>
    <label>Problemática</label>
    <input name="problematica" class="form-control mb-3" value="<?= $p['problematica'] ?>">
    <label>Objetivos</label>
    <input name="objetivos" class="form-control mb-3" value="<?= $p['objetivos'] ?>">
    <div class="row">
      <div class="col-md-6 mb-3"><label>Fecha de inicio</label><input type="date" name="fecha_inicio" class="form-control" value="<?= $p['fecha_inicio'] ?>" required></div>
      <div class="col-md-6 mb-3"><label>Fecha fin estimada</label><input type="date" name="fecha_fin" class="form-control" value="<?= $p['fecha_fin'] ?>" required></div>
    </div>
    <label>Estado</label>
    <?php if ($idp == 0) { ?>
      <input class="form-control" value="Activo" readonly>
      <input type="hidden" name="estado" value="activo">
      <small class="text-muted">Se asigna automáticamente al crear el proyecto.</small>
    <?php } else { ?>
      <select name="estado" class="form-select">
        <option value="activo" <?= $p['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="finalizado" <?= $p['estado'] == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
      </select>
    <?php } ?>
  </form>

  <div class="tarjeta mb-4">
    <div class="titulo-seccion">Asignar alumnos <span class="etiqueta media">Alumnos agregados: <?= $total ?></span></div>
    <?php if ($idp == 0) { ?>
      <p class="text-muted m-0">Guarda el proyecto para poder agregar alumnos.</p>
    <?php } else { ?>
      <div class="row g-4">
        <div class="col-md-6">
          <form method="post">
            <label>Buscar alumno</label>
            <input name="correo" class="form-control" placeholder="correo@ejemplo.com" required>
            <small class="text-muted">Escribe el correo del alumno.</small>
            <label class="mt-3 d-block">Rol en el proyecto</label>
            <select name="rol" class="form-select">
              <option value="participante">Participante</option>
              <option value="lider">Líder</option>
            </select>
            <small class="text-muted">El rol se aplicará al alumno que agregues.</small>
            <?php if ($msg) { ?><div class="alert alert-danger mt-3 mb-0 py-2"><?= $msg ?></div><?php } ?>
            <button name="agregar" class="btn btn-primary w-100 mt-3">Agregar alumno</button>
          </form>
        </div>
        <div class="col-md-6">
          <label>Alumnos agregados (<?= $total ?>)</label>
          <?php while ($a = mysqli_fetch_assoc($equipo)) { ?>
            <div class="caja d-flex align-items-center gap-2 mb-2">
              <span class="avatar verde"><?= iniciales($a['nombre']) ?></span>
              <div class="flex-grow-1">
                <strong class="small"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?></strong><br>
                <small class="text-muted"><?= $a['correo'] ?> · <?= ucfirst($a['rol']) ?></small>
              </div>
              <a href="proyecto_form.php?id=<?= $idp ?>&quitar=<?= $a['id_alumno'] ?>" class="text-muted"><i class="bi bi-trash"></i></a>
            </div>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="d-flex justify-content-between">
    <a href="proyectos.php" class="btn btn-blanco"><i class="bi bi-x-lg"></i> Cancelar</a>
    <div class="d-flex gap-2">
      <?php if ($idp > 0) { ?>
        <a href="proyectos.php?borrar=<?= $idp ?>" class="btn btn-outline-danger" onclick="return confirm('¿Borrar el proyecto?')"><i class="bi bi-trash"></i> Eliminar</a>
      <?php } ?>
      <button form="proyecto" name="guardar" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar proyecto</button>
    </div>
  </div>
</div>
</main>
</body>
</html>
