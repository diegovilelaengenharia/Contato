<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Ambiente e Banco de Dados</h1>";

echo "<h2>1. Teste de PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PHP executando OK.<br>";

echo "<h2>2. Teste de Conexão com Banco de Dados</h2>";

if (!file_exists('db.php')) {
    die("❌ ERRO: Arquivo db.php não encontrado no diretório atual.");
}

echo "Arquivo db.php encontrado.<br>";

try {
    require 'db.php';
    
    if (isset($pdo)) {
        echo "✅ Conexão PDO estabelecida com sucesso!<br>";
        
        $stmt = $pdo->query("SELECT VERSION()");
        $version = $stmt->fetchColumn();
        echo "Versão do MySQL: " . $version . "<br>";
        
        echo "<h3>Listando Tabelas:</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "⚠️ Nenhuma tabela encontrada no banco de dados.<br>";
        } else {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>" . htmlspecialchars($table) . "</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "❌ ERRO: Variável \$pdo não definida após incluir db.php.<br>";
    }

} catch (PDOException $e) {
    echo "❌ <strong>Erro Fatal de Banco de Dados:</strong> " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ <strong>Erro Genérico:</strong> " . $e->getMessage() . "<br>";
}

echo "<h2>3. Teste de Sessão</h2>";
session_start();
$_SESSION['teste_diag'] = 'Funciona';
echo "Sessão iniciada. ID: " . session_id() . "<br>";
if (isset($_SESSION['teste_diag']) && $_SESSION['teste_diag'] == 'Funciona') {
    echo "✅ Leitura e Escrita de Sessão OK.<br>";
} else {
    echo "❌ Falha na Sessão PHP.<br>";
}
?>
