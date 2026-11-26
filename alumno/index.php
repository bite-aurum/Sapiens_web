<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$numProyectos = contar("SELECT COUNT(*) FROM alumno_proyecto WHERE id_alumno = $id");
$tareas  = contar("SELECT COUNT(*) FROM tarea t JOIN alumno_proyecto ap ON ap.id_proyecto = t.id_proyecto
                   WHERE ap.id_alumno = $id AND t.estado != 'completada'");
$subs    = contar("SELECT COUNT(*) FROM alumno_subtarea x JOIN subtarea s ON s.id_subtarea = x.id_subtarea
                   WHERE x.id_alumno = $id AND s.estado != 'completada'");
$proxima = mysqli_fetch_assoc(mysqli_query($con, "SELECT t.* FROM tarea t JOIN alumno_proyecto ap ON ap.id_proyecto = t.id_proyecto
                   WHERE ap.id_alumno = $id AND t.estado != 'completada' AND t.fecha_limite >= CURDATE()
                   ORDER BY t.fecha_limite LIMIT 1"));

$proyectos = mysqli_query($con, "SELECT p.* FROM alumno_proyecto ap JOIN proyecto p ON p.id_proyecto = ap.id_proyecto
  WHERE ap.id_alumno = $id LIMIT 4");
$misSubs = mysqli_query($con, "SELECT s.*, t.titulo AS tarea, t.id_tarea FROM alumno_subtarea x
  JOIN subtarea s ON s.id_subtarea = x.id_subtarea
  JOIN tarea t ON t.id_tarea = s.id_tarea
  WHERE x.id_alumno = $id ORDER BY s.id_subtarea DESC LIMIT 4");
$notis = mysqli_query($con, "SELECT * FROM notificacion WHERE id_alumno = $id ORDER BY fecha DESC LIMIT 3");

$pagina = 'inicio';
include 'menu.php';
?>
<div class="tarjeta mb-4">
  <h4 class="fw-bold m-0">Bienvenid@ <span class="subrayado"><?= $_SESSION['nombre'] ?></span></h4>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="tarjeta d-flex justify-content-between align-items-center">
      <div><small class="text-muted">Mis proyectos</small><div class="fs-3 fw-bold"><?= $numProyectos ?></div></div>
      <span class="icono verde"><i class="bi bi-folder"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta d-flex justify-content-between align-items-center">
      <div><small class="text-muted">Tareas activas</small><div class="fs-3 fw-bold"><?= $tareas ?></div></div>
      <span class="icono verde"><i class="bi bi-check-circle"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta d-flex justify-content-between align-items-center">
      <div><small class="text-muted">Subtareas pendientes</small><div class="fs-3 fw-bold"><?= $subs ?></div></div>
      <span class="icono amarillo"><i class="bi bi-clipboard"></i></span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="tarjeta d-flex justify-content-between align-items-center">
      <div><small class="text-muted">Próxima entrega</small><div class="fw-bold mt-1"><?= $proxima ? fecha($proxima['fecha_limite']) : '—' ?></div></div>
      <span class="icono verde"><i class="bi bi-calendar-event"></i></span>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="tarjeta h-100">
      <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
        <h6 class="fw-bold m-0"><i class="bi bi-folder2-open text-muted"></i> Mis proyectos</h6>
        <a href="proyectos.php" class="small text-dark fw-semibold">Ver todos</a>
      </div>
      <?php while ($p = mysqli_fetch_assoc($proyectos)) { $av = avance($p['id_proyecto']); ?>
        <a href="proyecto.php?id=<?= $p['id_proyecto'] ?>" class="d-block text-dark mb-3">
          <div class="d-flex justify-content-between small"><strong><?= $p['nombre'] ?></strong><span><?= $av ?>%</span></div>
          <div class="barra-avance mt-1"><div style="width: <?= $av ?>%"></div></div>
        </a>
      <?php } ?>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="tarjeta mb-4">
      <div class="d-flex justify-content-between border-bottom pb-3 mb-2">
        <h6 class="fw-bold m-0"><i class="bi bi-check-circle text-muted"></i> Subtareas recientes</h6>
        <a href="tareas.php" class="small text-dark fw-semibold">Ver todas</a>
      </div>
      <?php while ($s = mysqli_fetch_assoc($misSubs)) { ?>
        <a href="tarea.php?id=<?= $s['id_tarea'] ?>" class="d-flex justify-content-between text-dark small py-2">
          <span><?= $s['titulo'] ?> <span class="text-muted">· <?= $s['tarea'] ?></span></span>
          <span class="etiqueta <?= $s['estado'] ?>"><?= ucfirst($s['estado']) ?></span>
        </a>
      <?php } ?>
    </div>
    <div class="tarjeta">
      <div class="d-flex justify-content-between border-bottom pb-3 mb-2">
        <h6 class="fw-bold m-0"><i class="bi bi-bell text-muted"></i> Notificaciones</h6>
        <a href="notificaciones.php" class="small text-muted"><?= $sinLeer ?> nuevas</a>
      </div>
      <?php while ($n = mysqli_fetch_assoc($notis)) { ?>
        <p class="small py-2 mb-0 <?= $n['leida'] ? '' : 'fw-semibold' ?>"><?= $n['mensaje'] ?><br><small class="text-muted fw-normal"><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></small></p>
      <?php } ?>
    </div>
  </div>
</div>
</main>
</body>
</html>
