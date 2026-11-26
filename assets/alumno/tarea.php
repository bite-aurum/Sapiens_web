<?php
// detalle de una tarea, aqui el alumno sube sus evidencias
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];
$id_tarea = (int)($_GET['id'] ?? 0);

// solo si la tarea es de un proyecto del alumno
$tarea = uno("SELECT t.*, p.nombre AS proyecto, CONCAT(d.nombre, ' ', d.apellido_paterno) AS docente
              FROM tarea t
              JOIN proyecto p ON p.id_proyecto = t.id_proyecto
              JOIN docente d ON d.id_docente = p.id_docente
              JOIN alumno_proyecto ap ON ap.id_proyecto = p.id_proyecto
              WHERE t.id_tarea = ? AND ap.id_alumno = ?", "ii", [$id_tarea, $id]);
if (!$tarea) {
    ir('tareas.php', 'La tarea no existe o no participas en su proyecto', 'error');
}

$subtareas = traer("SELECT * FROM subtarea WHERE id_tarea = ? ORDER BY id_subtarea", "i", [$id_tarea]);

// progreso segun las subtareas entregadas
$hechas = 0;
foreach ($subtareas as $s) {
    if (in_array($s['estado'], ['completada', 'completado'])) {
        $hechas++;
    }
}
$porcentaje = avance($hechas, count($subtareas));

$titulo = $tarea['titulo'];
include '../includes/arriba.php';
?>
<a href="tareas.php" class="btn btn-link px-0 mb-2">&larr; Mis tareas</a>
<h1 class="h3 fw-bold"><?= e($tarea['titulo']) ?></h1>
<p class="text-muted">
    <?= e($tarea['proyecto']) ?> · Docente: <?= e($tarea['docente']) ?> · Vence <?= fecha($tarea['fecha_limite']) ?>
</p>
<p>
    <span class="badge text-bg-<?= color($tarea['prioridad']) ?>">Prioridad <?= e($tarea['prioridad']) ?></span>
    <span class="badge text-bg-<?= color($tarea['estado']) ?>"><?= e(ucfirst($tarea['estado'])) ?></span>
</p>

<?php if ($tarea['descripcion']): ?>
    <p><?= nl2br(e($tarea['descripcion'])) ?></p>
<?php endif; ?>

<!-- progreso -->
<div class="progress mb-1" style="height: 10px;">
    <div class="progress-bar bg-success" style="width: <?= $porcentaje ?>%"></div>
</div>
<small class="text-muted"><?= $porcentaje ?>% entregado</small>

<!-- subtareas con su evidencia -->
<h2 class="h5 fw-bold mt-4 mb-3">Subtareas</h2>
<?php foreach ($subtareas as $s): ?>
    <?php $archivos = traer("SELECT nombre_archivo, url, fecha_subida FROM archivo WHERE id_subtarea = ? AND id_alumno = ?", "ii", [$s['id_subtarea'], $id]); ?>
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h3 class="h6 fw-bold"><?= e($s['titulo']) ?></h3>
                <span class="badge text-bg-<?= color($s['estado']) ?> align-self-start"><?= e(ucfirst($s['estado'])) ?></span>
            </div>
            <?php if ($s['descripcion']): ?>
                <p class="text-muted"><?= nl2br(e($s['descripcion'])) ?></p>
            <?php endif; ?>

            <!-- archivos que ya subio -->
            <?php foreach ($archivos as $a): ?>
                <div class="small mb-1">
                    <a href="<?= e(ARCHIVOS . $a['url']) ?>" target="_blank"><?= e($a['nombre_archivo']) ?></a>
                    <span class="text-muted">· <?= fecha($a['fecha_subida']) ?></span>
                </div>
            <?php endforeach; ?>

            <!-- subir evidencia -->
            <form method="POST" action="../acciones/alumno_subir.php" enctype="multipart/form-data" class="d-flex gap-2 mt-2">
                <input type="hidden" name="id_tarea" value="<?= $id_tarea ?>">
                <input type="hidden" name="id_subtarea" value="<?= $s['id_subtarea'] ?>">
                <input type="file" name="archivo" class="form-control form-control-sm" required>
                <button type="submit" class="btn btn-primary btn-sm">Subir</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!$subtareas): ?>
    <p class="text-muted">Esta tarea no tiene subtareas.</p>
<?php endif; ?>
<?php include '../includes/abajo.php'; ?>
