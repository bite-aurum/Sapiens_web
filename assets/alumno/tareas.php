<?php
// mis tareas, primero las pendientes
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];

$tareas = traer("SELECT t.id_tarea, t.titulo, t.estado, t.prioridad, t.fecha_limite, p.nombre AS proyecto
                 FROM alumno_proyecto ap
                 JOIN proyecto p ON p.id_proyecto = ap.id_proyecto
                 JOIN tarea t ON t.id_proyecto = p.id_proyecto
                 WHERE ap.id_alumno = ?
                 ORDER BY t.estado = 'completada', t.fecha_limite", "i", [$id]);

$titulo = 'Mis tareas';
include '../includes/arriba.php';
?>
<h1 class="h3 fw-bold mb-4">Mis tareas</h1>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tarea</th>
                    <th>Proyecto</th>
                    <th>Vence</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $t): ?>
                    <tr>
                        <td class="fw-bold"><?= e($t['titulo']) ?></td>
                        <td><?= e($t['proyecto']) ?></td>
                        <td><?= fecha($t['fecha_limite']) ?></td>
                        <td><span class="badge text-bg-<?= color($t['prioridad']) ?>"><?= e(ucfirst($t['prioridad'])) ?></span></td>
                        <td><span class="badge text-bg-<?= color($t['estado']) ?>"><?= e(ucfirst($t['estado'])) ?></span></td>
                        <td class="text-end"><a href="tarea.php?id=<?= $t['id_tarea'] ?>" class="btn btn-primary btn-sm">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$tareas): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No tienes tareas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/abajo.php'; ?>
