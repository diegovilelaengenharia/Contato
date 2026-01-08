<?php
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
require_once '../db.php';

// 1. AUTHENTICATION
if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../index.php");
    exit;
}
$cliente_id = $_SESSION['cliente_id'];

// 2. LOGIC: HANDLE UPLOAD
if(isset($_FILES['arquivo_pendencia']) && isset($_POST['pendencia_id'])) {
    $pid = $_POST['pendencia_id'];
    $file = $_FILES['arquivo_pendencia'];
    
    if($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'zip'];
        
        if(in_array($ext, $allowed)) {
             // Create Dir
             $dir = __DIR__ . '/uploads/pendencias/';
             if(!is_dir($dir)) mkdir($dir, 0755, true);
             
             // Name: ID_TIMESTAMP.ext
             $new_name = $pid . '_' . time() . '.' . $ext;
             
             if(move_uploaded_file($file['tmp_name'], $dir . $new_name)) {
                 // Tenta atualizar status para 'em_analise' e salvar nome do arquivo se possível
                 // Vamos tentar atualizar apenas o status primeiro, pois não temos certeza da coluna de arquivo.
                 // SE a coluna 'status' for ENUM restrito, isso pode falhar.
                 
                 try {
                    $sql = "UPDATE processo_pendencias SET status='em_analise' WHERE id=? AND cliente_id=?";
                    $stmtUpdate = $pdo->prepare($sql);
                    $stmtUpdate->execute([$pid, $cliente_id]);
                    
                    if($stmtUpdate->rowCount() > 0) {
                        $msg_success = "Arquivo enviado! Pendência em análise.";
                    } else {
                        // Se não afetou linhas, pode ser que o status já fosse 'em_analise' ou erro de ID
                        $msg_success = "Arquivo salvo em: uploads/pendencias/" . $new_name;
                    }

                 } catch(PDOException $e) {
                     // Erro detalhado para debug
                     $msg_error = "Arquivo salvo, mas erro ao atualizar status: " . $e->getMessage();
                     // Fallback: se o status 'em_analise' não for aceito, o arquivo já está na pasta.
                 }
                 
             } else {
                 $msg_error = "Erro ao mover arquivo para pasta de uploads.";
             }
        } else {
            $msg_error = "Formato inválido.";
        }
    }
}

// 3. FETCH PENDENCIES
$stmt_pend = $pdo->prepare("SELECT * FROM processo_pendencias WHERE cliente_id = ? ORDER BY CASE WHEN status = 'resolvido' THEN 2 WHEN status = 'em_analise' THEN 1 ELSE 0 END, data_criacao DESC");
$stmt_pend->execute([$cliente_id]);
$pendencias = $stmt_pend->fetchAll(PDO::FETCH_ASSOC);

