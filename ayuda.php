<?php
// centro de ayuda con preguntas frecuentes, el admin las agrega y edita
require_once 'includes/publico.php';
list($pagina, $secciones) = cargarPagina('ayuda');
$preguntas = traer("SELECT * FROM pregunta ORDER BY orden, id_pregunta");

$titulo = 'Ayuda';
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

<main class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-8">
    <h2 class="h4 fw-bold mb-4">Preguntas frecuentes</h2>

    <!-- solo se abre una pregunta a la vez -->
    <div class="accordion" id="preguntas">
        <?php foreach ($preguntas as $p): ?>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pregunta<?= $p['id_pregunta'] ?>">
                        <?= e($p['pregunta']) ?>
                    </button>
                </h3>
                <div id="pregunta<?= $p['id_pregunta'] ?>" class="accordion-collapse collapse" data-bs-parent="#preguntas">
                    <div class="accordion-body"><?= nl2br(e($p['respuesta'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!$preguntas): ?>
        <p class="text-muted">Todavía no hay preguntas.</p>
    <?php endif; ?>
</div>
</div>
</main>
<?php include 'includes/pie.php'; ?>
