<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$termo = trim($_GET['search'] ?? '');

if (!empty($termo)) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE nome LIKE :termo OR email LIKE :termo OR telefone LIKE :termo ORDER BY nome ASC");
    $stmt->execute([':termo' => "%$termo%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM clientes ORDER BY nome ASC");
}
$clientes = $stmt->fetchAll();

if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    $del = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $del->execute([$idExcluir]);
    header("Location: clientes.php");
    exit();
}
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
    <div>
        <h2 class="m-0 fw-bold">Gerenciamento de Clientes</h2>
        <small class="text-muted">Cadastro e controle de parceiros corporativos e PF</small>
    </div>
    <a href="cliente_novo.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Novo Cliente</a>
</div>

<div class="custom-card">
    <form action="clientes.php" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($termo, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pesquise por nome, e-mail ou telefone do parceiro...">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Filtrar</button>
            <?php if (!empty($termo)): ?>
                <a href="clientes.php" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover m-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Completo / Razão</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Data Registro</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($clientes) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Nenhum cliente atende aos critérios da consulta executada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td>#<?= $c['id']; ?></td>
                            <td><strong class="text-white"><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($c['telefone'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($c['endereco'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= date('d/m/Y', strtotime($c['data_cadastro'])); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="cliente_editar.php?id=<?= $c['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-user-gear"></i></a>
                                    <a href="clientes.php?excluir=<?= $c['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Atenção! Você tem certeza absoluta que deseja remover este cliente?\nEsta ação apagará os dados permanentemente e todos os chamados vinculados a ele.');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>