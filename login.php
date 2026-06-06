<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario_id'])) {
    header("Location: pages/dashboard.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            header("Location: pages/dashboard.php");
            exit();
        } else {
            $erro = "Credenciais inválidas para acesso.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TechFix Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background-color: #12141c;">

<div class="custom-card style-login" style="width: 100%; max-width: 400px; padding: 2.5rem;">
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold m-0"><i class="fa-solid fa-screwdriver-wrench"></i> TechFix</h2>
        <small class="text-muted text-uppercase tracking-wide">Gestão Corporativa de TI</small>
    </div>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger py-2" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <small><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></small>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail corporativo</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" required placeholder="riri@techfix.com">
            </div>
        </div>
        
        <div class="mb-4">
            <label for="senha" class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="senha" name="senha" required placeholder="rian123">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 p-2 font-weight-bold">
            <i class="fa-solid fa-right-to-bracket me-2"></i> Autenticar
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>