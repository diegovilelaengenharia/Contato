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

// BUSCAR DADOS DO CLIENTE
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// BUSCAR DETALHES DO PROCESSO
$stmt_det = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
$stmt_det->execute([$cliente_id]);
$detalhes = $stmt_det->fetch(PDO::FETCH_ASSOC);

// DEFINIÇÃO DAS FASES
$fases_padrao = [
    'Levantamento de Dados',
    'Desenvolvimento de Projetos',
    'Aprovação na Prefeitura',
    'Pagamento de Taxas',
    'Emissão de Alvará',
    'Entrega de Projetos'
];

$etapa_atual = $detalhes['etapa_atual'] ?? 'Levantamento de Dados';
$etapa_atual = trim($etapa_atual);

// STATUS CALC
$fase_index = array_search($etapa_atual, $fases_padrao);
if($fase_index === false) $fase_index = 0; 
$porcentagem = round((($fase_index + 1) / count($fases_padrao)) * 100);

// CONTAR PENDÊNCIAS
$stmt_pend_count = $pdo->prepare("SELECT COUNT(*) FROM processo_pendencias WHERE cliente_id = ? AND status != 'resolvido'");
$stmt_pend_count->execute([$cliente_id]);
$pendencias_count = $stmt_pend_count->fetchColumn();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente | Vilela Engenharia</title>
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- STYLES -->
    <link rel="stylesheet" href="css/style.css?v=2.7.3">
    
    <style>
        .app-button {
            position: relative;
            z-index: 1;
            text-decoration: none;
            display: flex;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- 1. COMPANY HEADER (Branding) -->
        <header class="company-header">
            <!-- Bell Notification (Top Right) -->
            <div style="position:absolute; top:10px; right:10px; cursor:pointer;" title="Notificações">
                <div style="position:relative;">
                    <span style="font-size:1.6rem;">🔔</span>
                    <?php if($pendencias_count > 0): ?>
                        <span style="position:absolute; top:-2px; right:-2px; background:red; color:white; font-size:0.6rem; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; border:2px solid #f4f6f8;"><?= $pendencias_count ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <img src="../../assets/logo.png" alt="Vilela Engenharia" style="height:60px; margin-bottom:5px;">
            <div class="company-name">Diego T. N. Vilela</div>
            <div class="company-crea">Engenheiro Civil · CREA 235.474/D</div>
        </header>

        <!-- 2. CLIENT CARD (Profile) -->
        <section class="client-card">
            
            <div class="client-header-row">
                <!-- Avatar -->
                <div style="flex-shrink:0;">
                    <?php 
                        $avatarPath = $cliente['foto_perfil'] ?? '';
                        if($avatarPath && file_exists($avatarPath)): 
                    ?>
                        <img src="<?= htmlspecialchars($avatarPath) ?>?v=<?= time() ?>" alt="Perfil" style="width:50px; height:50px; border-radius:50%; object-fit:cover; border:2px solid var(--color-primary);">
                    <?php else: ?>
                        <div style="width:50px; height:50px; border-radius:50%; background:#e9ecef; color:#aaa; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">👤</div>
                    <?php endif; ?>
                </div>
                
                <!-- Name & Welcome -->
                <div class="client-name-block" style="flex:1;">
                    <span>Olá,</span>
                    <h1><?= htmlspecialchars(explode(' ', $cliente['nome'])[0]) ?></h1>
                </div>

                <!-- Logout -->
                <a href="logout.php" style="background:#fffcfc; border:1px solid #f8d7da; color:#dc3545; padding:6px 12px; border-radius:8px; font-weight:600; font-size:0.75rem; text-decoration:none;">
                    Sair
                </a>
            </div>

            <!-- Details Grid -->
            <div class="client-details-row">
                <?php if(!empty($detalhes['endereco_imovel'])): ?>
                    <div style="display:flex; align-items:center; gap:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <span style="font-size:1rem;">📍</span> <span style="font-weight:600; color:#333;">Obra:</span> <?= htmlspecialchars($detalhes['endereco_imovel']) ?>
                    </div>
                <?php endif; ?>

                <div style="display:flex; flex-wrap:wrap; gap:15px; margin-top:5px;">
                    <?php if(!empty($detalhes['cpf_cnpj'])): ?>
                        <div style="display:flex; align-items:center; gap:4px;">
                            <span>🆔</span> <?= htmlspecialchars($detalhes['cpf_cnpj']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($detalhes['contato_tel'])): ?>
                        <div style="display:flex; align-items:center; gap:4px;">
                            <span>📞</span> <?= htmlspecialchars($detalhes['contato_tel']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </section>

        <!-- 3. ACTION GRID (Buttons) -->
        <div class="app-action-grid">
            
            <!-- TIMELINE -->
            <a href="timeline.php" class="app-button" style="cursor: pointer;">
                <div class="app-btn-icon" style="background:#e3f2fd; color:#0d47a1;">⏳</div>
                <div class="app-btn-content">
                    <span class="app-btn-title">Linha do Tempo</span>
                    <span class="app-btn-desc"><?= htmlspecialchars($etapa_atual) ?></span>
                </div>
                <div style="font-weight:800; color:#0d47a1; font-size:1.1rem;"><?= $porcentagem ?>%</div>
            </a>

            <!-- PENDÊNCIAS -->
            <?php 
                $has_pendency = $pendencias_count > 0;
                $p_style = $has_pendency ? "border-left: 4px solid #dc3545;" : ""; 
                $p_icon_bg = "#fff3cd"; 
                $p_icon_col = "#856404";
            ?>
            <a href="pendencias.php" class="app-button" style="<?= $p_style ?>">
                <div class="app-btn-icon" style="background:<?= $p_icon_bg ?>; color:<?= $p_icon_col ?>;">⚠️</div>
                <div class="app-btn-content">
                    <span class="app-btn-title" style="<?= $has_pendency ? 'color:#dc3545; font-weight:700;' : '' ?>">Pendências</span>
                    <?php if($has_pendency): ?>
                        <span class="app-btn-desc" style="color:#dc3545; font-weight:600;"><?= $pendencias_count ?> Nova(s) atualização(ões)</span>
                    <?php else: ?>
                        <span class="app-btn-desc">Tudo em dia!</span>
                    <?php endif; ?>
                </div>
                <?php if($has_pendency): ?>
                    <div style="background:#dc3545; color:white; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:bold;"><?= $pendencias_count ?></div>
                <?php else: ?>
                    <div style="color:#198754; font-size:1.2rem;">✅</div>
                <?php endif; ?>
            </a>

            <!-- FINANCEIRO -->
            <a href="financeiro.php" class="app-button">
                <div class="app-btn-icon" style="background:#d1e7dd; color:#146c43;">💰</div>
                <div class="app-btn-content">
                    <span class="app-btn-title">Financeiro</span>
                    <span class="app-btn-desc">Faturas e Recibos</span>
                </div>
                <div style="color:#146c43;">➔</div>
            </a>

            <!-- DOCUMENTOS -->
            <a href="documentos.php" class="app-button">
                <div class="app-btn-icon" style="background:#fff3cd; color:#856404;">📂</div>
                <div class="app-btn-content">
                    <span class="app-btn-title">Documentos</span>
                    <span class="app-btn-desc">Acessar Projetos</span>
                </div>
                <div style="color:#856404;">➔</div>
            </a> 

        </div>

        <!-- DEVELOPER CREDIT -->
        <div style="text-align:center; margin-top:50px; opacity:0.6; font-size:0.8rem;">
            Desenvolvido por <strong>Diego T. N. Vilela</strong> (v2.7.3)
        </div>

        <div class="floating-buttons">
            <a href="https://wa.me/5535984529577?text=Ola%20Engenheiro,%20tenho%20uma%20divida%20sobre%20o%20processo" class="floating-btn floating-btn--whatsapp" target="_blank" title="Falar com Engenheiro">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>
            </a>
            <a href="https://www.instagram.com/diegovilela.eng/" class="floating-btn floating-btn--instagram" target="_blank" title="Instagram">
                <svg viewBox="0 0 24 24"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm5 3.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 5A1.5 1.5 0 1 0 10.5 12 1.5 1.5 0 0 0 12 13.5zm4.25-6.75a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"></path></svg>
            </a>
        </div>
    </div>

</body>
</html>
