<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
require_once '../db.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../index.php");
    exit;
}
$cliente_id = $_SESSION['cliente_id'];

// 3. FETCH PENDENCIES
$stmt_pend = $pdo->prepare("SELECT * FROM processo_pendencias WHERE cliente_id = ? ORDER BY data_criacao DESC");
$stmt_pend->execute([$cliente_id]);
$all_pendencias = $stmt_pend->fetchAll(PDO::FETCH_ASSOC);

$resolvidas = [];
$abertas = [];

foreach($all_pendencias as $p) {
    if($p['status'] == 'resolvido') {
        $resolvidas[] = $p;
    } else {
        $abertas[] = $p;
    }
}

function get_pendency_files($p_id) {
    return []; // DUMMY
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
    <link rel="stylesheet" href="css/style.css?v=3.0">
    
    <style>
        /* FORCE SOCIAL UPDATE v2 */
        .floating-buttons { position: fixed; bottom: 25px; right: 25px; display: flex; flex-direction: column; gap: 16px; z-index: 99999 !important; }
        .floating-btn { width: 56px; height: 56px; border-radius: 50%; display: grid; place-items: center; background: var(--btn-bg); color: #ffffff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 0 8px 24px rgba(0, 0, 0, 0.1); transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease; text-decoration: none; position: relative; border: none !important; }
        .floating-btn svg { width: 28px; height: 28px; fill: currentColor; }
        .floating-btn--whatsapp { --btn-bg: #25d366; }
        .floating-btn--whatsapp:hover { background: #20bd5a; box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4); }
        .floating-btn--instagram { --btn-bg: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
        .floating-btn--instagram:hover { box-shadow: 0 6px 16px rgba(220, 39, 67, 0.4); }
        .floating-btn:hover { transform: scale(1.1) rotate(-4deg); }
        .floating-btn:active { transform: scale(0.95); }

        body { background: #f4f6f8; }
        
        /* HEADER - RED THEME (Premium) */
        .page-header {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); /* Light Red Gradient */
            border-bottom: none;
            padding: 30px 25px; 
            border-bottom-left-radius: 30px; 
            border-bottom-right-radius: 30px;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.15); 
            margin-bottom: 30px;
            display: flex; align-items: center; justify-content: space-between;
            color: #842029; /* Dark Red Text */
            position: relative;
            overflow: hidden;
            border: 1px solid #f5c2c7;
        }
        
        .page-header::after {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 150px; height: 150px; background: rgba(255,255,255,0.4);
            border-radius: 50%; pointer-events: none;
        }

        .btn-back {
            text-decoration: none; color: #842029; font-weight: 600; 
            display: flex; align-items: center; gap: 8px;
            padding: 10px 20px; 
            background: white; 
            border-radius: 25px;
            transition: 0.3s;
            font-size: 0.95rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #f5c2c7;
        }
        .btn-back:hover { background: #fff5f5; transform: translateX(-3px); }
        
        .header-title-box {
            display: flex; flex-direction: column; align-items: flex-end; text-align: right;
        }
        .header-title-main { font-size: 1.4rem; font-weight: 700; letter-spacing: -0.5px; color: #58151c; }
        .header-title-sub { font-size: 0.8rem; opacity: 0.8; font-weight: 500; margin-top: 2px; color: #842029; }

        .status-badge {
            padding: 4px 10px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase;
        }

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
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #555;
            margin: 30px 0 15px 0;
            display: flex; align-items: center; gap: 8px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- HEADER -->
        <div class="page-header">
            <!-- Left: Back Button -->
            <a href="index.php" class="btn-back">
                <span class="material-symbols-rounded">arrow_back</span> Voltar
            </a>

            <!-- Right: Title & Icon -->
            <div style="display:flex; align-items:center; gap:15px; z-index:2;">
                 <div class="header-title-box">
                    <span class="header-title-main">Pendências</span>
                    <span class="header-title-sub">Ações Necessárias</span>
                 </div>
                 
                 <!-- Icon -->
                 <div style="background: white; border:1px solid #f5c2c7; color: #dc3545; width: 55px; height: 55px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.1);">
                    &#9888;&#65039;
                 </div>
            </div>
        </div>

        <!-- CONTENT: COMMENTED OUT LOOPS -->
        <div style="padding: 20px; text-align: center; color: #666;">
            <h3>Debug: Loops Disabled</h3>
            <p>Resolved Count: <?= count($resolvidas) ?></p>
            <p>Open Count: <?= count($abertas) ?></p>
        </div>


        <!-- WHATSAPP CTA -->
        <div style="text-align: center; margin-top: 20px; padding-bottom: 20px;">
                <a href="https://wa.me/5535984529577?text=Ola,%20tenho%20duvidas%20sobre%20as%20pendencias." style="display:inline-block; font-size: 0.85rem; color: #146c43; text-decoration: none; font-weight: 600; padding: 10px 20px; background: #d1e7dd; border-radius: 20px;">
                Dúvidas sobre as pendências? Fale conosco.
            </a>
        </div>
            
    </div>

    <!-- MODAL DE RESOLUÇÃO (HTML Structure Only) -->
    <div id="resolveModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-modal" onclick="closeResolveModal()">&times;</span>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <span class="material-symbols-rounded" style="font-size: 3rem; color: #0d6efd; background: #eff6ff; padding: 15px; border-radius: 50%;">cloud_upload</span>
                <h3 style="margin: 15px 0 5px 0; color: #333;">Resolver Pendência</h3>
                <p id="modalPendencyTitle" style="color: #666; font-size: 0.9rem; margin: 0;">Titulo da Pendência</p>
            </div>

            <form action="pendencias.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="pendencia_id" id="modalPendencyId" value="">
                
                <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; color: #333;">
                    Selecione o arquivo/comprovante:
                </label>
                
                <div style="border: 2px dashed #ccc; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px; background: #fafafa; position: relative;" onclick="document.getElementById('fileInput').click()">
                    <span class="material-symbols-rounded" style="color: #999; font-size: 2rem; display: block; margin-bottom: 5px;">folder_open</span>
                    <span style="color: #555; font-size: 0.9rem;">Clique para escolher o arquivo</span>
                    <input type="file" name="arquivo_pendencia" id="fileInput" required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                </div>
                <!-- File Name Display -->
                <div id="fileNameDisplay" style="font-size: 0.85rem; color: #0d6efd; margin-bottom: 15px; font-weight: 500; display: none;"></div>

                <button type="submit" name="upload_arquivo" style="width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);">
                    Enviar Arquivo
                </button>
            </form>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(5px);
            padding: 20px;
        }
        .modal-content {
            background: white; width: 100%; max-width: 400px;
            padding: 30px; border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
            animation: slideUp 0.3s ease-out;
        }
        .close-modal {
            position: absolute; top: 15px; right: 20px;
            font-size: 2rem; color: #aaa; cursor: pointer;
            line-height: 1;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <script>
        function openResolveModal(id, title) {
            document.getElementById('modalPendencyId').value = id;
            document.getElementById('modalPendencyTitle').innerText = title;
            document.getElementById('resolveModal').style.display = 'flex';
        }

        function closeResolveModal() {
            document.getElementById('resolveModal').style.display = 'none';
        }

        // Close on outside click
        document.getElementById('resolveModal').addEventListener('click', function(e) {
            if (e.target === this) closeResolveModal();
        });

        // Show filename
        document.getElementById('fileInput').addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : '';
            var display = document.getElementById('fileNameDisplay');
            if(fileName) {
                display.style.display = 'block';
                display.innerText = '📎 ' + fileName;
            } else {
                display.style.display = 'none';
            }
        });
    </script>
    
    <!-- FLOATING SOCIAL BUTTONS -->
    <div class="floating-buttons" style="z-index: 99999;">
        <a href="https://wa.me/5535984529577?text=Ola%20Diego%20Vilela" class="floating-btn floating-btn--whatsapp" target="_blank" rel="noopener" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24" role="presentation"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>
        </a>
        <a href="https://www.instagram.com/diegovilela.eng/" class="floating-btn floating-btn--instagram" target="_blank" rel="noopener" aria-label="Instagram">
            <svg viewBox="0 0 24 24" role="presentation"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm5 3.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 5A1.5 1.5 0 1 0 10.5 12 1.5 1.5 0 0 0 12 13.5zm4.25-6.75a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"></path></svg>
        </a>
    </div>

</body>
</html>
