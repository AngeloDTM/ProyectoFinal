<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
solo_invitados();

$errores = [];
$v = ['nombre' => '', 'usuario' => '', 'correo' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nombre']  = trim($_POST['nombre'] ?? '');
    $v['usuario'] = trim($_POST['usuario'] ?? '');
    $v['correo']  = trim($_POST['correo'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirmar    = $_POST['confirmar'] ?? '';

    if (!csrf_valido()) {
        $errores[] = 'La sesión expiró. Recarga la página e intenta de nuevo.';
    }
    if (mb_strlen($v['nombre']) < 2 || mb_strlen($v['nombre']) > 60) {
        $errores[] = 'El nombre debe tener entre 2 y 60 caracteres.';
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $v['usuario'])) {
        $errores[] = 'El usuario debe tener de 3 a 30 caracteres: letras, números o guion bajo.';
    }
    if (!filter_var($v['correo'], FILTER_VALIDATE_EMAIL) || strlen($v['correo']) > 120) {
        $errores[] = 'Escribe un correo válido.';
    }
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $confirmar) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    if (!$errores) {
        $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE usuario = ? OR correo = ? LIMIT 1');
        $st->execute([$v['usuario'], $v['correo']]);
        if ($st->fetch()) {
            $errores[] = 'Ese usuario o correo ya está registrado.';
        }
    }

    if (!$errores) {
        $st = $pdo->prepare('INSERT INTO usuarios (nombre, usuario, correo, password_hash) VALUES (?, ?, ?, ?)');
        $st->execute([$v['nombre'], $v['usuario'], $v['correo'], password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?registrado=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta · Colmena</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <main class="auth">
        <div class="intro">
            <h1>Colmena</h1>
            <p>Crea tu cuenta y empieza a compartir.</p>
        </div>

        <form class="box" method="post" action="registro.php" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <?php if ($errores): ?>
                <ul class="msg err" role="alert">
                    <?php foreach ($errores as $m): ?><li><?= e($m) ?></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?= e($v['nombre']) ?>" autocomplete="name" required>

            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" value="<?= e($v['usuario']) ?>" autocomplete="username" required>

            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" value="<?= e($v['correo']) ?>" autocomplete="email" required>

            <label for="password">Contraseña (mínimo 8 caracteres)</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>

            <label for="confirmar">Confirmar contraseña</label>
            <input type="password" id="confirmar" name="confirmar" autocomplete="new-password" required>

            <button type="submit" class="btn">Crear cuenta</button>
            <hr>
            <a class="btn btn-alt" href="login.php">Ya tengo cuenta</a>
        </form>
    </main>
</body>
</html>
