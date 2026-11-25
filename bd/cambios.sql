
UPDATE alumno SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE id_alumno = 1;

ALTER TABLE pagina ADD imagen VARCHAR(255) NULL;

INSERT INTO ajuste (clave, valor) VALUES ('marca', 'SAPIENS'), ('logo', 'logo-completo.png');

ALTER TABLE tarea ADD fecha_asignacion DATE NOT NULL DEFAULT curdate();
ALTER TABLE tarea ADD criterios TEXT NULL;

ALTER TABLE notificacion MODIFY fecha DATETIME NOT NULL DEFAULT current_timestamp();

ALTER TABLE docente ADD activo TINYINT(1) NOT NULL DEFAULT 1;

DELETE FROM notificacion WHERE id_notificacion IN (2, 3, 4, 5);
DELETE FROM reporte WHERE id_reporte IN (2, 3);
