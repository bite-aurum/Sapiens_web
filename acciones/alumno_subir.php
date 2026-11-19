<?php
// sube la evidencia de una subtarea, hace lo mismo que subir_archivoAlumno.php de la app
require_once '../includes/sesion.php';
validar('alumno');
$id = $_SESSION['id'];

$id_tarea = (int)($_POST['id_tarea'] ?? 0);
$id_subtarea = (int)($_POST['id_subtarea'] ?? 0);
$volver = '../alumno/tarea.php?id=' . $id_tarea;

// la subtarea tiene que ser de un proyecto del alumno
$subtarea = uno("SELECT s.id_subtarea
                 FROM subtarea s
                 JOIN tarea t ON t.id_tarea = s.id_tarea
                 JOIN alumno_proyecto ap ON ap.id_proyecto = t.id_proyecto
                 WHERE s.id_subtarea = ? AND ap.id_alumno = ?", "ii", [$id_subtarea, $id]);
if (!$subtarea) {
    ir($volver, 'La subtarea no existe o no participas en su proyecto', 'error');
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] != 0) {
    ir($volver, 'Selecciona un archivo', 'error');
}

$archivo = $_FILES['archivo'];
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$permitidas = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip', 'png', 'jpg', 'jpeg'];

if (!in_array($extension, $permitidas)) {
    ir($volver, 'Tipo de archivo no permitido', 'error');
}
if ($archivo['size'] > 10 * 1024 * 1024) {
    ir($volver, 'El archivo no debe pesar más de 10 MB', 'error');
}

// se guarda en la misma carpeta de la app para que el docente lo vea en los dos lados
$carpeta = ARCHIVOS . 'uploads/';
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}
$nombre = uniqid('archivo_') . '.' . $extension;

if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombre)) {
    ir($volver, 'No se pudo guardar el archivo', 'error');
}

ejecutar("INSERT INTO archivo (nombre_archivo, url, tipo_mime, id_alumno, id_subtarea) VALUES (?, ?, ?, ?, ?)",
    "sssii", [substr($archivo['name'], 0, 100), 'uploads/' . $nombre, $archivo['type'], $id, $id_subtarea]);

// la subtarea queda entregada, con la misma palabra que usa la app
ejecutar("UPDATE subtarea SET estado = 'completado' WHERE id_subtarea = ?", "i", [$id_subtarea]);
actualizarTarea($id_tarea);

ir($volver, 'Archivo subido');