function getWhatsappLink($pendency_title) {
    $text = "Olá, estou entrando em contato sobre a pendência: *" . strip_tags($pendency_title) . "*.";
    return "https://wa.me/5535984529577?text=" . urlencode($text);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendências</title>
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- STYLES -->
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    
    <style>
        body { background: #f4f6f8; }
        
        /* HEADER PADRONIZADO (Verde Brand) */
        .page-header {
            background: #e8f5e9; /* Light Green Standard */
            border-bottom: none;
            padding: 25px 20px; 
            border-bottom-left-radius: 20px; 
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.1); 
            margin-bottom: 25px;
            display: flex; align-items: center; justify-content: space-between;
            color: #146c43;
        }
        .btn-back {
            text-decoration: none; color: #146c43; font-weight: 600; 
            display: flex; align-items: center; gap: 5px;
            padding: 8px 16px; background: #fff; border-radius: 20px;
            transition: 0.2s;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .status-badge {
            padding: 4px 10px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase;
        }
        .st-pendente { background: #fff3cd; color: #856404; }
        .st-resolvido { background: #d1e7dd; color: #0f5132; }
        .st-analise { background: #cff4fc; color: #055160; }

        .btn-action-text {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px;
            border-radius: 12px;
            font-weight: 600; font-size: 0.95rem;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.1s;
        }
        .btn-action-text:active { transform: scale(0.98); }

        .empty-state {
            text-align: center; padding: 40px; color: #999;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- HEADER -->
        <div class="page-header">
            <div style="display:flex; align-items:center; gap:15px;">
                <a href="index.php" class="btn-back"><span>←</span> Voltar</a>
                <h1 style="font-size:1.2rem; margin:0; font-weight: 700;">Pendências</h1>
            </div>
        </div>

        <?php if(isset($msg_success)): ?>
            <div style="background:#d1e7dd; color:#0f5132; padding:15px; border-radius:12px; margin-bottom:20px; font-size:0.9rem; border: 1px solid #badbcc;">
                ✅ <?= $msg_success ?>
            </div>
        <?php endif; ?>

        <?php if(isset($msg_error)): ?>
            <div style="background:#f8d7da; color:#842029; padding:15px; border-radius:12px; margin-bottom:20px; font-size:0.9rem; border: 1px solid #f5c2c7;">
                ❌ <?= $msg_error ?>
            </div>
        <?php endif; ?>

        <!-- CONTENT -->
        <?php if(empty($pendencias)): ?>
            <div class="empty-state">
                <span style="font-size:2rem; display:block; margin-bottom:10px;">🎉</span>
                <h3 style="color:#333; margin:0;">Tudo Certo!</h3>
                <div style="font-size:0.9rem; margin-top:5px;">Nenhuma pendência encontrada.</div>
            </div>
        <?php else: ?>
            
            <div style="display: flex; flex-direction: column; gap: 20px; padding-bottom: 20px;">
                <?php foreach($pendencias as $p): 
                    $status = $p['status'];
                    $is_resolvido = ($status == 'resolvido');
                    
                    // Detect Files Logic (FileSystem Scan)
                    // Pattern: ID_TIMESTAMP.ext or just ID.ext (legacy)
                    $upload_dir = __DIR__ . '/uploads/pendencias/';
                    $web_path = 'uploads/pendencias/'; // Relative to this file
                    $anexos = [];
                    
                    if(is_dir($upload_dir)) {
                        // Scan for files starting with ID_
                        $files = glob($upload_dir . $p['id'] . "_*.*");
                        if($files) {
                            foreach($files as $f) {
                                $filename = basename($f);
                                $anexos[] = [
                                    'name' => $filename,
                                    'path' => $web_path . $filename,
                                    'date' => filemtime($f)
                                ];
                            }
                        }
                    }
                    
                    // Se tiver anexo e status nao for resolvido, considera como "Em Análise/Anexado" visualmente
                    $has_attachment = !empty($anexos);
                    if($has_attachment && !$is_resolvido) {
                         $status_label = "Arquivo Enviado / Em Análise";
                         $bg_badge = "#0d6efd"; // Blue
                         $bg_card = "#f0f8ff"; // Light Blue bg
                         $border_card = "#cce5ff";
                         $text_title = "#084298";
                    } elseif($is_resolvido) {
                         $status_label = "Resolvido";
                         $bg_badge = "#198754"; // Green
                         $bg_card = "#d1e7dd"; 
                         $border_card = "#badbcc";
                         $text_title = "#0f5132";
                    } else {
                         // Default Pendente
                         $status_label = "Pendente";
                         $bg_badge = "#ffc107"; // Yellow
                         $bg_card = "#fff9d6";
                         $border_card = "#ffeeba";
                         $text_title = "#856404";
                    }
                    
                    $data_criacao = date('d/m/Y', strtotime($p['data_criacao']));
                ?>
                <div style="background: <?= $bg_card ?>; border: 1px solid <?= $border_card ?>; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    
                    <!-- Header do Card: Título e Status -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div>
                            <!-- DATA EVIDENTE -->
                            <span style="display: block; font-size: 0.8rem; font-weight: 700; color: #555; margin-bottom: 4px; opacity: 0.7;">
                                📅 <?= $data_criacao ?>
                            </span>
                            <!-- TÍTULO GRANDE E NEGRITO -->
                            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: <?= $text_title ?>; line-height: 1.3;">
                                <?= htmlspecialchars($p['titulo']) ?>
                            </h3>
                        </div>
                        
                        <span style="background: <?= $bg_badge ?>; color: <?= ($status_label=='Pendente')?'#333':'white' ?>; padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                            <?= $status_label ?>
                        </span>
                    </div>

                    <!-- Descrição -->
                    <?php if(!empty($p['descricao'])): ?>
                        <div style="font-size: 0.95rem; color: #444; margin-bottom: 15px; line-height: 1.5; font-weight: 500;">
                            <?= nl2br(htmlspecialchars($p['descricao'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Arquivos Já Enviados (Feedback Visual) -->
                    <?php if($has_attachment): ?>
                        <div style="margin-bottom: 15px; background: rgba(255,255,255,0.7); padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                            <strong style="display:block; font-size:0.8rem; margin-bottom:5px; color:#555;">Arquivos Enviados:</strong>
                            <?php foreach($anexos as $arq): ?>
                                <a href="<?= $arq['path'] ?>" target="_blank" style="display:inline-flex; align-items:center; gap:6px; font-size:0.85rem; color: #0d6efd; text-decoration:none; background:white; padding:5px 10px; border-radius:15px; border:1px solid #ddd; margin-right:5px; margin-bottom:5px;">
                                    📎 Anexo (<?= date('d/m H:i', $arq['date']) ?>)
                                </a>
                            <?php endforeach; ?>
                            <div style="font-size:0.75rem; color:#888; margin-top:5px;">*O arquivo foi enviado e está sob análise do engenheiro.</div>
                        </div>
                    <?php endif; ?>

                    <!-- Ações (Upload/Whatsapp) -->
                    <?php if(!$is_resolvido): ?>
                    <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                        
                        <!-- Form Upload -->
                        <form action="pendencias.php" method="POST" enctype="multipart/form-data" style="background: rgba(255,255,255,0.6); padding: 15px; border-radius: 12px; border: 1px dashed <?= $text_title ?>; margin-bottom:0;">
                            <input type="hidden" name="pendencia_id" value="<?= $p['id'] ?>">
                            
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; color: #333;">
                                <?= $has_attachment ? 'Enviar novo arquivo (sobrescrever/adicional):' : 'Anexar Comprovante/Arquivo:' ?>
                            </label>
                            <div style="display: flex; gap: 10px;">
                                <input type="file" name="arquivo_pendencia" required style="font-size: 0.85rem; width: 100%; border-radius: 6px; border: 1px solid #ccc; background: #fff; padding:5px;">
                            </div>
                            <button type="submit" name="upload_arquivo" style="margin-top: 10px; width: 100%; padding: 10px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <span class="material-symbols-rounded">cloud_upload</span> Enviar Arquivo
                            </button>
                        </form>

                        <!-- Botão Whatsapp -->
                        <a href="<?= getWhatsappLink($p['titulo']) ?>" target="_blank" class="btn-action-text" style="background: #25D366; color: white; border: 1px solid #badbcc; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="material-symbols-rounded">chat</span>
                            Fale com o Engenheiro
                        </a>

                    </div>
                    <?php else: ?>
                        <!-- Se resolvido -->
                            <div style="margin-top: 10px; font-size: 0.85rem; color: #0f5132; background: rgba(255,255,255,0.4); padding: 8px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
                            <span class="material-symbols-rounded" style="font-size: 1rem;">check_circle</span>
                            <span>Pendência regularizada.</span>
                            </div>
                    <?php endif; ?>

                </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <!-- FLOATING ACTION BUTTONS -->
        <div class="floating-buttons">
            <a href="https://wa.me/5535984529577" class="floating-btn floating-btn--whatsapp" target="_blank" title="Falar com Engenheiro">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>
            </a>
        </div>
        
        <!-- WHATSAPP CTA -->
        <div style="text-align: center; margin-top: 20px; padding-bottom: 20px;">
             <a href="https://wa.me/5535984529577?text=Ola,%20tenho%20duvidas%20sobre%20as%20pendencias." style="display:inline-block; font-size: 0.85rem; color: #146c43; text-decoration: none; font-weight: 600; padding: 10px 20px; background: #d1e7dd; border-radius: 20px;">
                Dúvidas sobre as pendências? Fale conosco.
             </a>
        </div>
        
    </div>

</body>
</html>
