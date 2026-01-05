<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Teste de PHP - Vilela Engenharia</h1>";
echo "<p>Se você está vendo isso, o PHP está funcionando.</p>";
echo "<p>Agora vamos tentar incluir o init.php...</p>";

try {
    require 'includes/init.php';
    echo "<p style='color:green'>Sucesso ao carregar init.php</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>Erro ao carregar init.php: " . $e->getMessage() . "</p>";
}

echo "<p>Agora schema.php...</p>";
try {
    require 'includes/schema.php';
    echo "<p style='color:green'>Sucesso ao carregar schema.php</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>Erro ao carregar schema.php: " . $e->getMessage() . "</p>";
}

echo "<p>Teste Completo.</p>";
?>
