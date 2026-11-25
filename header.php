<?php
$aj = [];
$res = mysqli_query($con, "SELECT * FROM ajuste");
while ($f = mysqli_fetch_assoc($res)) {
  $aj[$f['clave']] = $f['valor'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $titulo ?> | <?= $aj['marca'] ?></title>
  <link rel="icon" href="assets/img/isotipo.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

  <header class="header">
    <a href="index.php"><img class="logo" src="assets/img/<?= $aj['logo'] ?>" alt="<?= $aj['marca'] ?>"></a>
    <nav class="nav">
      <a href="index.php">Inicio</a>
      <a href="acerca.php">Acerca de SAPIENS</a>
      <a href="antecedentes.php">Antecedentes</a>
      <a href="ayuda.php">Ayuda</a>
    </nav>
    <div class="botones">
      <a href="index.php#login" class="btn btn-claro">Iniciar sesión</a>
      <a href="registro.php" class="btn btn-primario">Registrarse</a>
    </div>
  </header>
