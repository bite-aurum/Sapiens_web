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

function paginacion($total, $pag, $url) {
  $paginas = ceil($total / 5);
  if ($paginas <= 1) return;
  echo '<div class="paginacion">';
  for ($i = 1; $i <= $paginas; $i++) {
    if ($i == $pag) {
      echo "<a class='activo'>$i</a>";
    } else {
      echo "<a href='{$url}pag=$i'>$i</a>";
    }
  }
  echo '</div>';
}

function prioridad($fecha, $estado) {
  if ($estado == 'completada') return 'baja';
  $dias = (strtotime($fecha) - strtotime(date('Y-m-d'))) / 86400;
  if ($dias <= 3) return 'alta';
  if ($dias <= 7) return 'media';
  return 'baja';
}

function actualizarEstado($id_tarea) {
  global $con;
  $total  = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $id_tarea");
  $hechas = contar("SELECT COUNT(*) FROM subtarea WHERE id_tarea = $id_tarea AND estado = 'completada'");

  if ($total > 0 && $hechas == $total) {
    $estado = 'completada';
  } elseif ($hechas > 0) {
    $estado = 'en progreso';
  } else {
    $estado = 'pendiente';
  }
  mysqli_query($con, "UPDATE tarea SET estado = '$estado' WHERE id_tarea = $id_tarea");

  $tarea = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_proyecto FROM tarea WHERE id_tarea = $id_tarea"));
  $idp   = $tarea['id_proyecto'];
  $faltan = contar("SELECT COUNT(*) FROM tarea WHERE id_proyecto = $idp AND estado != 'completada'");

  if ($faltan == 0) {
    mysqli_query($con, "UPDATE proyecto SET estado = 'finalizado' WHERE id_proyecto = $idp");
  } else {
    mysqli_query($con, "UPDATE proyecto SET estado = 'activo' WHERE id_proyecto = $idp");
  }
}
