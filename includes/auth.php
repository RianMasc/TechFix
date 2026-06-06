<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarAutenticacao() {
    if (!isset($_SESSION['usuario_id'])) {
        // Como o arquivo pode rodar de dentro de subpastas, calcula a rota raiz do login de forma limpa
        $path = (basename(dirname($_SERVER['PHP_SELF'])) === 'pages') ? '../login.php' : 'login.php';
        header("Location: " . $path);
        exit();
    }
}

function tentarSanitizar($dados) {
    return htmlspecialchars($dados ?? '', ENT_QUOTES, 'UTF-8');
}
?>