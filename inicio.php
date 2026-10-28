<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
requiere_login();

$nombre  = $_SESSION['nombre'];
$inicial = mb_strtoupper(mb_substr($nombre, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio · Colmena</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- BARRA SUPERIOR -->
    <header class="topbar">
        <div class="topbar-left">
            <a class="logo" href="inicio.php" aria-label="Inicio">C</a>
            <input class="search" type="search" placeholder="Buscar en Colmena" aria-label="Buscar">
        </div>
        <nav class="tabs" aria-label="Secciones">
            <a class="tab on" href="inicio.php">Inicio</a>
            <a class="tab" href="#">Amigos</a>
            <a class="tab" href="#">Mensajes</a>
        </nav>
        <form method="post" action="logout.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="btn-salir">Cerrar sesión</button>
        </form>
    </header>

    <div class="layout">
        <!-- COLUMNA IZQUIERDA: menú -->
        <aside class="side">
            <a class="item" href="#"><span class="avatar"><?= e($inicial) ?></span><?= e($nombre) ?></a>
            <a class="item" href="#">Amigos</a>
            <a class="item" href="#">Guardados</a>
            <a class="item" href="#">Eventos</a>
        </aside>

        <!-- COLUMNA CENTRAL: feed -->
        <main class="feed">
            <!-- Aquí irá el formulario para publicar -->
            <section class="card compose">
                <span class="avatar"><?= e($inicial) ?></span>
                <input type="text" placeholder="¿Qué estás pensando, <?= e(explode(' ', $nombre)[0]) ?>?" disabled>
            </section>

            <!-- Aquí irá el listado de publicaciones (SELECT a la base de datos) -->
            <section class="card empty">
                <p>Aún no hay publicaciones.</p>
            </section>
        </main>

        <!-- COLUMNA DERECHA: contactos -->
        <aside class="side">
            <h3>Contactos</h3>
            <p class="muted">Aquí aparecerán tus amigos.</p>
        </aside>
    </div>
</body>
</html>
