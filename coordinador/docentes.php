<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'coordinador') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['activo'])) {
  $d = (int) $_GET['doc'];
  $a = (int) $_GET['activo'];
  mysqli_query($con, "UPDATE docente SET activo = $a WHERE id_docente = $d AND id_coordinador = $id");
  header('Location: docentes.php');
  exit;
}

if (isset($_GET['quitar'])) {
  $d = (int) $_GET['quitar'];
  mysqli_query($con, "UPDATE docente SET id_coordinador = NULL WHERE id_docente = $d AND id_coordinador = $id");
  header('Location: docentes.php');
  exit;
}

$yo      = mysqli_fetch_assoc(mysqli_query($con, "SELECT codigo_vinculo FROM coordinador WHERE id_coordinador = $id"));
$total   = contar("SELECT COUNT(*) FROM docente WHERE id_coordinador = $id");
$activos = contar("SELECT COUNT(*) FROM docente WHERE id_coordinador = $id AND activo = 1");
$pct     = $total == 0 ? 0 : round($activos * 100 / $total);

$buscar  = limpiar($_GET['buscar'] ?? '');
$estado  = $_GET['estado'] ?? '';
$materia = limpiar($_GET['materia'] ?? '');
$filtro  = "";
if ($buscar != '')  $filtro .= " AND (nombre LIKE '%$buscar%' OR apellido_paterno LIKE '%$buscar%' OR correo LIKE '%$buscar%')";
if ($estado == '1') $filtro .= " AND activo = 1";
if ($estado == '0') $filtro .= " AND activo = 0";
if ($materia != '') $filtro .= " AND departamento = '$materia'";

$pag = (int) ($_GET['pag'] ?? 1);
if ($pag < 1) $pag = 1;
$inicio = ($pag - 1) * 5;

$totalLista = contar("SELECT COUNT(*) FROM docente WHERE id_coordinador = $id $filtro");
$docentes   = mysqli_query($con, "SELECT * FROM docente WHERE id_coordinador = $id $filtro ORDER BY nombre LIMIT $inicio, 5");
$materias = mysqli_query($con, "SELECT DISTINCT departamento FROM docente WHERE id_coordinador = $id AND departamento != ''");

$pagina = 'docentes';
include 'menu.php';
?>
<h1 class="titulo pb-3 mb-4 border-bottom">Docentes</h1>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between"><small class="fw-bold">Docentes registrados</small><i class="bi bi-people text-muted"></i></div>
      <div class="numero"><?= $total ?></div>
      <small class="text-muted normal"><?= $total == 0 ? 'Sin miembros asignados' : 'Ligados a tu clave' ?></small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between"><small class="fw-bold">Docentes activos</small><i class="bi bi-person-check text-acento"></i></div>
      <div class="numero"><?= $activos ?></div>
      <small class="text-muted normal"><?= $pct ?>% de actividad global</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tarjeta dato">
      <div class="d-flex justify-content-between"><small class="fw-bold">Clave de vinculación</small><i class="bi bi-person-check text-acento"></i></div>
      <div class="numero"><?= $yo['codigo_vinculo'] ?></div>
      <small class="text-muted normal">Compártela con tus docentes para que se liguen a ti</small>
    </div>
  </div>
</div>

<form class="tarjeta py-2 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div class="input-group" style="max-width: 340px">
    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
    <input name="buscar" class="form-control" placeholder="Buscar docente por nombre o correo..." value="<?= $buscar ?>">
  </div>
  <div class="d-flex align-items-center gap-2">
    <small class="text-muted text-nowrap"><i class="bi bi-funnel"></i> Filtrar:</small>
    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Todos los estados</option>
      <option value="1" <?= $estado == '1' ? 'selected' : '' ?>>Activos</option>
      <option value="0" <?= $estado == '0' ? 'selected' : '' ?>>Inactivos</option>
    </select>
    <select name="materia" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Todas las materias</option>
      <?php while ($m = mysqli_fetch_assoc($materias)) { ?>
        <option <?= $materia == $m['departamento'] ? 'selected' : '' ?>><?= $m['departamento'] ?></option>
      <?php } ?>
    </select>
  </div>
</form>

<div class="tarjeta lista">
  <div class="titulo-seccion">Docentes registrados <span class="normal text-muted">Total: <?= $totalLista ?></span></div>
  <?php if (mysqli_num_rows($docentes) == 0) { ?>
    <div class="vacio">
      <span class="icono"><i class="bi bi-person-x"></i></span>
      <h6 class="fw-bold">Sin docentes registrados.</h6>
      <small>Los docentes se ligan escribiendo tu clave de vinculación en su configuración.</small>
    </div>
  <?php } ?>
  <?php while ($d = mysqli_fetch_assoc($docentes)) {
    $proy = contar("SELECT COUNT(*) FROM proyecto WHERE id_docente = $d[id_docente]");
  ?>
    <div class="fila">
      <div class="d-flex align-items-center gap-3">
        <span class="avatar"><?= iniciales($d['nombre']) ?></span>
        <div>
          <strong class="small"><?= $d['nombre'] . ' ' . $d['apellido_paterno'] ?></strong><br>
          <small class="text-muted"><?= $d['correo'] ?><?= $d['departamento'] ? ' · ' . $d['departamento'] : '' ?> · <?= $proy ?> proyectos</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3 small">
        <?php if ($d['activo'] == 1) { ?>
          <span class="etiqueta activo">Activo</span>
          <a href="docentes.php?doc=<?= $d['id_docente'] ?>&activo=0" class="text-muted">Desactivar</a>
        <?php } else { ?>
          <span class="etiqueta">Inactivo</span>
          <a href="docentes.php?doc=<?= $d['id_docente'] ?>&activo=1">Activar</a>
        <?php } ?>
        <a href="docentes.php?quitar=<?= $d['id_docente'] ?>" class="text-danger" onclick="return confirm('¿Quitar docente?')"><i class="bi bi-trash"></i></a>
      </div>
    </div>
  <?php } ?>
</div>

<?php paginacion($totalLista, $pag, "docentes.php?buscar=$buscar&estado=$estado&materia=$materia&"); ?>
</main>
</body>
</html>