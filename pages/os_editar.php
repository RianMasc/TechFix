<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id = ?");
$stmt->execute([$id]);
$os = $stmt->fetch();
if(!$os) { header("Location: ordens.php"); exit(); }

$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();
$equipamentos = $pdo->query("SELECT id, tipo, marca FROM equipamentos")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc   = trim($_POST['descricao'] ?? '');
    $diag   = trim($_POST['diagnostico'] ?? '');
    $valor  = floatval($_POST['valor'] ?? 0.0);
    $status = trim($_POST['status'] ?? 'Recebido');

    $up = $pdo->prepare("UPDATE ordens_servico SET descricao=?, diagnostico=?, valor=?, status=? WHERE id=?");
    $up->execute([$desc, $diag, $valor, $status, $id]);
    header("Location: ordens.php");
    exit();
}
?>
<div class="mb-4"><h2 class="fw-bold">Manutenção O.S. #<?= $id; ?></h2></div>
<div class="custom-card" style="max-width:800px;">
    <form action="os_editar.php?id=<?= $id; ?>" method="POST">
        <div class="row mb-3">
            <div class="col-md-6"><label class="form-label">Valor do Serviço Atualizado</label><input type="number" step="0.01" class="form-control" name="valor" value="<?= $os['valor']; ?>"></div>
            <div class="col-md-6">
                <label class="form-label">Fluxo de Trabalho</label>
                <select class="form-select" name="status">
                    <?php foreach(['Recebido','Em Análise','Aguardando Peça','Em Reparo','Pronto','Entregue'] as $st): ?>
                        <option value="<?= $st; ?>" <?= $os['status']==$st?'selected':''; ?>><?= $st; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3"><label class="form-label">Descrição das Queixas do Ativo</label><textarea class="form-control" name="descricao" rows="3" required><?= htmlspecialchars($os['descricao'], ENT_QUOTES); ?></textarea></div>
        <div class="mb-4"><label class="form-label">Laudo Técnico Atualizado</label><textarea class="form-control" name="diagnostico" rows="3"><?= htmlspecialchars($os['diagnostico'] ?? '', ENT_QUOTES); ?></textarea></div>
        <button type="submit" class="btn btn-warning text-dark fw-bold">Atualizar Ordem</button>
        <a href="ordens.php" class="btn btn-outline-secondary">Cancelar</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>