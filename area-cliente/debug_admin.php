<?php
// debug_admin.php - Trace execution of gestao_admin_99.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Admin Debugger</h1>";
echo "1. Starting...<br>";

echo "2. Requiring includes/init.php...<br>";
try {
    require 'includes/init.php'; // This starts session
    echo "✅ Init Loaded.<br>";
} catch (Throwable $e) {
    die("❌ Init Failed: " . $e->getMessage());
}

// Admin Check usually happens in init or after
echo "3. Check Logic: Is Admin Logged?<br>";
if (isset($_SESSION['usuario_admin']) && $_SESSION['usuario_admin'] === true) {
    echo "✅ YES, Admin is logged.<br>";
} else {
    echo "⚠️ NO, Admin NOT logged (Session might be empty). Proceeding with tests anyway...<br>";
}

echo "4. Requiring includes/schema.php...<br>";
try {
    require 'includes/schema.php';
    echo "✅ Schema Loaded.<br>";
} catch (Throwable $e) {
    echo "❌ Schema Failed: " . $e->getMessage() . "<br>";
}

echo "5. Requiring config/taxas.php...<br>";
try {
    $taxas = require 'config/taxas.php';
    echo "✅ Taxas Loaded.<br>";
} catch (Throwable $e) {
    echo "❌ Taxas Failed: " . $e->getMessage() . "<br>";
}

echo "6. Testing DB Query (Clientes List)...<br>";
try {
    $clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome ASC")->fetchAll();
    echo "✅ Clientes Scanned. Count: " . count($clientes) . "<br>";
} catch (Throwable $e) {
    echo "❌ DB Query Failed: " . $e->getMessage() . "<br>";
}

echo "<h2 style='color:green'>🎉 ADMIN LOGIC SUCCESS!</h2>";
echo "If this works, the crash is likely in the HTML rendering loop.";
?>
