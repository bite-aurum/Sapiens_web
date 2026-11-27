<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'docente') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id  = $_SESSION['id'];
$idp = (int) ($_GET['id'] ?? 0);

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

  mysqli_query($con, "DELETE FROM alumno_proyecto WHERE id_proyecto = $idp");
  $alumnos = $_POST['alumnos'] ?? [];
  foreach ($alumnos as $a) {
    $a   = (int) $a;
    $rol = limpiar($_POST['rol'][$a]);
    mysqli_query($con, "INSERT INTO alumno_proyecto (id_alumno, id_proyecto, rol) VALUES ($a, $idp, '$rol')");
  }

  header('Location: proyectos.php');
  exit;
}

$p = ['nombre' => '', 'descripcion' => '', 'problematica' => '', 'objetivos' => '', 'fecha_inicio' => date('Y-m-d'), 'fecha_fin' => '', 'estado' => 'activo'];
if ($idp > 0) {
  $p = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM proyecto WHERE id_proyecto = $idp AND id_docente = $id"));
}

$todos = mysqli_query($con, "SELECT a.*, ap.rol FROM alumno a
  LEFT JOIN alumno_proyecto ap ON ap.id_alumno = a.id_alumno AND ap.id_proyecto = $idp
  ORDER BY a.nombre");
$agregados = contar("SELECT COUNT(*) FROM alumno_proyecto WHERE id_proyecto = $idp");

$pagina = 'proyectos';
include 'menu.php';
?>
<div class="angosto">
  <a href="proyectos.php" class="btn btn-blanco btn-sm mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
  <h1 class="titulo mb-4"><?= $idp == 0 ? 'Nuevo proyecto' : 'Editar proyecto' ?></h1>

  <form method="post">
    <div class="tarjeta mb-4">
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
      <select name="estado" class="form-select">
        <option value="activo" <?= $p['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="finalizado" <?= $p['estado'] == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
      </select>
    </div>

    <div class="tarjeta mb-4">
      <div class="titulo-seccion">Asignar alumnos <span class="etiqueta media">Alumnos agregados: <?= $agregados ?></span></div>
      <div style="max-height: 320px; overflow-y: auto">
        <?php while ($a = mysqli_fetch_assoc($todos)) { $ida = $a['id_alumno']; ?>
          <div class="caja d-flex align-items-center gap-3 mb-2">
            <input type="checkbox" class="form-check-input m-0" name="alumnos[]" value="<?= $ida ?>" <?= $a['rol'] ? 'checked' : '' ?>>
            <span class="avatar verde"><?= iniciales($a['nombre']) ?></span>
            <div class="flex-grow-1">
              <strong class="small"><?= $a['nombre'] . ' ' . $a['apellido_paterno'] ?></strong><br>
              <small class="text-muted"><?= $a['correo'] ?></small>
            </div>
            <select name="rol[<?= $ida ?>]" class="form-select form-select-sm" style="width: 150px">
              <option value="participante">Participante</option>
              <option value="lider" <?= $a['rol'] == 'lider' ? 'selected' : '' ?>>Líder</option>
            </select>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <a href="proyectos.php" class="btn btn-blanco"><i class="bi bi-x-lg"></i> Cancelar</a>
      <div class="d-flex gap-2">
        <?php if ($idp > 0) { ?>
          <a href="proyectos.php?borrar=<?= $idp ?>" class="btn btn-outline-danger" onclick="return confirm('¿Borrar el proyecto?')"><i class="bi bi-trash"></i> Eliminar</a>
        <?php } ?>
        <button name="guardar" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar proyecto</button>
      </div>
    </div>
  </form>
</div>
</main>
</body>
</html>
