<?php
// antecedentes, los textos los edita el admin
require_once 'includes/publico.php';
list($pagina, $secciones) = cargarPagina('antecedentes');

$titulo = 'Antecedentes';
include 'includes/cabecera.php';
?>
<!-- portada -->
<header class="bg-primary text-white text-center py-5">
    <div class="container">
        <small class="text-uppercase"><?= e($pagina['etiqueta']) ?></small>
        <h1 class="fw-bold mt-2"><?= e($pagina['titulo']) ?></h1>
        <p class="lead mb-0"><?= e($pagina['subtitulo']) ?></p>
    </div>
</header>

<!-- secciones -->
<main class="container py-5">
    <div class="row g-4">
        <?php foreach ($secciones as $s): ?>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 fw-bold"><?= e($s['titulo']) ?></h2>
                        <p class="mb-0"><?= nl2br(e($s['texto'])) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
        <a href="registro.php" class="btn btn-primary btn-lg">Crear mi cuenta</a>
    </div>
</main>
<?php include 'includes/pie.php'; ?>
