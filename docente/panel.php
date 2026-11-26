<?php
// panel del docente, igual que el dashboard de la app
require_once '../includes/sesion.php';
validar('docente');
$id = $_SESSION['id'];

// numeros generales
$resumen = uno("SELECT
    (SELECT COUNT(*) FROM proyecto WHERE id_docente = ?) AS proyectos,
    (SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = ?) AS tareas,
    (SELECT COUNT(*) FROM tarea t JOIN proyecto p ON p.id_proyecto = t.id_proyecto WHERE p.id_docente = ? AND t.estado <> 'completada') AS pendientes,
    (SELECT COUNT(DISTINCT ap.id_alumno) FROM alumno_proyecto ap JOIN proyecto p ON p.id_proyecto = ap.id_proyecto WHERE p.id_docente = ?) AS alumnos",
    "iiii", [$id, $id, $id, $id]);

// tareas recientes, las 3 que vencen primero
$recientes = traer("SELECT t.id_tarea, t.titulo, t.fecha_limite, t.prioridad, p.nombre AS proyecto
                    FROM tarea t
                    JOIN proyecto p ON t.id_proyecto = p.id_proyecto
                    WHERE p.id_docente = ?
                    ORDER BY t.fecha_limite
                    LIMIT 3", "i", [$id]);

$titulo = 'Panel del docente';
include '../includes/arriba.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Hola, <?= e($_SESSION['nombre']) ?></h1>
    <div>
        <a href="proyecto_form.php" class="btn btn-success">+ Nuevo proyecto</a>
        <a href="tarea_form.php" class="btn btn-primary">+ Nueva tarea</a>
    </div>
</div>

<!-- numeros generales -->
<div class="row g-3 mb-4">
    <?php foreach (['proyectos' => 'Proyectos', 'tareas' => 'Tareas', 'pendientes' => 'Tareas pendientes', 'alumnos' => 'Alumnos'] as $clave => $texto): ?>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <div class="h2 fw-bold text-primary mb-0"><?= $resumen[$clave] ?></div>
                    <small class="text-muted"><?= $texto ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- tareas recientes -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 fw-bold mb-0">Tareas recientes</h2>
    <a href="tareas.php">Ver todas</a>
</div>
<div class="list-group shadow-sm">
    <?php foreach ($recientes as $t): ?>
        <a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span>
                <span class="fw-bold"><?= e($t['titulo']) ?></span><br>
                <small class="text-muted"><?= e($t['proyecto']) ?> · Vence <?= fecha($t['fecha_limite']) ?></small>
            </span>
            <span class="badge text-bg-<?= color($t['prioridad']) ?>"><?= e(ucfirst($t['prioridad'])) ?></span>
        </a>
    <?php endforeach; ?>
    <?php if (!$recientes): ?>
        <div class="list-group-item text-muted">Todavía no tienes tareas.</div>
    <?php endif; ?>
</div>
<?php include '../includes/abajo.php'; ?>
