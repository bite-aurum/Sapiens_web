<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

if (isset($_GET['leer'])) {
  mysqli_query($con, "UPDATE notificacion SET leida = 1 WHERE id_alumno = $id");
  header('Location: notificaciones.php');
  exit;
}

$f = $_GET['f'] ?? '';
$filtro = "";
if ($f == 'noleidas') $filtro = "AND n.leida = 0";
if ($f == 'proyecto') $filtro = "AND n.tipo IN ('proyecto', 'asignacion')";
if ($f == 'tarea')    $filtro = "AND n.tipo = 'tarea'";

$notis = mysqli_query($con, "SELECT n.*, d.nombre AS docente FROM notificacion n
  LEFT JOIN docente d ON d.id_docente = n.id_docente
  WHERE n.id_alumno = $id $filtro ORDER BY n.fecha DESC");

$iconos = ['tarea' => 'file-earmark-text', 'proyecto' => 'folder', 'asignacion' => 'folder', 'alerta' => 'alarm', 'general' => 'check-circle'];

$pagina = 'notificaciones';
include 'menu.php';
?>
<h1 class="titulo mb-3">Notificaciones</h1>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="filtros">
    <a href="notificaciones.php" class="<?= $f == '' ? 'activo' : '' ?>">Todas</a>
    <a href="notificaciones.php?f=noleidas" class="<?= $f == 'noleidas' ? 'activo' : '' ?>">No leídas</a>
    <a href="notificaciones.php?f=proyecto" class="<?= $f == 'proyecto' ? 'activo' : '' ?>">Proyectos</a>
    <a href="notificaciones.php?f=tarea" class="<?= $f == 'tarea' ? 'activo' : '' ?>">Tareas</a>
  </div>
  <a href="notificaciones.php?leer=1" class="small text-muted"><i class="bi bi-check2"></i> Marcar todas como leídas</a>
</div>

<div class="tarjeta lista">
  <?php if (mysqli_num_rows($notis) == 0) { ?>
    <div class="vacio"><span class="icono"><i class="bi bi-bell"></i></span><p>No tienes notificaciones.</p></div>
  <?php } ?>
  <?php while ($n = mysqli_fetch_assoc($notis)) { ?>
    <div class="fila <?= $n['leida'] ? '' : 'no-leida' ?>">
      <div class="d-flex gap-3">
        <span class="icono <?= $n['tipo'] ?>"><i class="bi bi-<?= $iconos[$n['tipo']] ?? 'bell' ?>"></i></span>
        <div>
          <span class="small <?= $n['leida'] ? '' : 'fw-bold' ?>"><?= $n['mensaje'] ?></span>
          <?php if (!$n['leida']) { ?><span class="text-acento">●</span><?php } ?>
          <br><small class="text-muted"><?= $n['docente'] ? 'Prof. ' . $n['docente'] . ' · ' : '' ?><?= ucfirst($n['tipo']) ?></small>
        </div>
      </div>
      <small class="text-muted text-nowrap"><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></small>
    </div>
  <?php } ?>
</div>
</main>
</body>
</html>
