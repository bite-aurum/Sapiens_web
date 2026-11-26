<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'alumno') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';
$id = $_SESSION['id'];

$proyectos = mysqli_query($con, "SELECT p.*, ap.rol, d.nombre AS docente, d.apellido_paterno AS docente_ap
  FROM alumno_proyecto ap
  JOIN proyecto p ON p.id_proyecto = ap.id_proyecto
  JOIN docente d ON d.id_docente = p.id_docente
  WHERE ap.id_alumno = $id");

$pagina = 'proyectos';
include 'menu.php';
?>
<h1 class="titulo mb-4">Mis proyectos</h1>

<div class="tarjeta lista">
  <?php if (mysqli_num_rows($proyectos) == 0) { ?>
    <div class="vacio"><span class="icono"><i class="bi bi-folder"></i></span><p>Aún no estás en ningún proyecto.</p></div>
  <?php } ?>
  <?php while ($p = mysqli_fetch_assoc($proyectos)) { $av = avance($p['id_proyecto']); ?>
    <a href="proyecto.php?id=<?= $p['id_proyecto'] ?>" class="fila">
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <strong><?= $p['nombre'] ?></strong>
          <span class="etiqueta <?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span>
        </div>
        <small class="text-muted">PROY-2026-<?= $p['id_proyecto'] ?> · Prof. <?= $p['docente'] . ' ' . $p['docente_ap'] ?> · Fin estimado: <?= fecha($p['fecha_fin']) ?></small>
        <div class="d-flex align-items-center gap-3 mt-2">
          <div class="barra-avance flex-grow-1"><div style="width: <?= $av ?>%"></div></div>
          <small class="fw-semibold"><?= $av ?>%</small>
        </div>
      </div>
    </a>
  <?php } ?>
</div>
</main>
</body>
</html>
