<?php
require __DIR__ . '/includes/auth.php';
header('Location: ' . (esta_logueado() ? 'inicio.php' : 'login.php'));
exit;
