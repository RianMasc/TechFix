<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erro = "";
$sucesso = "";

// Busca a lista completa de clientes cadastrados para popular o select HTML dinamicamente
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $titulo     = trim($_POST['titulo'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $status     = trim($_POST['status'] ?? 'Aberto');

    if ($cliente_id === 0 || empty($titulo)) {
        $erro = "Selecione um cliente solicitante e defina um título descritivo para o chamado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO chamados (cliente_id, titulo, descricao, status) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$cliente_id, $titulo, $descricao, $status])) {
            $sucesso = "Ordem de serviço/Chamado registrado com sucesso.";
        } else {
            $erro = "Falha de persistência interna no BD.";
        }
    }
}
?>

<div class="mb-4">
    <h2 class="fw-bold m-0">Abertura de Chamado Técnico</h2>
    <small class="text-muted">Vincular nova demanda técnica e assistencial a um cliente</small>
</div>

<div class="custom-card" style="max-width: 800px;">
    <?php if(!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if(!empty($sucesso)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-square-check me-2"></i><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="chamado_novo.php" method="POST">
        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente Associado *</label>
            <select class="form-select" id="cliente_id" name="cliente_id" required>
                <option value="">-- Selecione o Cliente Solicitante --</option>
                <?php foreach($clientes as $cl): ?>
                    <option value="<?= $cl['id']; ?>"><?= htmlspecialchars($cl['nome'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="titulo" class="form-label">Título da O.S / Problema Relatado *</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required placeholder="Ex: Formatação e Upgrade de SSD NVMe em Notebook corporativo">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status Inicial</label>
            <select class="form-select" id="status" name="status">
                <option value="Aberto">Aberto (Aguardando Alocação)</option>
                <option value="Em andamento">Em andamento (Bancada/Remoto)</option>
                <option value="Finalizado">Finalizado (Encerrado)</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="descricao" class="form-label">Descrição Detalhada do Escopo do Serviço</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descreva os softwares a serem instalados, hardwares a serem trocados ou diagnósticos preliminares..."></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-ticket me-2"></i>Criar Ticket</button>
            <a href="chamados.php" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>