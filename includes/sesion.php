<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

function validar($roles) {
    $roles = (array)$roles;

    if (!isset($_SESSION['id']) || !in_array($_SESSION['rol'], $roles)) {
        if (in_array('admin', $roles)) {
            ir('../admin/login.php', 'Debes iniciar sesión como administrador', 'error');
        }
        ir('../index.php', 'Debes iniciar sesión para entrar a esa página', 'error');
    }
}
