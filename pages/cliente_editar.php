<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$erro = "";
$sucesso = "";

// Valida a existência física e resgata dados prévios do cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if (empty($nome) || empty($email)) {
        $erro = "Campos obrigatórios vazios.";
    } else {
        $up = $pdo->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?");
        if ($up->execute([$nome, $email, $telefone, $endereco, $id])) {
            $sucesso = "Dados atualizados com pleno sucesso.";
            // Atualiza array para exibição em tela pós-salvamento
            $cliente['nome'] = $nome;
            $cliente['email'] = $email;
            $cliente['telefone'] = $telefone;
            $cliente['endereco'] = $endereco;
        } else {
            $erro = "Erro interno ao atualizar.";
        }
    }
}
?>

<div class="mb-4">
    <h2 class="fw-bold m-0">Editar Cadastro de Cliente</h2>
    <small class="text-muted">Modificar atributos cadastrais do ID referenciado #<?= $id; ?></small>
</div>

<div class="custom-card" style="max-width: 700px;">
    <?php if(!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if(!empty($sucesso)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="cliente_editar.php?id=<?= $id; ?>" method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo / Razão Social</label>
            <input type="text" class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail Corporativo</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefone" class="form-label">Telefone de Contato</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($cliente['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Data de Registro</label>
                <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($cliente['data_cadastro'])); ?>" disabled>
            </div>
        </div>
        <div class="mb-4">
            <label for="endereco" class="form-label">Endereço de Atendimento</label>
            <input type="text" class="form-control" id="endereco" name="endereco" value="<?= htmlspecialchars($cliente['endereco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Atualizar</button>
            <a href="clientes.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>