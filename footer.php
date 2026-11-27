  <footer class="footer">
    <div class="footer-grid">
      <div>
        <img class="logo" src="assets/img/logo-completo-blanco.png" alt="SAPIENS">
        <p><?= $aj['descripcion'] ?></p>
      </div>
      <div>
        <h3>Plataforma</h3>
        <a href="quienes.php">Proyectos</a>
        <a href="quienes.php">Tareas y evidencias</a>
        <a href="quienes.php">Reportes</a>
      </div>
      <div>
        <h3>Soporte</h3>
        <a href="ayuda.php">Centro de ayuda</a>
        <a href="pagina.php?clave=guia">Guía por rol</a>
        <a href="ayuda.php">Preguntas frecuentes</a>
        <a href="pagina.php?clave=contacto">Contacto</a>
      </div>
      <div>
        <h3>Legal</h3>
        <a href="pagina.php?clave=privacidad">Política de privacidad</a>
        <a href="pagina.php?clave=terminos">Términos y condiciones</a>
        <a href="pagina.php?clave=cookies">Aviso de cookies</a>
        <a href="pagina.php?clave=proteccion">Protección de datos</a>
      </div>
      <div>
        <h3>Contacto</h3>
        <a href="mailto:<?= $aj['correo'] ?>"><?= $aj['correo'] ?></a>
        <a><?= $aj['ubicacion'] ?></a>
      </div>
    </div>
    <div class="footer-fin">
      <span><?= $aj['copyright'] ?></span>
      <span><?= $aj['version'] ?></span>
    </div>
  </footer>
<script src="js/main.js"></script>
</body>
</html>
