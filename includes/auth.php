<?php
// Sesión y funciones de ayuda compartidas por todas las páginas.
session_start();

// Escapa texto antes de mostrarlo en HTML (evita XSS).
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Token CSRF: protege los formularios.
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_valido(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function esta_logueado(): bool {
    return isset($_SESSION['uid']);
}
function requiere_login(): void {
    if (!esta_logueado()) {
        header('Location: login.php');
        exit;
    }
}
function solo_invitados(): void {
    if (esta_logueado()) {
        header('Location: inicio.php');
        exit;
    }
}
