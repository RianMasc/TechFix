<?php $pagina = basename($_SERVER['PHP_SELF']); ?>
<style>
    #sidebar {
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        background-color: #1a1d29;
        border-right: 1px solid var(--border-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid var(--border-color); text-align: center; }
    .sidebar-brand h4 { color: #0d6efd; font-weight: 700; margin: 0; }
    .sidebar-profile { padding: 1rem; background-color: #151821; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
    .sidebar-nav li a { padding: 0.8rem 1.5rem; display: flex; align-items: center; color: #a0aec0; text-decoration: none; border-left: 4px solid transparent; transition: all 0.2s; }
    .sidebar-nav li a:hover { background-color: #222636; color: #fff; }
    .sidebar-nav li.active a { background-color: #1e2538; color: #0d6efd; border-left-color: #0d6efd; font-weight: bold; }
    .sidebar-nav li a i { margin-right: 12px; width: 20px; text-align: center; }
</style>
<div id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fa-solid fa-screwdriver-wrench"></i> TechFix</h4>
    </div>
    <div class="sidebar-profile d-flex align-items-center">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">
            <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'A', 0, 1)); ?>
        </div>
        <div class="text-truncate">
            <strong class="d-block text-white"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
            <small class="text-muted">Analista Sênior</small>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="<?= ($pagina == 'dashboard.php') ? 'active' : ''; ?>"><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li class="<?= (strpos($pagina, 'cliente') !== false) ? 'active' : ''; ?>"><a href="clientes.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
        <li class="<?= (strpos($pagina, 'equipamento') !== false) ? 'active' : ''; ?>"><a href="equipamentos.php"><i class="fa-solid fa-laptop"></i> Equipamentos</a></li>
        <li class="<?= (strpos($pagina, 'chamado') !== false) ? 'active' : ''; ?>"><a href="chamados.php"><i class="fa-solid fa-ticket"></i> Chamados</a></li>
        <li class="<?= (strpos($pagina, 'ordem') !== false || strpos($pagina, 'os_') !== false) ? 'active' : ''; ?>"><a href="ordens.php"><i class="fa-solid fa-file-invoice-dollar"></i> Ordens de Serviço</a></li>
        <li class="<?= ($pagina == 'relatorios.php') ? 'active' : ''; ?>"><a href="relatorios.php"><i class="fa-solid fa-file-pdf"></i> Relatórios</a></li>
        <li class="<?= ($pagina == 'sobre.php') ? 'active' : ''; ?>"><a href="sobre.php"><i class="fa-solid fa-building"></i> Sobre a Empresa</a></li>
    </ul>
    <div class="p-3 border-top border-secondary-subtle">
        <a href="../logout.php" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-power-off me-2"></i>Sair</a>
    </div>
</div>