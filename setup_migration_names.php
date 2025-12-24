<?php
// Script de Migração de Nomes (Primeiro Nome / Sobrenome)
// Arquivo temporário: setup_migration_names.php

require_once 'area-cliente/db.php';

try {
    echo "<h1>Migração de Banco de Dados</h1>";

    // 1. Adicionar Coluna 'sobrenome' se não existir
    try {
        $pdo->query("SELECT sobrenome FROM clientes LIMIT 1");
        echo "<p>✅ Coluna 'sobrenome' já existe.</p>";
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE clientes ADD COLUMN sobrenome VARCHAR(255) AFTER nome");
        echo "<p>✅ Coluna 'sobrenome' criada com sucesso.</p>";
    }

    // 2. Migrar Dados
    $clientes = $pdo->query("SELECT * FROM clientes")->fetchAll();
    $count = 0;

    foreach ($clientes as $c) {
        $nome_full = trim($c['nome']);
        
        // Verifica se já parece migrado (sobrenome preenchido?)
        // Se sobrenome já tem valor, pula (assumindo que já rodou ou é novo)
        if (!empty($c['sobrenome'])) {
            continue;
        }

        // Se só tem um nome, sobrenome fica vazio
        if (strpos($nome_full, ' ') === false) {
            // Só primeiro nome
            // Não precisa mexer se o objetivo é deixar o primeiro nome no 'nome'
            // Mas se 'nome' for igual 'nome_full', está ok.
            continue;
        }

        // Tem espaços. Vamos separar.
        $partes = explode(' ', $nome_full, 2); // Limita em 2 partes
        $primeiro = $partes[0];
        $sobrenome = $partes[1] ?? '';

        // Atualiza
        $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, sobrenome = ? WHERE id = ?");
        $stmt->execute([$primeiro, $sobrenome, $c['id']]);
        $count++;
        echo "<div>Migrado ID #{$c['id']}: '{$nome_full}' -> '{$primeiro}' | '{$sobrenome}'</div>";
    }

    echo "<p>✅ Processo concluído. $count registros atualizados.</p>";
    echo "<p>Pode apagar este arquivo ou mantê-lo.</p>";
    echo "<a href='area-cliente/gestao_admin_99.php'>Voltar para Admin</a>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Erro no Banco de Dados: " . $e->getMessage() . "</h3>";
}
?>
