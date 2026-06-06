<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$erro = "";
$sucesso = "";

$stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = ?");
$stmt->execute([$id]);
$chamado = $stmt->fetch();

if (!$chamado) {
    header("Location: chamados.php");
    exit();
}

$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $titulo     = trim($_POST['titulo'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $status     = trim($_POST['status'] ?? 'Aberto');

    if ($cliente_id === 0 || empty($titulo)) {
        $erro = "Campos estruturais obrigatórios vazios.";
    } else {
        $up = $pdo->prepare("UPDATE chamados SET cliente_id = ?, titulo = ?, descricao = ?, status = ? WHERE id = ?");
        if ($up->execute([$cliente_id, $titulo, $descricao, $status, $id])) {
            $sucesso = "Ordem de serviço modificada e consolidada com sucesso.";
            $chamado['cliente_id'] = $cliente_id;
            $chamado['titulo'] = $titulo;
            $chamado['descricao'] = $descricao;
            $chamado['status'] = $status;
        } else {
            $erro = "Falha ao gravar alterações.";
        }
    }
}
?>

<div class="mb-4">
    <h2 class="fw-bold m-0">Atualizar Escopo de Chamado</h2>
    <small class="text-muted">Ajuste de andamento, status e descrições do chamado #<?= $id; ?></small>
</div>

<div class="custom-card" style="max-width: 800px;">
    <?php if(!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if(!empty($sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="chamado_editar.php?id=<?= $id; ?>" method="POST">
        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente Associado</label>
            <select class="form-select" id="cliente_id" name="cliente_id" required>
                <?php foreach($clientes as $cl): ?>
                    <option value="<?= $cl['id']; ?>" <?= $chamado['cliente_id'] == $cl['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($cl['nome'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="titulo" class="form-label">Título da O.S / Serviço</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required value="<?= htmlspecialchars($chamado['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status Atual da Demanda</label>
            <select class="form-select" id="status" name="status">
                <option value="Aberto" <?= $chamado['status'] === 'Aberto' ? 'selected' : ''; ?>>Aberto</option>
                <option value="Em andamento" <?= $chamado['status'] === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                <option value="Finalizado" <?= $chamado['status'] === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="descricao" class="form-label">Histórico / Relatório de Manutenção</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="5"><?= htmlspecialchars($chamado['descricao'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="fa-solid fa-square-poll-horizontal me-2"></i>Salvar Atualizações</button>
            <a href="chamados.php" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>