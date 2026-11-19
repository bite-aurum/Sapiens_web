<?php
// avisos que le mandan sus docentes
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];

$notificaciones = traer("SELECT n.*, CONCAT(d.nombre, ' ', d.apellido_paterno) AS remitente
                         FROM notificacion n
                         LEFT JOIN docente d ON d.id_docente = n.id_docente
                         WHERE n.id_alumno = ?
                         ORDER BY n.fecha DESC, n.id_notificacion DESC", "i", [$id]);

$titulo = 'Notificaciones';
include '../includes/arriba.php';
?>
<h1 class="h3 fw-bold mb-4">Notificaciones</h1>

<div class="list-group shadow-sm">
    <?php foreach ($notificaciones as $n): ?>
        <div class="list-group-item <?= $n['leida'] ? '' : 'list-group-item-primary' ?>">
            <div class="d-flex justify-content-between">
                <span class="badge text-bg-secondary align-self-start"><?= e(ucfirst($n['tipo'])) ?></span>
                <small class="text-muted"><?= fecha($n['fecha']) ?></small>
            </div>
            <p class="my-2"><?= nl2br(e($n['mensaje'])) ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">De: <?= e($n['remitente'] ?: 'SAPIENS') ?></small>
                <?php if (!$n['leida']): ?>
                    <form method="POST" action="../acciones/alumno_leida.php">
                        <input type="hidden" name="id_notificacion" value="<?= $n['id_notificacion'] ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Marcar como leída</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$notificaciones): ?>
        <div class="list-group-item text-muted">No tienes notificaciones.</div>
    <?php endif; ?>
</div>
<?php include '../includes/abajo.php'; ?>
