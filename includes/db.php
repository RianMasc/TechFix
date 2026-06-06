<?php
$dbDir = __DIR__ . '/../database';
$dbFile = $dbDir . '/techfix.db';

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Tabelas Existentes Preservadas
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL,
        telefone TEXT,
        endereco TEXT,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS chamados (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        titulo TEXT NOT NULL,
        descricao TEXT,
        status TEXT CHECK(status IN ('Aberto', 'Em andamento', 'Finalizado')) DEFAULT 'Aberto',
        data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    );");

    // MELHORIA 4 & 6: Nova Tabela de Equipamentos com Campo de Imagem
    $pdo->exec("CREATE TABLE IF NOT EXISTS equipamentos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        tipo TEXT NOT NULL,
        marca TEXT,
        modelo TEXT,
        numero_serie TEXT,
        defeito TEXT,
        foto TEXT,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    );");

    // MELHORIA 5: Nova Tabela de Ordens de Serviço
    $pdo->exec("CREATE TABLE IF NOT EXISTS ordens_servico (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        equipamento_id INTEGER NOT NULL,
        descricao TEXT NOT NULL,
        diagnostico TEXT,
        valor REAL DEFAULT 0.0,
        status TEXT CHECK(status IN ('Recebido', 'Em Análise', 'Aguardando Peça', 'Em Reparo', 'Pronto', 'Entregue')) DEFAULT 'Recebido',
        data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
        FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id) ON DELETE CASCADE
    );");

    // Administrador base
    $check = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE email = 'riri@techfix.com'")->fetchColumn();
    if ($check == 0) {
        $hash = password_hash('rian123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $ins->execute(['Rian Rocha Mascarenhas', 'riri@techfix.com', $hash]);
    }
} catch (PDOException $e) {
    die("Falha na inicialização do Banco de Dados: " . $e->getMessage());
}
?>