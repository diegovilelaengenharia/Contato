<?php
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
require_once '../db.php';

// VERIFICAR LOGIN
if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../index.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// BUSCAR DADOS DO CLIENTE (Necessário para o Header/Avatar)
$stmt_cli = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt_cli->execute([$cliente_id]);
$cliente = $stmt_cli->fetch(PDO::FETCH_ASSOC);

// BUSCAR DETALHES DO PROCESSO
$stmt_det = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
$stmt_det->execute([$cliente_id]);
$detalhes = $stmt_det->fetch(PDO::FETCH_ASSOC);

// LOAD CONFIG
$docs_config = require '../config/docs_config.php';
$processos = $docs_config['processes'];
$todos_docs = $docs_config['document_registry'];

// Identificar Processo do Cliente
$tipo_chave = ($detalhes && isset($detalhes['tipo_processo_chave'])) ? $detalhes['tipo_processo_chave'] : '';
$proc_data = $processos[$tipo_chave] ?? null;

// Buscar Status de Entrega
$stmt_entregues = $pdo->prepare("SELECT doc_chave FROM processo_docs_entregues WHERE cliente_id = ?");
$stmt_entregues->execute([$cliente_id]);
$entregues = $stmt_entregues->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos Iniciais</title>
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- STYLES -->
    <link rel="stylesheet" href="css/style.css?v=3.0">
    <link rel="stylesheet" href="css/header-premium.css?v=<?= time() ?>">
    
    <style>
        .doc-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.2s;
        }
        .doc-card.entregue {
            border-left: 5px solid #198754;
            background: #f8fff9;
        }
        .doc-card.pendente {
            border-left: 5px solid #dc3545;
        }
        .doc-icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1.2rem;
        }
        .doc-info { flex: 1; }
        .doc-title { font-weight: 600; color: #333; font-size: 0.95rem; line-height: 1.3; }
        .doc-status { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-top: 3px; display: inline-block; padding: 2px 8px; border-radius: 8px; }
        
        .status-ok { color: #198754; background: #d1e7dd; }
        .status-pend { color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

    <div class="app-container" style="padding: 20px;">
        
        <!-- HEADER -->
        <header style="display: flex; align-items: center; margin-bottom: 25px;">
            <a href="index.php" style="background: white; border: 1px solid #ddd; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #333; margin-right: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none;">
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
            <div>
                <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: #888; font-weight: 700;">Checklist</span>
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #222; margin: 0;">Documentos Iniciais <span style="font-size:0.8rem; color:#aaa; font-weight:400;">v3.1</span></h1>
            </div>
        </header>

        <?php if($proc_data): ?>
            
            <div style="background: #e7f1ff; border: 1px solid #b6d4fe; color: #084298; padding: 15px; border-radius: 12px; margin-bottom: 25px;">
                <strong style="display:block; margin-bottom:5px;">Processo Identificado:</strong>
                <span style="font-size: 1.1rem; font-weight: 700;"><?= htmlspecialchars($proc_data['titulo']) ?></span>
            </div>

            <h3 style="font-size: 1rem; color: #555; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;">Documentos Obrigatórios</h3>
            
            <?php foreach($proc_data['docs_obrigatorios'] as $d_key): 
                $is_ok = in_array($d_key, $entregues);
            ?>
                <div class="doc-card <?= $is_ok ? 'entregue' : 'pendente' ?>">
                    <div class="doc-icon" style="background: <?= $is_ok ? '#d1e7dd' : '#f8d7da' ?>; color: <?= $is_ok ? '#198754' : '#dc3545' ?>;">
                        <?= $is_ok ? '✓' : '!' ?>
                    </div>
                    <div class="doc-info">
                        <div class="doc-title"><?= htmlspecialchars($todos_docs[$d_key] ?? $d_key) ?></div>
                        <span class="doc-status <?= $is_ok ? 'status-ok' : 'status-pend' ?>">
                            <?= $is_ok ? 'Recebido' : 'Pendente' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(!empty($proc_data['docs_excepcionais'])): ?>
                <h3 style="font-size: 1rem; color: #555; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;">Documentos Excepcionais (Se aplicável)</h3>
                
                <?php foreach($proc_data['docs_excepcionais'] as $d_key): 
                    $is_ok = in_array($d_key, $entregues);
                ?>
                    <div class="doc-card <?= $is_ok ? 'entregue' : 'pendente' ?>">
                        <div class="doc-icon" style="background: <?= $is_ok ? '#d1e7dd' : '#fff3cd' ?>; color: <?= $is_ok ? '#198754' : '#856404' ?>;">
                            <?= $is_ok ? '✓' : '?' ?>
                        </div>
                        <div class="doc-info">
                            <div class="doc-title"><?= htmlspecialchars($todos_docs[$d_key] ?? $d_key) ?></div>
                            <span class="doc-status <?= $is_ok ? 'status-ok' : '' ?>" style="<?= !$is_ok ? 'background:#fff3cd; color:#856404;' : '' ?>">
                                <?= $is_ok ? 'Recebido' : 'Aguardando Avaliação' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; color: #888;">
                <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                <h3 style="color: #666;">Ainda não definido</h3>
                <p>O tipo do seu processo ainda está sendo analisado pela nossa equipe. Em breve a lista de documentos aparecerá aqui.</p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
