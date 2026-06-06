<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id = ?");
$stmt->execute([$id]);
$eq = $stmt->fetch();
if(!$eq) { header("Location: equipamentos.php"); exit(); }

$erro = ""; $sucesso = "";
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id   = intval($_POST['cliente_id'] ?? 0);
    $tipo         = trim($_POST['tipo'] ?? '');
    $marca        = trim($_POST['marca'] ?? '');
    $modelo       = trim($_POST['modelo'] ?? '');
    $num_serie    = trim($_POST['numero_serie'] ?? '');
    $defeito      = trim($_POST['defeito'] ?? '');
    $fotoDestino  = $eq['foto'];

    if (empty($tipo)) {
        $erro = "O tipo do ativo é obrigatório.";
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'])) {
                if(!empty($eq['foto']) && file_exists(__DIR__ . '/../' . $eq['foto'])) unlink(__DIR__ . '/../' . $eq['foto']);
                $novoNome = md5(uniqid(rand(), true)) . '.' . $extensao;
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../uploads/equipamentos/' . $novoNome);
                $fotoDestino = 'uploads/equipamentos/' . $novoNome;
            }
        }
        $up = $pdo->prepare("UPDATE equipamentos SET cliente_id=?, tipo=?, marca=?, modelo=?, numero_serie=?, defeito=?, foto=? WHERE id=?");
        $up->execute([$cliente_id, $tipo, $marca, $modelo, $num_serie, $defeito, $fotoDestino, $id]);
        header("Location: equipamentos.php");
        exit();
    }
}
?>
<div class="mb-4"><h2 class="fw-bold">Editar Equipamento #<?= $id; ?></h2></div>
<div class="custom-card" style="max-width:750px;">
    <form action="equipamento_editar.php?id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Cliente Proprietário</label>
            <select class="form-select" name="cliente_id">
                <?php foreach($clientes as $c): ?>
                    <option value="<?= $c['id']; ?>" <?= $eq['cliente_id']==$c['id']?'selected':''; ?>><?= htmlspecialchars($c['nome'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Tipo Ativo</label><input type="text" class="form-control" name="tipo" value="<?= htmlspecialchars($eq['tipo'], ENT_QUOTES); ?>" required></div>
        <div class="row mb-3">
            <div class="col-md-6"><label class="form-label">Marca</label><input type="text" class="form-control" name="marca" value="<?= htmlspecialchars($eq['marca'], ENT_QUOTES); ?>"></div>
            <div class="col-md-6"><label class="form-label">Modelo</label><input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($eq['modelo'], ENT_QUOTES); ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label">Número de Série</label><input type="text" class="form-control" name="numero_serie" value="<?= htmlspecialchars($eq['numero_serie'], ENT_QUOTES); ?>"></div>
        <div class="mb-3"><label class="form-label">Defeito Relatado</label><textarea class="form-control" name="defeito" rows="3"><?= htmlspecialchars($eq['defeito'], ENT_QUOTES); ?></textarea></div>
        <div class="mb-4"><label class="form-label">Substituir Foto</label><input type="file" class="form-control" name="foto"></div>
        <button type="submit" class="btn btn-warning text-dark fw-bold">Atualizar Ativo</button>
        <a href="equipamentos.php" class="btn btn-outline-secondary">Cancelar</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>