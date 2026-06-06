<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
verificarAutenticacao();

// Coleta de Informações base para os relatórios
$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalChamados = $pdo->query("SELECT COUNT(*) FROM chamados")->fetchColumn();
$abertos       = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Aberto'")->fetchColumn();
$andamento     = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Em andamento'")->fetchColumn();
$finalizados   = $pdo->query("SELECT COUNT(*) FROM chamados WHERE status = 'Finalizado'")->fetchColumn();
$totalOS       = $pdo->query("SELECT COUNT(*) FROM ordens_servico")->fetchColumn();

// Mapeamento de O.S. por status para enriquecer o relatório
$osRecebido    = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Recebido'")->fetchColumn();
$osAnalise     = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Em Análise'")->fetchColumn();
$osPeca        = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Aguardando Peça'")->fetchColumn();
$osReparo      = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Em Reparo'")->fetchColumn();
$osPronto      = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Pronto'")->fetchColumn();
$osEntregue    = $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status = 'Entregue'")->fetchColumn();

// SE GATILHADO O MODO DE IMPRESSÃO: Renderiza uma página HTML limpa e chama o driver de PDF/Impressora
if (isset($_GET['modo']) && $_GET['modo'] === 'imprimir') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Relatório Executivo - TechFix Solutions</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <style>
            body { background: #fff !important; color: #000 !important; font-family: Arial, sans-serif; padding: 20px; }
            .report-header { border-bottom: 3px solid #0d6efd; padding-bottom: 12px; margin-bottom: 30px; }
            .section-title { background: #f4f5f7 !important; color: #000 !important; padding: 6px 10px; border-left: 5px solid #0d6efd; font-weight: bold; margin-top: 25px; margin-bottom: 15px; }
            th { background-color: #f8f9fa !important; color: #000 !important; text-transform: uppercase; font-size: 11px; }
            @media print {
                .no-print { display: none !important; }
                body { padding: 0; }
            }
        </style>
    </head>
    <body>
        <div class="d-flex justify-content-between align-items-center mb-4 no-print bg-light p-3 rounded border">
            <span class="text-dark fw-bold">Previsão de Impressão do Relatório</span>
            <div>
                <button onclick="window.print();" class="btn btn-success fw-bold me-2"><i class="fa-solid fa-print"></i> Confirmar e Salvar como PDF</button>
                <a href="relatorios.php" class="btn btn-secondary">Voltar ao Sistema</a>
            </div>
        </div>

        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-primary fw-bold m-0" style="font-size: 26px;">TechFix Solutions</h1>
                <p class="text-muted m-0 small">Console de Governança de TI & Laboratório Técnico</p>
            </div>
            <div class="text-end text-muted small">
                <strong>Data de Emissão:</strong> <?= date('d/m/Y H:i'); ?><br>
                <strong>Escopo:</strong> Auditoria Acadêmica (2026)
            </div>
        </div>

        <div class="section-title">1. Volumetria Geral de Ativos</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Indicador Operacional</th>
                    <th class="text-center" style="width: 200px;">Registros Absolutos</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Clientes Corporativos e Pessoas Físicas Cadastradas</td><td class="text-center fw-bold"><?= $totalClientes; ?></td></tr>
                <tr><td>Tickets e Incidentes de Suporte Aberto (Chamados)</td><td class="text-center fw-bold"><?= $totalChamados; ?></td></tr>
                <tr><td>Ordens de Serviço Laboratoriais Solicitadas</td><td class="text-center fw-bold"><?= $totalOS; ?></td></tr>
            </tbody>
        </table>

        <div class="section-title">2. Distribuição Crítica de Chamados</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Status do Chamado</th>
                    <th class="text-center">Quantidade</th>
                    <th class="text-center">Representação Percentual</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Aberto (Ações Imediatas Requeridas)</td><td class="text-center text-danger fw-bold"><?= $abertos; ?></td><td class="text-center"><?= $totalChamados > 0 ? round(($abertos/$totalChamados)*100, 1) : 0; ?>%</td></tr>
                <tr><td>Em Andamento (Bancada/Remoto)</td><td class="text-center text-warning fw-bold"><?= $andamento; ?></td><td class="text-center"><?= $totalChamados > 0 ? round(($andamento/$totalChamados)*100, 1) : 0; ?>%</td></tr>
                <tr><td>Finalizado (Encerrado com Sucesso)</td><td class="text-center text-success fw-bold"><?= $finalizados; ?></td><td class="text-center"><?= $totalChamados > 0 ? round(($finalizados/$totalChamados)*100, 1) : 0; ?>%</td></tr>
            </tbody>
        </table>

        <div class="section-title">3. Fluxo de Trabalho de Ordens de Serviço (Laboratório)</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Fase da Ordem de Serviço</th>
                    <th class="text-center">Volume em Triagem</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Recebido (Aguardando Entrada)</td><td class="text-center"><?= $osRecebido; ?></td></tr>
                <tr><td>Em Análise Técnica</td><td class="text-center text-info fw-bold"><?= $osAnalise; ?></td></tr>
                <tr><td>Aguardando Peça de Reposição</td><td class="text-center text-danger fw-bold"><?= $osPeca; ?></td></tr>
                <tr><td>Em Reparo Físico</td><td class="text-center text-warning fw-bold"><?= $osReparo; ?></td></tr>
                <tr><td>Pronto (Aguardando Retirada)</td><td class="text-center text-primary fw-bold"><?= $osPronto; ?></td></tr>
                <tr><td>Entregue ao Parceiro (Encerrada)</td><td class="text-center text-success fw-bold"><?= $osEntregue; ?></td></tr>
            </tbody>
        </table>

        <div class="mt-5 pt-4 text-center border-top text-muted style="font-size: 11px;">
            TechFix Solutions Console © 2026 - Documento Interno de Validação Semestral.
        </div>

        <script>
            // Dispara automaticamente a caixa de diálogo de Salvamento/Impressão do Sistema Operacional
            window.onload = function() { window.print(); }
        </script>
    </body>
    </html>
    <?php
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-4">
    <h2 class="fw-bold m-0 text-white">Central de Relatórios</h2>
    <small class="text-muted">Geração analítica de dados gerenciais e exportação de relatórios</small>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="custom-card h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-layer-group text-primary me-2"></i>Métricas Consolidadas</h5>
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-light border-secondary d-flex justify-content-between"><span>Total de Clientes:</span> <strong><?= $totalClientes; ?></strong></li>
                    <li class="list-group-item bg-transparent text-light border-secondary d-flex justify-content-between"><span>Total de Chamados:</span> <strong><?= $totalChamados; ?></strong></li>
                    <li class="list-group-item bg-transparent text-light border-secondary d-flex justify-content-between"><span>Ordens de Serviço (O.S):</span> <strong><?= $totalOS; ?></strong></li>
                </ul>
            </div>
            <div class="mt-4">
                <a href="relatorios.php?modo=imprimir" class="btn btn-danger w-100 fw-bold"><i class="fa-solid fa-file-pdf me-2"></i>Gerar Impressão PDF</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="custom-card h-100">
            <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-pie-chart text-warning me-2"></i>Distribuição de Incidentes</h5>
            <div class="p-2">
                <div class="d-flex justify-content-between small text-muted mb-1"><span>Abertos:</span> <span><?= $abertos; ?></span></div>
                <div class="progress bg-dark mb-3" style="height: 10px;"><div class="progress-bar bg-danger" style="width: <?= $totalChamados>0?($abertos/$totalChamados)*100:0; ?>%"></div></div>

                <div class="d-flex justify-content-between small text-muted mb-1"><span>Em Andamento:</span> <span><?= $andamento; ?></span></div>
                <div class="progress bg-dark mb-3" style="height: 10px;"><div class="progress-bar bg-warning" style="width: <?= $totalChamados>0?($andamento/$totalChamados)*100:0; ?>%"></div></div>

                <div class="d-flex justify-content-between small text-muted mb-1"><span>Finalizados:</span> <span><?= $finalizados; ?></span></div>
                <div class="progress bg-dark" style="height: 10px;"><div class="progress-bar bg-success" style="width: <?= $totalChamados>0?($finalizados/$totalChamados)*100:0; ?>%"></div></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>