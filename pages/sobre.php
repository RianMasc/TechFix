<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="mb-4">
    <h2 class="fw-bold m-0 text-white">Sobre a Empresa</h2>
    <small class="text-muted">Informações institucionais e escopo de posicionamento de mercado</small>
</div>

<div class="custom-card">
    <div class="row g-4">
        <div class="col-md-6">
            <h5 class="text-primary fw-bold mb-3"><i class="fa-solid fa-id-card me-2"></i> Identificação</h5>
            <p><strong>Nome da Empresa:</strong> TechFix Solutions</p>
            <p><strong>Segmento Principal:</strong> Tecnologia da Informação, Suporte Técnico e Governança de TI.</p>
            <p><strong>Área de Atuação:</strong> Manutenção preventiva/corretiva de infraestrutura corporativa, gerenciamento de ativos, redes e service desk.</p>
        </div>
        <div class="col-md-6">
            <h5 class="text-primary fw-bold mb-3"><i class="fa-solid fa-bullseye me-2"></i> Posicionamento Estratégico</h5>
            <p><strong>Público-Alvo:</strong> Micro, pequenas e médias empresas (PMEs) que demandam gerenciamento profissional de infraestrutura sem o custo fixo de um departamento interno de TI.</p>
            <p><strong>Forma de Atendimento:</strong> Modelo híbrido, unindo suporte remoto ágil para soluções lógicas e atendimento presencial estruturado para manutenção de hardware e infraestrutura física.</p>
        </div>
        
        <div class="col-12"><hr class="border-secondary my-2"></div>
        
        <div class="col-md-6">
            <h5 class="text-primary fw-bold mb-2"><i class="fa-solid fa-boxes-stacked me-2"></i> Produtos & Serviços Oferecidos</h5>
            <ul>
                <li>Help Desk e Service Desk integrado para incidentes operacionais.</li>
                <li>Manutenção especializada de hardware (Desktops, Notebooks e Servidores).</li>
                <li>Gerenciamento de Ordens de Serviço laboratoriais e controle de ativos de clientes.</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h5 class="text-primary fw-bold mb-2"><i class="fa-solid fa-star me-2"></i> Diferenciais Competitivos</h5>
            <ul>
                <li>Centralização inteligente de demandas com controle rígido de status.</li>
                <li>Banco de dados em arquitetura relacional veloz e isolada.</li>
                <li>Geração em tempo real de relatórios analíticos de performance operacional para tomada de decisões.</li>
            </ul>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>