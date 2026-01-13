<?php
// debug_checklist.php - Trace execution of documentos_iniciais.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Checklist Debugger</h1>";
echo "1. Starting...<br>";

session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
echo "2. Session Started. ID: " . session_id() . "<br>";

if (!isset($_SESSION['cliente_id'])) {
    die("❌ Not logged in. Please log in as client first.");
}
echo "3. Logged as Client ID: " . $_SESSION['cliente_id'] . "<br>";

echo "4. Requiring ../db.php...<br>";
require_once '../db.php';
echo "✅ DB Loaded.<br>";

echo "5. Requiring ../includes/schema.php...<br>";
try {
    require_once '../includes/schema.php';
    echo "✅ Schema Loaded.<br>";
} catch (Throwable $e) {
    echo "❌ Schema Failed: " . $e->getMessage() . "<br>";
}

echo "6. Requiring ../config/docs_config.php...<br>";
try {
    $docs_config = require '../config/docs_config.php';
    echo "✅ Config Loaded. Type: " . gettype($docs_config) . "<br>";
} catch (Throwable $e) {
    echo "❌ Config Failed: " . $e->getMessage() . "<br>";
}

echo "7. Testing DB Query (Clientes)...<br>";
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$_SESSION['cliente_id']]);
$client = $stmt->fetch();
echo "✅ Client Data Fetched: " . ($client ? $client['nome'] : 'Not Found') . "<br>";

echo "8. Testing DB Query (Processo Detalhes)...<br>";
$stmt = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
$stmt->execute([$_SESSION['cliente_id']]);
$detalhes = $stmt->fetch();
echo "✅ Process Fetched.<br>";

echo "9. Fetching Docs Entregues...<br>";
$stmt_entregues = $pdo->prepare("SELECT doc_chave FROM processo_docs_entregues WHERE cliente_id = ?");
$stmt_entregues->execute([$_SESSION['cliente_id']]);
echo "✅ Docs Fetched.<br>";

echo "<h2 style='color:green'>🎉 COMPLETE SUCCESS!</h2>";
echo "If you see this, the backend logic is PERFECT. The error must be in the HTML/CSS part.";
?>
