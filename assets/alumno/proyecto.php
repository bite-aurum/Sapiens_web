<?php
// detalle de un proyecto del alumno
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];
$id_proyecto = (int)($_GET['id'] ?? 0);

// solo si el alumno esta en el proyecto
$proyecto = uno("SELECT p.*, CONCAT(d.nombre, ' ', d.apellido_paterno) AS docente
                 FROM proyecto p
                 JOIN alumno_proyecto ap ON ap.id_proyecto = p.id_proyecto
                 JOIN docente d ON d.id_docente = p.id_docente
                 WHERE p.id_proyecto = ? AND ap.id_alumno = ?", "ii", [$id_proyecto, $id]);
if (!$proyecto) {
    ir('proyectos.php', 'El proyecto no existe o no participas en él', 'error');
}

$equipo = traer("SELECT a.nombre, a.apellido_paterno, ap.rol
                 FROM alumno_proyecto ap
                 JOIN alumno a ON a.id_alumno = ap.id_alumno
                 WHERE ap.id_proyecto = ?
                 ORDER BY a.nombre", "i", [$id_proyecto]);

$tareas = traer("SELECT id_tarea, titulo, estado, prioridad, fecha_limite
                 FROM tarea WHERE id_proyecto = ?
                 ORDER BY fecha_limite", "i", [$id_proyecto]);

$titulo = $proyecto['nombre'];
include '../includes/arriba.php';
?>
<a href="proyectos.php" class="btn btn-link px-0 mb-2">&larr; Mis proyectos</a>
<h1 class="h3 fw-bold"><?= e($proyecto['nombre']) ?></h1>
<p class="text-muted">Docente: <?= e($proyecto['docente']) ?> · Del <?= fecha($proyecto['fecha_inicio']) ?> al <?= fecha($proyecto['fecha_fin']) ?></p>

<div class="row g-4">
    <!-- datos del proyecto -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold">Descripción</h2>
                <p><?= nl2br(e($proyecto['descripcion'])) ?></p>
                <h2 class="h6 fw-bold">Problemática</h2>
                <p><?= nl2br(e($proyecto['problematica'])) ?></p>
                <h2 class="h6 fw-bold">Objetivos</h2>
                <p class="mb-0"><?= nl2br(e($proyecto['objetivos'])) ?></p>
            </div>
        </div>

        <!-- tareas del proyecto -->
        <h2 class="h5 fw-bold mb-3">Tareas</h2>
        <div class="list-group shadow-sm">
            <?php foreach ($tareas as $t): ?>
                <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span>
                        <span class="fw-bold"><?= e($t['titulo']) ?></span><br>
                        <small class="text-muted">Vence <?= fecha($t['fecha_limite']) ?></small>
                    </span>
                    <span class="badge text-bg-<?= color($t['estado']) ?>"><?= e(ucfirst($t['estado'])) ?></span>
                </a>
            <?php endforeach; ?>
            <?php if (!$tareas): ?>
                <div class="list-group-item text-muted">El proyecto todavía no tiene tareas.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- equipo -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6 fw-bold">Equipo</h2>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($equipo as $m): ?>
                        <li class="d-flex justify-content-between py-1">
                            <span><?= e($m['nombre'] . ' ' . $m['apellido_paterno']) ?></span>
                            <small class="text-muted"><?= e(ucfirst($m['rol'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/abajo.php'; ?>
