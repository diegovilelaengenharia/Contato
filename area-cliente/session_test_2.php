<?php
// session_test_2.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração de Sessão
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();

echo "<h1>Teste de Sessão: PASSO 2</h1>";

if(isset($_SESSION['teste_ok']) && $_SESSION['teste_ok'] === true) {
    echo "<h2 style='color:green'>✅ SESSÃO FUNCIONANDO!</h2>";
    echo "<p>Valor recuperado: " . $_SESSION['teste_hora'] . "</p>";
    echo "<p>Isso significa que o servidor consegue lembrar quem está logado.</p>";
} else {
    echo "<h2 style='color:red'>❌ FALHA NA SESSÃO</h2>";
    echo "<p>Não foi possível recuperar os dados. O login nunca vai funcionar assim.</p>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}
?>
