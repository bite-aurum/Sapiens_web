<?php
$pie = ajustes();
?>
<footer class="bg-dark text-white-50 pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- marca, logo y descripcion -->
            <div class="col-md-4">
                <img src="assets/img/logo-completo-blanco.png" alt="SAPIENS" height="40" class="mb-3">
                <p><?= e($pie['descripcion'] ?? '') ?></p>
            </div>

            <!-- plataforma, son enlaces a las secciones de quienes somos -->
            <div class="col-6 col-md-2">
                <h6 class="text-white">Plataforma</h6>
                <ul class="list-unstyled">
                    <li><a class="text-white-50 text-decoration-none" href="quienes.php#proyectos">Proyectos</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="quienes.php#tareas-y-evidencias">Tareas y evidencias</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="quienes.php#reportes">Reportes</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-2">
                <h6 class="text-white">Soporte</h6>
                <ul class="list-unstyled">
                    <li><a class="text-white-50 text-decoration-none" href="ayuda.php">Centro de ayuda</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=guia">Guía por rol</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="ayuda.php#preguntas">Preguntas frecuentes</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=contacto">Contacto</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-2">
                <h6 class="text-white">Legal</h6>
                <ul class="list-unstyled">
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=privacidad">Política de privacidad</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=terminos">Términos y condiciones</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=cookies">Aviso de cookies</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="pagina.php?p=proteccion">Protección de datos</a></li>
                </ul>
            </div>

            <!-- contacto -->
            <div class="col-6 col-md-2">
                <h6 class="text-white">Contacto</h6>
                <ul class="list-unstyled">
                    <li><a class="text-white-50 text-decoration-none" href="mailto:<?= e($pie['correo'] ?? '') ?>"><?= e($pie['correo'] ?? '') ?></a></li>
                    <li><?= e($pie['ubicacion'] ?? '') ?></li>
                </ul>
            </div>
        </div>

        <!-- copyright -->
        <hr class="border-secondary">
        <div class="d-flex justify-content-between small">
            <span><?= e($pie['copyright'] ?? '') ?></span>
            <span><?= e($pie['version'] ?? '') ?></span>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
