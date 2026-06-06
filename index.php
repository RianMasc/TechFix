<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Roteador raiz: Se houver sessão ativa, vai para o painel, senão força login
if (isset($_SESSION['usuario_id'])) {
    header("Location: pages/dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>