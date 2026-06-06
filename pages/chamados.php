<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$busca  = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$sql = "SELECT ch.*, cl.nome as cliente_nome 
        FROM chamados ch 
        JOIN clientes cl ON ch.cliente_id = cl.id WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (ch.titulo LIKE :busca OR ch.descricao LIKE :busca OR cl.nome LIKE :busca)";
    $params[':busca'] = "%$busca%";
}
if (!empty($status)) {
    $sql .= " AND ch.status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY ch.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll();

if (isset($_GET['excluir'])) {
    $idExc = intval($_GET['excluir']);
    $del = $pdo->prepare("DELETE FROM chamados WHERE id = ?");
    $del->execute([$idExc]);
    header("Location: chamados.php");
    exit();
}
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
    <div>
        <h2 class="m-0 fw-bold">Chamados de Suporte Técnico</h2>
        <small class="text-muted">Controle total de incidentes e ordens de serviço em andamento</small>
    </div>
    <a href="chamado_novo.php" class="btn btn-primary"><i class="fa-solid fa-folder-plus me-2"></i>Abrir Chamado</a>
</div>

<div class="custom-card">
    <form action="chamados.php" method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar por título, escopo ou nome do cliente...">
        </div>
        <div class="col-md-4">
            <select class="form-select" name="status">
                <option value="">-- Filtrar por Status (Todos) --</option>
                <option value="Aberto" <?= $status === 'Aberto' ? 'selected' : ''; ?>>Aberto</option>
                <option value="Em andamento" <?= $status === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                <option value="Finalizado" <?= $status === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-primary w-100" type="submit"><i class="fa-solid fa-filter"></i></button>
            <?php if(!empty($busca) || !empty($status)): ?>
                <a href="chamados.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-rotate-left"></i></a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover m-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente Solicitante</th>
                    <th>Título / Assunto</th>
                    <th>Abertura</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($chamados) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum ticket pendente ou encontrado sob estes filtros.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($chamados as $ch): ?>
                        <tr>
                            <td>#<?= $ch['id']; ?></td>
                            <td><strong class="text-white"><?= htmlspecialchars($ch['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?= htmlspecialchars($ch['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ch['data_abertura'])); ?></td>
                            <td>
                                <?php if($ch['status'] === 'Aberto'): ?>
                                    <span class="badge bg-danger">Aberto</span>
                                <?php elseif($ch['status'] === 'Em andamento'): ?>
                                    <span class="badge bg-warning text-dark">Em andamento</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Finalizado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="chamado_editar.php?id=<?= $ch['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-sliders"></i></a>
                                    <a href="chamados.php?excluir=<?= $ch['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Atenção! Você tem certeza absoluta que deseja remover este chamado?\nEsta ação apagará os dados do ticket permanentemente.');">
                                        <i class="fa-solid fa-trash-can"></i>
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