<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erro = ""; $sucesso = "";
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id   = intval($_POST['cliente_id'] ?? 0);
    $tipo         = trim($_POST['tipo'] ?? '');
    $marca        = trim($_POST['marca'] ?? '');
    $modelo       = trim($_POST['modelo'] ?? '');
    $num_serie    = trim($_POST['numero_serie'] ?? '');
    $defeito      = trim($_POST['defeito'] ?? '');
    $fotoDestino  = null;

    if ($cliente_id === 0 || empty($tipo)) {
        $erro = "Selecione o proprietário e defina o tipo do componente tecnológico.";
    } else {
        // MELHORIA 6: Processamento Defensivo de Upload de Imagem
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensoesValidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesValidas)) {
                $erro = "Formato inválido. Permitidos: JPG, JPEG, PNG e WEBP.";
            } else {
                $dirTarget = __DIR__ . '/../uploads/equipamentos/';
                if (!is_dir($dirTarget)) {
                    mkdir($dirTarget, 0777, true);
                }
                $novoNome = md5(uniqid(rand(), true)) . '.' . $extensao;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dirTarget . $novoNome)) {
                    $fotoDestino = 'uploads/equipamentos/' . $novoNome;
                }
            }
        }

        if (empty($erro)) {
            $stmt = $pdo->prepare("INSERT INTO equipamentos (cliente_id, tipo, marca, modelo, numero_serie, defeito, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $tipo, $marca, $modelo, $num_serie, $defeito, $fotoDestino]);
            $sucesso = "Equipamento catalogado com sucesso.";
        }
    }
}
?>
<div class="mb-4"><h2 class="fw-bold">Novo Equipamento</h2></div>
<div class="custom-card" style="max-width:750px;">
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro; ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso; ?></div><?php endif; ?>

    <form action="equipamento_novo.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Proprietário / Cliente *</label>
            <select class="form-select" name="cliente_id" required>
                <option value="">-- Escolha o Cliente --</option>
                <?php foreach($clientes as $c): ?><option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['nome'], ENT_QUOTES,'UTF-8'); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><label class="form-label">Tipo Ativo *</label><input type="text" class="form-control" name="tipo" placeholder="Ex: Notebook" required></div>
            <div class="col-md-4"><label class="form-label">Marca</label><input type="text" class="form-control" name="marca" placeholder="Ex: Dell"></div>
            <div class="col-md-4"><label class="form-label">Modelo</label><input type="text" class="form-control" name="modelo" placeholder="Ex: Inspiron 15"></div>
        </div>
        <div class="mb-3"><label class="form-label">Número de Série / ID Operacional</label><input type="text" class="form-control" name="numero_serie"></div>
        <div class="mb-3"><label class="form-label">Defeito Inicial Relatado</label><textarea class="form-control" name="defeito" rows="3"></textarea></div>
        <div class="mb-4"><label class="form-label">Foto do Equipamento (Extensões aceitas: Imagens)</label><input type="file" class="form-control" name="foto"></div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Salvar Ativo</button>
        <a href="equipamentos.php" class="btn btn-outline-secondary">Voltar</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>