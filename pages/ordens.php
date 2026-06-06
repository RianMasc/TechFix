<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT os.*, cl.nome as cliente_nome, eq.tipo as eq_tipo, eq.marca as eq_marca 
                      FROM ordens_servico os 
                      JOIN clientes cl ON os.cliente_id = cl.id 
                      JOIN equipamentos eq ON os.equipamento_id = eq.id 
                      ORDER BY os.id DESC");
$ordens = $stmt->fetchAll();

if(isset($_GET['excluir'])) {
    $del = $pdo->prepare("DELETE FROM ordens_servico WHERE id = ?");
    $del->execute([intval($_GET['excluir'])]);
    header("Location: ordens.php");
    exit();
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 fw-bold">Ordens de Serviço</h2>
        <small class="text-muted">Gestão financeira e laboratorial de reparos em andamento</small>
    </div>
    <a href="os_nova.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Nova O.S.</a>
</div>

<div class="custom-card">
    <div class="table-responsive">
        <table class="table table-hover m-0">
            <thead>
                <tr>
                    <th>O.S</th>
                    <th>Cliente</th>
                    <th>Equipamento</th>
                    <th>Valor</th>
                    <th>Status Técnico</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($ordens) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma ordem de serviço aberta na bancada.</td></tr>
                <?php else: ?>
                    <?php foreach($ordens as $os): ?>
                        <tr>
                            <td>#<?= $os['id']; ?></td>
                            <td><strong class="text-white"><?= htmlspecialchars($os['cliente_nome'], ENT_QUOTES); ?></strong></td>
                            <td><?= htmlspecialchars($os['eq_tipo'] . ' (' . $os['eq_marca'] . ')', ENT_QUOTES); ?></td>
                            <td class="text-success fw-bold">R$ <?= number_format($os['valor'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                $colors = ['Recebido'=>'secondary','Em Análise'=>'info','Aguardando Peça'=>'danger','Em Reparo'=>'warning','Pronto'=>'primary','Entregue'=>'success'];
                                $badgeColor = $colors[$os['status']] ?? 'light';
                                ?>
                                <span class="badge bg-<?= $badgeColor; ?>"><?= $os['status']; ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="os_editar.php?id=<?= $os['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-sliders"></i></a>
                                    <a href="ordens.php?excluir=<?= $os['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir permanentemente esta OS?');"><i class="fa-solid fa-trash"></i></a>
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