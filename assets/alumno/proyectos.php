<?php
// mis proyectos
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];

$proyectos = traer("SELECT p.id_proyecto, p.nombre, p.estado, p.fecha_fin,
                        CONCAT(d.nombre, ' ', d.apellido_paterno) AS docente,
                        (SELECT COUNT(*) FROM tarea t WHERE t.id_proyecto = p.id_proyecto) AS total,
                        (SELECT COUNT(*) FROM tarea t WHERE t.id_proyecto = p.id_proyecto AND t.estado = 'completada') AS hechas
                    FROM alumno_proyecto ap
                    JOIN proyecto p ON p.id_proyecto = ap.id_proyecto
                    JOIN docente d ON d.id_docente = p.id_docente
                    WHERE ap.id_alumno = ?
                    ORDER BY p.id_proyecto DESC", "i", [$id]);

$titulo = 'Mis proyectos';
include '../includes/arriba.php';
?>
<h1 class="h3 fw-bold mb-4">Mis proyectos</h1>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Proyecto</th>
                    <th>Docente</th>
                    <th>Entrega</th>
                    <th>Estado</th>
                    <th>Avance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $p): ?>
                    <?php $porcentaje = avance($p['hechas'], $p['total']); ?>
                    <tr>
                        <td class="fw-bold"><?= e($p['nombre']) ?></td>
                        <td><?= e($p['docente']) ?></td>
                        <td><?= fecha($p['fecha_fin']) ?></td>
                        <td><span class="badge text-bg-<?= color($p['estado']) ?>"><?= e(ucfirst($p['estado'])) ?></span></td>
                        <td style="min-width: 140px;">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                            <small class="text-muted"><?= $porcentaje ?>%</small>
                        </td>
                        <td class="text-end"><a href="proyecto.php?id=<?= $p['id_proyecto'] ?>" class="btn btn-primary btn-sm">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$proyectos): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Todavía no estás en ningún proyecto.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/abajo.php'; ?>
