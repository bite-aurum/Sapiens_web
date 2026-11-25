<?php
function limpiar($texto) {
  global $con;
  return mysqli_real_escape_string($con, trim($texto));
}

function contar($sql) {
  global $con;
  $fila = mysqli_fetch_row(mysqli_query($con, $sql));
  return $fila[0];
}

function avance($id_proyecto) {
  $total  = contar("SELECT COUNT(*) FROM tarea WHERE id_proyecto = $id_proyecto");
  $hechas = contar("SELECT COUNT(*) FROM tarea WHERE id_proyecto = $id_proyecto AND estado = 'completada'");
  if ($total == 0) return 0;
  return round($hechas * 100 / $total);
}

function fecha($f) {
  if (!$f) return '—';
  return date('d/m/Y', strtotime($f));
}

function iniciales($nombre) {
  return strtoupper(mb_substr($nombre, 0, 2));
}

function rutaArchivo($url) {
  $nombre = basename($url);
  if (is_file("../../sapiens_movil/uploads/$nombre")) return "../../sapiens_movil/uploads/$nombre";
  if (is_file("../uploads/$nombre")) return "../uploads/$nombre";
  return '';
}
