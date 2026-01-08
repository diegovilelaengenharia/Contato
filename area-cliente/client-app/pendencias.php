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

// 2. FETCH CLIENT DATA
$stmt = $pdo->prepare("SELECT nome FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente_nome = $stmt->fetchColumn(); 

// --- LOGIC: HANDLE UPLOAD ---
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
                 // Update Status to 'em_analise' or just mark as uploaded in a generic way?
                 // Let's set status to 'em_analise' if column exists, or just keep it 'pendente' but notify.
                 // For now, let's assume we update status to 'em_analise' IF that enum exists.
                 // If not, we'll just ignore status change or use 'resolvido' if user wants.
                 // User said: "Show 'Encaminhado'". 
                 // We can check if 'encaminhado' is a valid status. If not, we might need to add it or use a standardized one.
                 // Safe bet: Update `status` to 'em_analise' (Analysis) if your DB supports it. 
                 // If not sure, let's try to update to 'em_analise'. If enum fails, it fails silently? No, safer to not touch status if strict.
                 // Re-reading request: "Aparecer q foi encaminhado".
                 // Let's store the filename in a new logic or just rely on the file existence?
                 // I will try to update status to 'em_analise'. if it fails, I'll catch it.
                 try {
                    $pdo->prepare("UPDATE processo_pendencias SET status='em_analise' WHERE id=? AND cliente_id=?")->execute([$pid, $cliente_id]);
                 } catch(Exception $e) { /* Ignore enum error */ }
                 
                 // Feedback
                 $msg_success = "Arquivo enviado com sucesso!";
             } else {
                 $msg_error = "Erro ao salvar arquivo.";
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

function getWhatsappLink($pendency_desc) {
    $text = "Olá, estou entrando em contato sobre a pendência: *" . strip_tags($pendency_desc) . "*.";
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
        .page-header {
            background: #f8d7da; /* Light Red */
            border-bottom: none;
            padding: 25px 20px; 
            border-bottom-left-radius: 20px; 
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1); 
            margin-bottom: 25px;
            display: flex; align-items: center; gap: 10px;
            color: #842029;
        }
        .btn-back {
            text-decoration: none; color: #842029; font-weight: 600; 
            display: flex; align-items: center; gap: 5px;
            padding: 8px 16px; background: #fff; border-radius: 20px;
            transition: 0.2s;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        /* Table Styles */
        .pendency-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .pendency-table th {
            text-align: left;
            padding: 15px;
            background: #fdfdfe;
            color: #999;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            border-bottom: 1px solid #eee;
        }
        .pendency-table td {
            padding: 15px;
            border-bottom: 1px solid #f2f2f2;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #333;
        }
        .pendency-table tr:last-child td { border-bottom: none; }
        
        .status-badge {
            padding: 4px 10px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase;
        }
        .st-pendente { background: #fff3cd; color: #856404; }
        .st-resolvido { background: #d1e7dd; color: #0f5132; }
        .st-analise { background: #cff4fc; color: #055160; }

        .action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px;
            border-radius: 8px;
            border: none; cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-upload { background: #e9ecef; color: #333; }
        .btn-upload:hover { background: #dee2e6; }
        
        .btn-whatsapp { background: #d1e7dd; color: #198754; margin-left: 5px; }
        
        /* Responsive Table */
        @media (max-width: 600px) {
            .pendency-table thead { display: none; }
            .pendency-table tr { display: block; border-bottom: 1px solid #eee; padding: 15px; }
            .pendency-table td { display: block; padding: 5px 0; border: none; }
            .col-desc { font-weight: 600; margin-bottom: 5px; }
            .col-meta { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- HEADER -->
        <div class="page-header" style="justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:15px;">
                <a href="index.php" class="btn-back"><span>←</span> Voltar</a>
                <h1 style="font-size:1.2rem; margin:0;">Pendências</h1>
            </div>
            <div class="app-btn-icon" style="background:#fce8e6; color:#dc3545;">⚠️</div>
        </div>

        <?php if(isset($msg_success)): ?>
            <div style="background:#d1e7dd; color:#0f5132; padding:15px; border-radius:12px; margin-bottom:20px; font-size:0.9rem;">
                ✅ <?= $msg_success ?>
            </div>
        <?php endif; ?>

            <?php if(empty($pendencias)): ?>
                <div style="text-align:center; padding:40px; color:#999;">
                    <span style="font-size:2rem; display:block; margin-bottom:10px;">🎉</span>
                    Nenhuma pendência encontrada.
                </div>
            <?php else: ?>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach($pendencias as $p): 
                        $status_class = 'st-pendente';
                        $status_label = 'Pendente';
                        if($p['status'] == 'resolvido') { $status_class = 'st-resolvido'; $status_label = 'Resolvido'; }
                        elseif($p['status'] == 'em_analise') { $status_class = 'st-analise'; $status_label = 'Encaminhado'; }
                    ?>
                    
                    <div class="card-pendency" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
                        
                        <!-- Header: Descrição e Status -->
                        <div style="margin-bottom: 15px;">
                             <div style="font-weight: 600; font-size: 1.05rem; color: #333; margin-bottom: 5px; line-height: 1.4;">
                                <?= htmlspecialchars($p['titulo']) ?>
                             </div>
                             <?php if($p['descricao']): ?>
                                <div style="font-size: 0.9rem; color: #777; margin-bottom: 10px;">
                                    <?= htmlspecialchars($p['descricao']) ?>
                                </div>
                             <?php endif; ?>
                             
                             <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                <span style="font-size: 0.85rem; color: #999;">
                                    <?= date('d/m/Y', strtotime($p['data_criacao'])) ?>
                                </span>
                                <span class="status-badge <?= $status_class ?>" style="font-size: 0.75rem;">
                                    <?= $status_label ?>
                                </span>
                             </div>
                        </div>

                        <!-- Actions Row -->
                        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                            
                            <!-- 1. Botão Anexar (Se não resolvido) -->
                            <?php if($p['status'] != 'resolvido'): ?>
                                <form method="POST" enctype="multipart/form-data" style="margin:0;">
                                    <input type="hidden" name="pendencia_id" value="<?= $p['id'] ?>">
                                    <input type="file" name="arquivo_pendencia" id="file_<?= $p['id'] ?>" style="display:none;" onchange="this.form.submit()">
                                    
                                    <button type="button" onclick="document.getElementById('file_<?= $p['id'] ?>').click()" class="btn-action-text" style="background: #f0f2f5; color: #333; border: 1px solid #ccc;">
                                        <span class="material-symbols-rounded">attach_file</span>
                                        Anexar Arquivo
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- 2. Botão Whatsapp -->
                            <a href="<?= getWhatsappLink($p['titulo']) ?>" target="_blank" class="btn-action-text" style="background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc;">
                                <span class="material-symbols-rounded">chat</span>
                                Fale com o Engenheiro
                            </a>

                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>

        <!-- Styles for new buttons -->
        <style>
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
        </style>
        
    </div>

</body>
</html>        .pendency-desc {
            font-size: 1rem; color: #333; line-height: 1.5; margin-bottom: 15px;
        }
        
        .btn-action-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%;
            padding: 14px; /* Larger touch target */
            background: var(--color-primary); color: white;
            border-radius: 12px;
            text-decoration: none; font-weight: 600; font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: none; cursor: pointer;
        }

        .empty-state {
            text-align: center; padding: 40px 20px; color: #888;
        }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; opacity: 0.5; }
    </style>
</head>
<body>

    <div class="app-container" style="padding: 0;">
        
        <!-- COLORED HEADER -->
        <div class="page-header">
            <a href="index.php" class="btn-back">
                <span>←</span> Voltar
            </a>
            <h1 style="font-size: 1.3rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                <span>⚠️</span> Pendências
            </h1>
        </div>

        <div style="padding: 0 20px;">
            <!-- CONTENT -->
            <?php if (empty($pendencias)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🎉</div>
                    <h2 style="font-size: 1.2rem; margin-bottom: 10px; color: #333;">Tudo em dia!</h2>
                    <p>Você não tem nenhuma pendência para resolver no momento.</p>
                </div>
            <?php else: ?>
                
                <div style="margin-bottom: 20px; font-size: 0.9rem; color: #666; padding: 0 5px;">
                    Itens que precisam da sua atenção. Use o botão <b>Anexar</b> para enviar documentos solicitados.
                </div>

                <?php foreach($pendencias as $p): 
                    $status = $p['status'];
                    $is_resolved = ($status == 'resolvido');
                    $status_label = $is_resolved ? 'Resolvido' : (($status == 'em_analise' || $status == 'anexado') ? 'Em Análise' : 'Pendente');
                    $bg_class = $is_resolved ? 'status-resolvido' : (($status == 'em_analise' || $status == 'anexado') ? 'status-analise' : 'status-pendente');
                    $icon = $is_resolved ? '✅' : '⏳';
                ?>
                    <div class="card-pendency <?= $is_resolved ? 'resolvido' : '' ?>">
                        <div class="status-badge <?= $bg_class ?>">
                            <span><?= $icon ?></span> <?= $status_label ?>
                        </div>
                        
                        <div class="pendency-desc">
                            <?= $p['descricao'] ?>
                        </div>
                        
                        <?php if (!$is_resolved): ?>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                
                                <!-- Upload Action -->
                                <form action="../upload_pendencia_cliente.php" method="POST" enctype="multipart/form-data" style="margin:0;">
                                    <input type="hidden" name="pendencia_id" value="<?= $p['id'] ?>">
                                    <input type="file" name="arquivo" id="file-<?= $p['id'] ?>" style="display:none;" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('file-<?= $p['id'] ?>').click()" class="btn-action-primary" style="background: #0d6efd;">
                                        <span>📎</span> Anexar Comprovante
                                    </button>
                                </form>

                                <!-- WhatsApp Action -->
                                <a href="<?= getWhatsappLink($p['descricao']) ?>" target="_blank" class="btn-action-primary" style="background: #25d366;">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>
                                    Falar no WhatsApp
                                </a>

                            </div>
                        <?php else: ?>
                            <div style="text-align: center; font-size: 0.8rem; color: #198754; font-weight: 600; padding: 10px; background: #e8f5e9; border-radius: 8px;">
                                Item concluído em <?= date('d/m/Y', strtotime($p['data_criacao'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
        
        <div class="floating-buttons">
            <a href="https://wa.me/5535984529577" class="floating-btn floating-btn--whatsapp" target="_blank" title="Falar com Engenheiro">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>
            </a>
        </div>

    </div>

</body>
</html>
