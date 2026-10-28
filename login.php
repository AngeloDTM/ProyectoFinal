<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
solo_invitados();

$error = '';
$ok = isset($_GET['registrado']) ? 'Cuenta creada. Ya puedes iniciar sesión.' : '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valido()) {
        $error = 'La sesión expiró. Recarga la página e intenta de nuevo.';
    } elseif ($usuario === '' || $password === '') {
        $error = 'Escribe tu usuario y tu contraseña.';
    } else {
        $st = $pdo->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE usuario = ? OR correo = ? LIMIT 1');
        $st->execute([$usuario, $usuario]);
        $u = $st->fetch();

        if ($u && password_verify($password, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid']    = (int)$u['id'];
            $_SESSION['nombre'] = $u['nombre'];
            header('Location: inicio.php');
            exit;
        }
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Colmena</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <main class="auth">
        <div class="intro">
            <h1>Colmena</h1>
            <p>Comparte lo que pasa en tu día con las personas que más te importan.</p>
        </div>

        <form class="box" method="post" action="login.php" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <?php if ($ok): ?><p class="msg ok" role="status"><?= e($ok) ?></p><?php endif; ?>
            <?php if ($error): ?><p class="msg err" role="alert"><?= e($error) ?></p><?php endif; ?>

            <label for="usuario">Usuario o correo</label>
            <input type="text" id="usuario" name="usuario" value="<?= e($usuario) ?>" autocomplete="username" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit" class="btn">Iniciar sesión</button>
            <hr>
            <a class="btn btn-alt" href="registro.php">Crear cuenta nueva</a>
        </form>
    </main>
</body>
</html>
