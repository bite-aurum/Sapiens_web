<?php
// registro de alumnos, docentes y coordinadores
require_once 'includes/publico.php';

$titulo = 'Registro';
include 'includes/cabecera.php';
?>
<main class="bg-light py-5">
    <div class="container">
    <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold">Crear cuenta</h1>
                <p class="text-muted">Llena tus datos para registrarte</p>

                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger py-2"><?= e($_GET['error']) ?></div>
                <?php endif; ?>

                <form method="POST" action="acciones/registro.php">
                    <div class="mb-3">
                        <label for="rol" class="form-label">Soy</label>
                        <select name="rol" id="rol" class="form-select" required>
                            <option value="alumno">Alumno</option>
                            <option value="docente">Docente</option>
                            <option value="coordinador">Coordinador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" placeholder="Nombre y apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo institucional</label>
                        <input type="email" name="correo" id="correo" class="form-control" placeholder="tucorreo@uthh.edu.mx" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" id="contrasena" class="form-control" minlength="8" required>
                        <div class="form-text">Mínimo 8 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmar" class="form-label">Confirmar contraseña</label>
                        <input type="password" name="confirmar" id="confirmar" class="form-control" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Registrarme</button>
                </form>

                <p class="mt-3 mb-0 small">¿Ya tienes cuenta? <a href="index.php#login">Inicia sesión</a></p>
            </div>
        </div>
    </div>
    </div>
    </div>
</main>
<?php include 'includes/pie.php'; ?>
