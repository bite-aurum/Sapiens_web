<?php
// panel del alumno, igual que el dashboard de la app
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];

// la siguiente tarea que tiene que entregar
$proxima = uno("SELECT t.id_tarea, t.titulo, t.fecha_limite, p.nombre AS proyecto
                FROM tarea t
                JOIN proyecto p ON t.id_proyecto = p.id_proyecto
                JOIN alumno_proyecto ap ON p.id_proyecto = ap.id_proyecto
                WHERE ap.id_alumno = ? AND t.estado <> 'completada'
                ORDER BY t.fecha_limite IS NULL, t.fecha_limite
                LIMIT 1", "i", [$id]);

// sus proyectos con el avance
$proyectos = traer("SELECT p.id_proyecto, p.nombre,
                        (SELECT COUNT(*) FROM tarea t WHERE t.id_proyecto = p.id_proyecto) AS total,
                        (SELECT COUNT(*) FROM tarea t WHERE t.id_proyecto = p.id_proyecto AND t.estado = 'completada') AS hechas
                    FROM alumno_proyecto ap
                    JOIN proyecto p ON ap.id_proyecto = p.id_proyecto
                    WHERE ap.id_alumno = ?
                    ORDER BY p.id_proyecto DESC
                    LIMIT 3", "i", [$id]);

// avisos que no ha leido
$sin_leer = uno("SELECT COUNT(*) AS total FROM notificacion WHERE id_alumno = ? AND leida = 0", "i", [$id]);

$titulo = 'Panel del alumno';
include '../includes/arriba.php';
?>
<h1 class="h3 fw-bold mb-4">Hola, <?= e($_SESSION['nombre']) ?></h1>

<div class="row g-4">
    <!-- proxima entrega -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-muted">Próxima entrega</h2>
                <?php if ($proxima): ?>
                    <p class="h5 fw-bold mb-1"><?= e($proxima['titulo']) ?></p>
                    <p class="text-muted mb-3"><?= e($proxima['proyecto']) ?> · Vence <?= fecha($proxima['fecha_limite']) ?></p>
                    <a href="tarea.php?id=<?= $proxima['id_tarea'] ?>" class="btn btn-primary btn-sm">Ver tarea</a>
                <?php else: ?>
                    <p class="mb-0">No tienes tareas pendientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- notificaciones -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-muted">Notificaciones</h2>
                <p class="h5 fw-bold"><?= $sin_leer['total'] ?> sin leer</p>
                <a href="notificaciones.php" class="btn btn-outline-primary btn-sm">Ver notificaciones</a>
            </div>
        </div>
    </div>
</div>

<!-- mis proyectos -->
<div class="d-flex justify-content-between align-items-center mt-5 mb-3">
    <h2 class="h5 fw-bold mb-0">Mis proyectos</h2>
    <a href="proyectos.php">Ver todos</a>
</div>
<div class="row g-3">
    <?php foreach ($proyectos as $p): ?>
        <?php $porcentaje = avance($p['hechas'], $p['total']); ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h6 fw-bold"><?= e($p['nombre']) ?></h3>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $porcentaje ?>% de avance</small>
                    <a href="proyecto.php?id=<?= $p['id_proyecto'] ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$proyectos): ?>
        <p class="text-muted">Todavía no estás en ningún proyecto.</p>
    <?php endif; ?>
</div>
<?php include '../includes/abajo.php'; ?>
