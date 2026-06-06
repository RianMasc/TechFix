<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erro = ""; $sucesso = "";
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();
$equipamentos = $pdo->query("SELECT id, tipo, marca, modelo FROM equipamentos")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $eq_id      = intval($_POST['equipamento_id'] ?? 0);
    $desc       = trim($_POST['descricao'] ?? '');
    $diag       = trim($_POST['diagnostico'] ?? '');
    $valor      = floatval($_POST['valor'] ?? 0.0);
    $status     = trim($_POST['status'] ?? 'Recebido');

    if($cliente_id===0 || $eq_id===0 || empty($desc)) {
        $erro = "Preencha o cliente, ativo e a descrição inicial do serviço.";
    } else {
        $ins = $pdo->prepare("INSERT INTO ordens_servico (cliente_id, equipamento_id, descricao, diagnostico, valor, status) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$cliente_id, $eq_id, $desc, $diag, $valor, $status]);
        $sucesso = "Ordem de serviço gerada e disponível em bancada.";
    }
}
?>
<div class="mb-4"><h2 class="fw-bold">Abertura de O.S.</h2></div>
<div class="custom-card" style="max-width:800px;">
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro; ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso; ?></div><?php endif; ?>

    <form action="os_nova.php" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Cliente Solicitante *</label>
                <select class="form-select" name="cliente_id" required>
                    <option value="">-- Selecione --</option>
                    <?php foreach($clientes as $c): ?><option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['nome'], ENT_QUOTES); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ativo Tecnológico Vinculado *</label>
                <select class="form-select" name="equipamento_id" required>
                    <option value="">-- Selecione o Dispositivo --</option>
                    <?php foreach($equipamentos as $e): ?>
                        <option value="<?= $e['id']; ?>"><?= htmlspecialchars($e['tipo'] . ' ' . $e['marca'] . ' [' . $e['modelo'] . ']', ENT_QUOTES); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><label class="form-label">Orçamento Inicial (R$)</label><input type="number" step="0.01" class="form-control" name="valor" value="0.00"></div>
            <div class="col-md-6">
                <label class="form-label">Triagem / Status Técnico</label>
                <select class="form-select" name="status">
                    <option value="Recebido">Recebido</option>
                    <option value="Em Análise">Em Análise</option>
                    <option value="Aguardando Peça">Aguardando Peça</option>
                    <option value="Em Reparo">Em Reparo</option>
                    <option value="Pronto">Pronto</option>
                    <option value="Entregue">Entregue</option>
                </select>
            </div>
        </div>
        <div class="mb-3"><label class="form-label">Descrição do Relatório de Entrada *</label><textarea class="form-control" name="descricao" rows="3" required></textarea></div>
        <div class="mb-4"><label class="form-label">Laudo Técnico / Diagnóstico de Bancada</label><textarea class="form-control" name="diagnostico" rows="3"></textarea></div>
        <button type="submit" class="btn btn-primary">Iniciar Ordem</button>
        <a href="ordens.php" class="btn btn-outline-secondary">Voltar</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>