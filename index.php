<?php
// inicio e inicio de sesion
require_once 'includes/publico.php';
list($pagina, $secciones) = cargarPagina('inicio');

$titulo = 'Inicio';
include 'includes/cabecera.php';
?>
<main class="bg-light py-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">

            <!-- texto de bienvenida, lo edita el admin -->
            <div class="col-lg-7">
                <span class="badge text-bg-primary mb-3"><?= e($pagina['etiqueta']) ?></span>
                <h1 class="display-5 fw-bold"><?= e($pagina['titulo']) ?></h1>
                <p class="lead text-muted"><?= e($pagina['subtitulo']) ?></p>

                <!-- numeros de la portada -->
                <div class="row text-center mt-4">
                    <?php foreach ($secciones as $s): ?>
                        <div class="col">
                            <div class="h3 fw-bold text-primary mb-0"><?= e($s['titulo']) ?></div>
                            <small class="text-muted"><?= e($s['texto']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- formulario de inicio de sesion -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0" id="login">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold">Iniciar sesión</h2>
                        <p class="text-muted">Ingresa con tu cuenta institucional</p>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger py-2"><?= e($_GET['error']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($_GET['mensaje'])): ?>
                            <div class="alert alert-success py-2"><?= e($_GET['mensaje']) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="acciones/login.php">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control" placeholder="tucorreo@uthh.edu.mx" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Entrar</button>
                        </form>

                        <p class="mt-3 mb-1 small">¿Aún no tienes cuenta? <a href="registro.php">Regístrate</a></p>
                        <p class="small text-muted mb-0">
                            Al continuar aceptas los <a href="pagina.php?p=terminos">Términos y condiciones</a>
                            y la <a href="pagina.php?p=privacidad">Política de privacidad</a>.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
<?php include 'includes/pie.php'; ?>
