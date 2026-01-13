<?php
// session_test_1.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração de Sessão (Mesma do index.php)
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();

$_SESSION['teste_hora'] = date('H:i:s');
$_SESSION['teste_ok'] = true;

echo "<h1>Teste de Sessão: PASSO 1</h1>";
echo "<p>Sessão iniciada.</p>";
echo "<p>Valor salvo: " . $_SESSION['teste_hora'] . "</p>";
echo "<p><a href='session_test_2.php'>CLIQUE AQUI PARA O PASSO 2 (Verificar Persistência)</a></p>";
?>
