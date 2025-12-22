<?php
require 'db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS pre_cadastros (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        cpf_cnpj VARCHAR(50),
        email VARCHAR(255),
        telefone VARCHAR(50),
        endereco_obra VARCHAR(255),
        tipo_servico VARCHAR(100),
        mensagem TEXT,
        data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pendente', 'aprovado', 'arquivado') DEFAULT 'pendente',
        ip_origem VARCHAR(45)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Tabela 'pre_cadastros' criada ou verificada com sucesso!<br>";
    echo "Agora a página pública de cadastro funcionará.";

} catch (PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage();
}
?>
