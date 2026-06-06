<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Contadores Existentes Preservados
$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalChamados = $pdo->query("SELECT COUNT(*) FROM chamados")->fetchColumn();
$abertos       = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Aberto'")->fetchColumn();
$andamento     = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Em andamento'")->fetchColumn();
$finalizados   = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Finalizado'")->fetchColumn();

// Coleta de dados reais para o Gráfico 1 (Status de Chamados)
$chartStatusData = [$abertos, $andamento, $finalizados];

// Coleta de dados reais para o Gráfico 2 (Clientes cadastrados por mês no ano corrente de 2026)
$clientesPorMes = array_fill(1, 12, 0);
$resMes = $pdo->query("SELECT strftime('%m', data_cadastro) as mes, COUNT(*) as qtd FROM clientes WHERE strftime('%Y', data_cadastro) = '2026' GROUP BY mes")->fetchAll();
foreach ($resMes as $r) {
    $clientesPorMes[intval($r['mes'])] = intval($r['qtd']);
}
$chartMesData = array_values($clientesPorMes);

$ultimosChamados = $pdo->query("SELECT ch.*, cl.nome as cliente_nome FROM chamados ch JOIN clientes cl ON ch.cliente_id = cl.id ORDER BY ch.id DESC LIMIT 5")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 fw-bold">Dashboard Executivo</h2>
        <small class="text-muted">TechFix Solutions — Visão macro operacional</small>
    </div>
    <div class="text-muted font-monospace small">Ano Operacional: 2026</div>
</div>

<div class="row">
    <div class="col-md-4 col-xl-2-4 col-sm-6 mb-4">
        <div class="custom-card border-start border-4 border-primary h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted text-uppercase small">Clientes Cadastrados</h6>
                    <h2 class="fw-bold text-white m-0 mt-2" style="font-size: 2.2rem;"><?= $totalClientes; ?></h2>
                </div>
                <div class="bg-primary-subtle text-primary rounded p-2 mt-1"><i class="fa-solid fa-users fa-lg"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2-4 col-sm-6 mb-4">
        <div class="custom-card border-start border-4 border-secondary h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted text-uppercase small">Total de Chamados</h6>
                    <h2 class="fw-bold text-white m-0 mt-2" style="font-size: 2.2rem;"><?= $totalChamados; ?></h2>
                </div>
                <div class="bg-secondary-subtle text-secondary rounded p-2 mt-1"><i class="fa-solid fa-ticket fa-lg"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2-4 col-sm-6 mb-4">
        <div class="custom-card border-start border-4 border-danger h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted text-uppercase small">Chamados Abertos</h6>
                    <h2 class="fw-bold text-danger m-0 mt-2" style="font-size: 2.2rem;"><?= $abertos; ?></h2>
                </div>
                <div class="bg-danger-subtle text-danger rounded p-2 mt-1"><i class="fa-solid fa-folder-open fa-lg"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-2-4 col-sm-6 mb-4">
        <div class="custom-card border-start border-4 border-warning h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted text-uppercase small">Em Andamento</h6>
                    <h2 class="fw-bold text-warning m-0 mt-2" style="font-size: 2.2rem;"><?= $andamento; ?></h2>
                </div>
                <div class="bg-warning-subtle text-warning rounded p-2 mt-1"><i class="fa-solid fa-spinner fa-spin fa-lg"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-2-4 col-sm-12 mb-4">
        <div class="custom-card border-start border-4 border-success h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted text-uppercase small">Finalizados</h6>
                    <h2 class="fw-bold text-success m-0 mt-2" style="font-size: 2.2rem;"><?= $finalizados; ?></h2>
                </div>
                <div class="bg-success-subtle text-success rounded p-2 mt-1"><i class="fa-solid fa-circle-check fa-lg"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="custom-card">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Chamados por Status</h5>
            <div style="position: relative; height:250px;">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="custom-card">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-line text-success me-2"></i>Clientes Cadastrados em 2026</h5>
            <div style="position: relative; height:250px;">
                <canvas id="chartEvolucao"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gráfico 1: Rosca/Pizza para Status
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Aberto', 'Em andamento', 'Finalizado'],
            datasets: [{
                data: <?= json_encode($chartStatusData); ?>,
                backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#fff' } } } }
    });

    // Gráfico 2: Linha para evolução mensal
    new Chart(document.getElementById('chartEvolucao'), {
        type: 'line',
        data: {
            labels: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            datasets: [{
                label: 'Novos Clientes',
                data: <?= json_encode($chartMesData); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: { 
                y: { ticks: { color: '#fff' }, grid: { color: '#2d3247' } },
                x: { ticks: { color: '#fff' }, grid: { color: '#2d3247' } }
            }
        }
    });
});
</script>

<div class="custom-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-history me-2 text-primary"></i>Últimas Demandas</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover m-0">
            <thead>
                <tr><th>ID</th><th>Cliente</th><th>Título</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosChamados as $ch): ?>
                    <tr>
                        <td>#<?= $ch['id']; ?></td>
                        <td><?= htmlspecialchars($ch['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($ch['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge bg-<?= $ch['status']=='Aberto'?'danger':($ch['status']=='Em andamento'?'warning':'success'); ?>"><?= $ch['status']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>