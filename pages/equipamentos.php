<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$busca = trim($_GET['search'] ?? '');

if (!empty($busca)) {
    $stmt = $pdo->prepare("SELECT eq.*, cl.nome as cliente_nome FROM equipamentos eq JOIN clientes cl ON eq.cliente_id = cl.id WHERE eq.tipo LIKE :b OR eq.marca LIKE :b OR eq.modelo LIKE :b OR cl.nome LIKE :b ORDER BY eq.id DESC");
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT eq.*, cl.nome as cliente_nome FROM equipamentos eq JOIN clientes cl ON eq.cliente_id = cl.id ORDER BY eq.id DESC");
}
$equipamentos = $stmt->fetchAll();

if (isset($_GET['excluir'])) {
    $idExc = intval($_GET['excluir']);
    // Remove imagem física associada
    $fotoPath = $pdo->prepare("SELECT foto FROM equipamentos WHERE id = ?");
    $fotoPath->execute([$idExc]);
    $foto = $fotoPath->fetchColumn();
    if (!empty($foto) && file_exists(__DIR__ . '/../' . $foto)) {
        unlink(__DIR__ . '/../' . $foto);
    }
    
    $del = $pdo->prepare("DELETE FROM equipamentos WHERE id = ?");
    $del->execute([$idExc]);
    header("Location: equipamentos.php");
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 fw-bold">Ativos & Equipamentos</h2>
        <small class="text-muted">Parque tecnológico cadastrado para suporte laboratorial</small>
    </div>
    <a href="equipamento_novo.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Novo Equipamento</a>
</div>

<div class="custom-card">
    <form action="equipamentos.php" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Filtrar por marca, número de série, tipo ou cliente...">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover m-0">
            <thead>
                <tr>
                    <th>Miniatura</th>
                    <th>Cliente</th>
                    <th>Equipamento / Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Nº Série</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($equipamentos) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nenhum equipamento listado.</td></tr>
                <?php else: ?>
                    <?php foreach($equipamentos as $eq): ?>
                        <tr>
                            <td>
                                <?php if(!empty($eq['foto']) && file_exists(__DIR__ . '/../' . $eq['foto'])): ?>
                                    <img src="../<?= $eq['foto']; ?>" class="img-thumbnail-custom">
                                <?php else: ?>
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width:45px; height:45px;"><i class="fa-solid fa-laptop"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong class="text-white"><?= htmlspecialchars($eq['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?= htmlspecialchars($eq['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($eq['marca'] . ' / ' . $eq['modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="font-monospace text-warning"><?= htmlspecialchars($eq['numero_serie'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="equipamento_editar.php?id=<?= $eq['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="equipamentos.php?excluir=<?= $eq['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este ativo apagará todas as O.S vinculadas. Continuar?');"><i class="fa-solid fa-trash"></i></a>
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