<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if (empty($nome) || empty($email)) {
        $erro = "Os campos Nome e E-mail são obrigatórios para a integridade do registro.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nome, $email, $telefone, $endereco])) {
            $sucesso = "Cliente registrado de forma satisfatória no banco de dados.";
        } else {
            $erro = "Falha crítica interna ao tentar persistir os dados.";
        }
    }
}
?>

<div class="mb-4">
    <h2 class="fw-bold m-0">Cadastrar Novo Cliente</h2>
    <small class="text-muted">Inserção imediata de ativos de atendimento no sistema TechFix</small>
</div>

<div class="custom-card" style="max-width: 700px;">
    <?php if(!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if(!empty($sucesso)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="cliente_novo.php" method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo / Razão Social *</label>
            <input type="text" class="form-control" id="nome" name="nome" required placeholder="Ex: Informática Global LTDA">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail de Contato *</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Ex: contato@infoglobal.com">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Ex: (11) 99999-9999">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Data Cadastral</label>
                <input type="text" class="form-control" value="<?= date('d/m/Y'); ?>" disabled>
            </div>
        </div>
        <div class="mb-4">
            <label for="endereco" class="form-label">Endereço Operacional</label>
            <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Ex: Av. Paulista, 1000 - São Paulo SP">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Salvar Registro</button>
            <a href="clientes.php" class="btn btn-outline-secondary">Voltar à Listagem</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>