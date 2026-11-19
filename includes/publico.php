<?php
// funciones de las paginas publicas
require_once __DIR__ . '/../config/conexion.php';

// trae los textos de una pagina y sus secciones
function cargarPagina($clave) {
    $pagina = uno("SELECT * FROM pagina WHERE clave = ?", "s", [$clave]);
    if (!$pagina) {
        return [null, []];
    }
    $secciones = traer("SELECT * FROM seccion WHERE id_pagina = ? ORDER BY orden, id_seccion", "i", [$pagina['id_pagina']]);
    return [$pagina, $secciones];
}

// trae los datos del footer
function ajustes() {
    $lista = [];
    foreach (traer("SELECT clave, valor FROM ajuste") as $fila) {
        $lista[$fila['clave']] = $fila['valor'];
    }
    return $lista;
}

// convierte un titulo en id para los enlaces del footer, Tareas y evidencias queda tareas-y-evidencias
function ancla($texto) {
    $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
                            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', '¿' => '']);
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($texto)), '-');
}
