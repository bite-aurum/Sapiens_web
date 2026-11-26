<?php
// marca una notificacion como leida
require_once '../includes/sesion.php';
validar('alumno');

$id_notificacion = (int)($_POST['id_notificacion'] ?? 0);

// solo puede marcar las suyas
ejecutar("UPDATE notificacion SET leida = 1 WHERE id_notificacion = ? AND id_alumno = ?", "ii", [$id_notificacion, $_SESSION['id']]);

ir('../alumno/notificaciones.php');
