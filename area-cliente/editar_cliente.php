<?php
// editar_cliente.php

// Iniciando sessão com o nome correto
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
// Debug para erro 500
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: index.php');
    exit;
}

// Output Buffering para evitar erro de Header sent
ob_start();

require 'db.php';
// Ensure schema is up to date (Self-Healing)
require_once 'includes/schema.php';

$cliente_id = $_GET['id'] ?? null;
if (!$cliente_id) {
    die("ID do cliente não fornecido.");
}

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("Cliente não encontrado.");
}

// Buscar detalhes
// Buscar detalhes
$stmtDet = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
$stmtDet->execute([$cliente_id]);
$detalhes = $stmtDet->fetch();

// Buscar campos extras
try {
    $stmtEx = $pdo->prepare("SELECT * FROM processo_campos_extras WHERE cliente_id = ?");
    $stmtEx->execute([$cliente_id]);
    $campos_extras = $stmtEx->fetchAll();
} catch (Exception $e) {
    // Tabela não existe? Criar agora.
    $pdo->exec("CREATE TABLE IF NOT EXISTS processo_campos_extras (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        valor TEXT,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");
    $campos_extras = [];
}

// Arrays para dropdowns
$tipos_pessoa = ['Fisica', 'Juridica'];
$estados_civil = ['Solteiro', 'Casado', 'Divorciado', 'Viuvo', 'Uniao Estavel'];

// Msg Feedback
$msg_alert = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'success_update') $msg_alert = "<script>alert('✅ Dados atualizados com sucesso!');</script>";
    if($_GET['msg'] == 'welcome') $msg_alert = "<script>alert('✅ Cliente criado com sucesso! Continue editando abaixo.');</script>";
    if($_GET['msg'] == 'error') $msg_alert = "<script>alert('❌ Erro: " . htmlspecialchars($_GET['details'] ?? 'Desconhecido') . "');</script>";
}
if(isset($_GET['new']) && $_GET['new']==1) $msg_alert = "<script>alert('✅ Cadastro aprovado! Complete os dados agora.');</script>";

echo $msg_alert;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cliente | Vilela Engenharia</title>
    <link href="https://fonts.googleapis.com/css2?family=outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        /* Sincronizando com admin_style.css */
        :root {
            --primary: var(--color-primary, #146c43);
            --primary-hover: #0f5132;
            --bg-page: var(--color-bg, #f8f9fa);
            --bg-card: var(--color-surface, #ffffff);
            --text-main: var(--color-text, #2c3e50);
            --text-sub: var(--color-text-subtle, #7f8c8d);
            --border-color: var(--color-border, #e2e8f0);
        }
        
        body { 
            background: var(--bg-page); 
            font-family: 'Outfit', sans-serif; 
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .main-wrapper {
            max-width: 1600px; 
            margin: 0 auto; 
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        /* Header */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .page-title h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            display: flex; 
            align-items: center; 
            gap: 10px;
        }

        .page-title span {
            font-size: 0.9rem;
            color: var(--text-sub);
            font-weight: 400;
            background: #eee;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .btn-close {
            background: #eef2f5;
            color: #555;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-close:hover { background: #dfe4ea; color: #333; }

        /* Form Structure */
        .form-container {
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.04);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .section-header {
            background: #fdfdfd;
            padding: 12px 25px;
            border-bottom: 1px solid var(--border-color);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-header:first-child { border-top: none; }

        .section-header h2 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-icon {
            width: 32px; height: 32px;
            background: #e6f4ea;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .section-body {
            padding: 20px 25px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            background: #fbfbfb;
            transition: all 0.2s;
            font-family: 'Outfit', sans-serif;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(20, 108, 67, 0.1);
        }

        .form-group input[readonly] {
            background: #eee;
            color: #888;
            cursor: not-allowed;
        }

        /* Sticky Footer */
        .sticky-footer {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px 30px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.03);
            gap: 15px;
        }
        
        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(20, 108, 67, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-save:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media(max-width: 768px) {
            body { padding: 10px; }
            .section-body { padding: 20px; }
            .sticky-footer { flex-direction: column; }
            .btn-save { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <?php include 'includes/form_cliente_template.php'; ?>
    </div>


</body>
</html>
