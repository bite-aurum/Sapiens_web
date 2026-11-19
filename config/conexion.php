<?php
// conexion a la base, es la misma que usa la app movil
$conexion = mysqli_connect("127.0.0.1", "u845581198_SAPIENS", "SAPIENS_dev123", "u845581198_SAPIENS");

if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

// carpeta donde la app movil guarda los archivos, la web usa la misma
define('ARCHIVOS', '../../sapiens_movil/');

// trae varias filas de una consulta
function traer($sql, $tipos = '', $datos = []) {
    global $conexion;
    $stmt = mysqli_prepare($conexion, $sql);
    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$datos);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $filas;
}

// trae solo una fila
function uno($sql, $tipos = '', $datos = []) {
    $filas = traer($sql, $tipos, $datos);
    return $filas ? $filas[0] : null;
}

// para insert, update y delete
function ejecutar($sql, $tipos = '', $datos = []) {
    global $conexion;
    $stmt = mysqli_prepare($conexion, $sql);
    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$datos);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// manda a otra pagina con un mensaje verde o rojo
function ir($pagina, $mensaje = '', $tipo = 'mensaje') {
    if ($mensaje !== '') {
        $pagina .= (strpos($pagina, '?') === false ? '?' : '&') . $tipo . '=' . urlencode($mensaje);
    }
    header('Location: ' . $pagina);
    exit;
}

// limpia un texto antes de mostrarlo
function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// muestra la fecha como 19/11/2026
function fecha($valor) {
    return $valor ? date('d/m/Y', strtotime($valor)) : 'Sin fecha';
}

// porcentaje de avance
function avance($hechas, $total) {
    return $total > 0 ? round($hechas * 100 / $total) : 0;
}

// color de bootstrap segun la prioridad o el estado
function color($texto) {
    $colores = [
        'alta' => 'danger', 'media' => 'warning', 'baja' => 'success',
        'pendiente' => 'secondary', 'en progreso' => 'info',
        'completada' => 'success', 'completado' => 'success',
        'activo' => 'primary', 'finalizado' => 'dark',
        'a tiempo' => 'success', 'en retraso' => 'danger'
    ];
    $clave = strtolower($texto);
    return isset($colores[$clave]) ? $colores[$clave] : 'secondary';
}

// cambia el estado de la tarea segun sus subtareas, igual que la app movil
function actualizarTarea($id_tarea) {
    $conteo = uno("SELECT COUNT(*) AS total,
                          SUM(estado IN ('completada', 'completado')) AS hechas
                   FROM subtarea WHERE id_tarea = ?", "i", [$id_tarea]);
    $total = (int)$conteo['total'];
    $hechas = (int)$conteo['hechas'];

    if ($total == 0 || $hechas == 0) {
        $estado = 'pendiente';
    } elseif ($hechas < $total) {
        $estado = 'en progreso';
    } else {
        $estado = 'completada';
    }
    ejecutar("UPDATE tarea SET estado = ? WHERE id_tarea = ?", "si", [$estado, $id_tarea]);
}
