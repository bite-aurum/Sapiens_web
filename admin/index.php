<?php
session_start();
if (($_SESSION['rol'] ?? '') != 'admin') { header('Location: ../index.php'); exit; }
include '../conexion.php';
include '../funciones.php';

$clave = limpiar($_GET['p'] ?? 'inicio');
$a     = $_GET['a'] ?? 'header';
$pag   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pagina WHERE clave = '$clave'"));
$idp   = $pag['id_pagina'];

$aj = [];
$res = mysqli_query($con, "SELECT * FROM ajuste");
while ($f = mysqli_fetch_assoc($res)) {
  $aj[$f['clave']] = $f['valor'];
}

if (isset($_POST['guardar'])) {
  $t = limpiar($_POST['titulo']);
  $d = limpiar($_POST['descripcion'] ?? '');
  $img = '';
  if (!empty($_FILES['imagen']['name'])) {
    $img = time() . '_' . basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/$img");
  }

  if ($a == 'header') {
    mysqli_query($con, "UPDATE pagina SET titulo = '$t', subtitulo = '$d' WHERE id_pagina = $idp");
    if ($img) mysqli_query($con, "UPDATE pagina SET imagen = '$img' WHERE id_pagina = $idp");
  }
  if ($a == 'marca') {
    mysqli_query($con, "UPDATE ajuste SET valor = '$t' WHERE clave = 'marca'");
    if ($img) mysqli_query($con, "UPDATE ajuste SET valor = '$img' WHERE clave = 'logo'");
  }
  if ($a == 'footer') {
    mysqli_query($con, "UPDATE ajuste SET valor = '$t' WHERE clave = 'copyright'");
    mysqli_query($con, "UPDATE ajuste SET valor = '$d' WHERE clave = 'descripcion'");
  }
  if ($a == 'contacto') {
    mysqli_query($con, "UPDATE ajuste SET valor = '$t' WHERE clave = 'correo'");
    mysqli_query($con, "UPDATE ajuste SET valor = '$d' WHERE clave = 'ubicacion'");
  }
  if ($a[0] == 's') {
    $ids = (int) substr($a, 1);
    mysqli_query($con, "UPDATE seccion SET titulo = '$t', texto = '$d' WHERE id_seccion = $ids");
  }
  if ($a[0] == 'q') {
    $idq = (int) substr($a, 1);
    mysqli_query($con, "UPDATE pregunta SET pregunta = '$t', respuesta = '$d' WHERE id_pregunta = $idq");
  }
  if ($a == 'nuevo') {
    if ($clave == 'ayuda') {
      mysqli_query($con, "INSERT INTO pregunta (pregunta, respuesta, orden) VALUES ('$t', '$d', 99)");
    } else {
      mysqli_query($con, "INSERT INTO seccion (id_pagina, titulo, texto, orden) VALUES ($idp, '$t', '$d', 99)");
    }
    $a = 'header';
  }
  header("Location: index.php?p=$clave&a=$a&ok=1");
  exit;
}

$opciones = ['header' => 'Encabezado (Header)'];
if ($clave == 'inicio') {
  $opciones['marca']    = 'Logo y nombre del sitio';
  $opciones['footer']   = 'Pie de página (Footer)';
  $opciones['contacto'] = 'Contacto del pie de página';
}
$secciones = mysqli_query($con, "SELECT * FROM seccion WHERE id_pagina = $idp ORDER BY orden");
while ($s = mysqli_fetch_assoc($secciones)) {
  $opciones['s' . $s['id_seccion']] = 'Sección: ' . $s['titulo'];
}
if ($clave == 'ayuda') {
  $preguntas = mysqli_query($con, "SELECT * FROM pregunta ORDER BY orden");
  while ($q = mysqli_fetch_assoc($preguntas)) {
    $opciones['q' . $q['id_pregunta']] = 'Pregunta: ' . $q['pregunta'];
  }
}
$opciones['nuevo'] = $clave == 'ayuda' ? '+ Agregar pregunta' : '+ Agregar sección';

$titulo = '';
$desc   = '';
$imagen = '';
$conImagen = false;
$conDesc   = true;
if ($a == 'header')   { $titulo = $pag['titulo']; $desc = $pag['subtitulo']; $imagen = $pag['imagen']; $conImagen = true; }
if ($a == 'marca')    { $titulo = $aj['marca']; $imagen = $aj['logo']; $conImagen = true; $conDesc = false; }
if ($a == 'footer')   { $titulo = $aj['copyright']; $desc = $aj['descripcion']; }
if ($a == 'contacto') { $titulo = $aj['correo']; $desc = $aj['ubicacion']; }
if ($a[0] == 's') {
  $s = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM seccion WHERE id_seccion = " . (int) substr($a, 1)));
  $titulo = $s['titulo'];
  $desc   = $s['texto'];
}
if ($a[0] == 'q') {
  $q = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pregunta WHERE id_pregunta = " . (int) substr($a, 1)));
  $titulo = $q['pregunta'];
  $desc   = $q['respuesta'];
}

$pagina = 'editar';
include 'menu.php';
?>
<?php if (isset($_GET['ok'])) { ?><div class="alert alert-success">Cambios guardados</div><?php } ?>

<form method="get" class="tarjeta mb-0" style="border-bottom-left-radius: 0; border-bottom-right-radius: 0; border-bottom: none">
  <input type="hidden" name="p" value="<?= $clave ?>">
  <label class="text-uppercase text-muted small">Apartado a modificar</label>
  <select name="a" class="form-select" onchange="this.form.submit()">
    <?php foreach ($opciones as $valor => $nombre) { ?>
      <option value="<?= $valor ?>" <?= $valor == $a ? 'selected' : '' ?>><?= $nombre ?></option>
    <?php } ?>
  </select>
</form>

<form method="post" enctype="multipart/form-data" id="editar" class="tarjeta mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0; border-top: 1px solid #f3efe6">
  <label class="text-uppercase text-muted small">Texto / Título</label>
  <input name="titulo" class="form-control mb-3" value="<?= $titulo ?>" required>
  <?php if ($conDesc) { ?>
    <label class="text-uppercase text-muted small">Descripción</label>
    <textarea name="descripcion" class="form-control mb-3" rows="3"><?= $desc ?></textarea>
  <?php } ?>
  <?php if ($conImagen) { ?>
    <label class="text-uppercase text-muted small">Imagen nueva / Banner</label>
    <div class="input-group">
      <input class="form-control" value="<?= $imagen ?: 'Sin imagen' ?>" readonly>
      <label class="btn btn-blanco"><i class="bi bi-upload"></i> Subir imagen<input type="file" name="imagen" accept="image/*" hidden onchange="this.closest('.input-group').querySelector('input').value = this.files[0].name"></label>
    </div>
  <?php } ?>
</form>

<div class="tarjeta d-flex flex-wrap justify-content-between align-items-center gap-2">
  <small class="text-muted">Los cambios se aplicarán inmediatamente tras guardar la configuración.</small>
  <div class="d-flex gap-2 ms-auto">
    <a href="index.php?p=<?= $clave ?>&a=<?= $a ?>" class="btn btn-blanco">Cancelar</a>
    <button form="editar" name="guardar" class="btn btn-primary">Guardar cambios</button>
  </div>
</div>
  </main>
</div>
</body>
</html>
