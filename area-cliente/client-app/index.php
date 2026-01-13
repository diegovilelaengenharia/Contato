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

// NOTIFICATIONS LOGIC
$notificacoes = [];
// 1. Pendências
$stmt_pend = $pdo->prepare("SELECT count(*) as qtd FROM processo_pendencias WHERE cliente_id = ? AND status != 'resolvido'");
$stmt_pend->execute([$cliente_id]);
$pend_qtd = $stmt_pend->fetchColumn();

// 2. Financeiro
$stmt_fin = $pdo->prepare("SELECT count(*) as qtd FROM processo_financeiro WHERE cliente_id = ? AND (status = 'pendente' OR status = 'atrasado')");
$stmt_fin->execute([$cliente_id]);
$fin_qtd = $stmt_fin->fetchColumn();

// PROGRESSO
$fases_padrao = [
    'Abertura de Processo (Guichê)',
    'Fiscalização (Parecer Fiscal)',
    'Triagem (Documentos Necessários)',
    'Comunicado de Pendências (Triagem)',
    'Análise Técnica (Engenharia)',
    'Comunicado (Pendências e Taxas)',
    'Confecção de Documentos',
    'Avaliação (ITBI/Averbação)',
    'Processo Finalizado (Documentos Prontos)'
];
$etapa_atual = $detalhes['etapa_atual'] ?? 'Levantamento de Dados';
$fase_index = array_search(trim($etapa_atual), $fases_padrao);
if($fase_index === false) $fase_index = 0; 
$porcentagem = round((($fase_index + 1) / count($fases_padrao)) * 100);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente | Vilela Engenharia</title>
    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <style>
        :root {
            --primary: #146c43;
            --primary-dark: #0f5132;
            --secondary: #ffc107;
            --bg-light: #f8f9fa;
            --text-dark: #212529;
            --text-muted: #6c757d;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            color: var(--text-dark);
        }
        .app-container {
            max-width: 600px;
            margin: 0 auto;
            min-height: 100vh;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .portal-header {
            background-color: var(--primary);
            color: white;
            padding: 25px 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 15px rgba(20, 108, 67, 0.2);
        }
        .ph-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo-small {
            height: 35px;
            filter: brightness(0) invert(1); /* Logo branca */
        }
        .page-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        
        .client-info-box {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .avatar-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.2rem;
        }
        .welcome-text h2 {
            margin: 0;
            font-size: 1.3rem;
        }
        .welcome-text .sub {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 300;
        }
        .logout-btn {
            color: white;
            text-decoration: none;
            background: rgba(0,0,0,0.2);
            width: 35px; height: 35px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        /* CARDS */
        .cards-container {
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .portal-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .portal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
            border-color: var(--primary);
        }
        /* Left border accent */
        .portal-card::before {
            content: '';
            position: absolute;
            left: 0; top: 15%; height: 70%;
            width: 4px;
            border-radius: 0 4px 4px 0;
        }
        
        .pc-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .pc-icon {
            width: 45px; height: 45px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .pc-info h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #333;
        }
        .pc-info p {
            margin: 3px 0 0 0;
            font-size: 0.8rem;
            color: #888;
        }
        .pc-arrow {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.2rem;
        }

        /* CARD VARIANTS */
        /* Documents Initial (Blue) */
        .card-initial::before { background: #0d6efd; }
        .card-initial .pc-icon { background: #e3f2fd; color: #0d6efd; }

        /* Timeline (Green) */
        .card-timeline::before { background: #198754; }
        .card-timeline .pc-icon { background: #d1e7dd; color: #198754; }
        
        /* Pendencies (Orange) */
        .card-pend::before { background: #fd7e14; }
        .card-pend .pc-icon { background: #ffe5d0; color: #fd7e14; }
        
        /* Finance (Yellow) */
        .card-fin::before { background: #ffc107; }
        .card-fin .pc-icon { background: #fff3cd; color: #ffab00; }
        
        /* Documents Final (Cyan) */
        .card-doc::before { background: #0dcaf0; }
        .card-doc .pc-icon { background: #cff4fc; color: #055160; }
        
        /* FOOTER CARD */
        .footer-card {
            background: #fff9db;
            border: 1px solid #ffe066;
            border-radius: 16px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            margin-top: 20px;
        }
        .footer-card:hover {
            filter: brightness(0.98);
        }
        .fc-icon {
            font-size: 1.5rem;
        }
        .fc-info h4 { margin:0; font-size: 0.95rem; color: #856404; }
        .fc-info span { font-size: 0.75rem; color: #856404; opacity: 0.8; }
        
        .download-btn {
            background: #ffc107;
            color: #333;
            width: 35px; height: 35px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-left: auto;
        }

        /* BADGES */
        .badge-count {
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- HEADER -->
        <header class="portal-header">
            <div class="ph-top">
                <img src="../../assets/logo.png" alt="Vilela" class="logo-small">
                <span class="page-title">Portal de Acompanhamento</span>
            </div>
            
            <div class="client-info-box">
                <?php 
                    $avatarPath = $cliente['foto_perfil'] ?? '';
                    if($avatarPath && !str_starts_with($avatarPath, '../') && !str_starts_with($avatarPath, 'http')) $avatarPath = '../' . $avatarPath;
                ?>
                <div class="avatar-circle">
                    <?php if($avatarPath && file_exists($avatarPath) && !is_dir($avatarPath)): ?>
                        <img src="<?= htmlspecialchars($avatarPath) ?>?v=<?= time() ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($cliente['nome'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                
                <div class="welcome-text" style="flex:1;">
                    <span class="sub">Bem-vindo(a),</span>
                    <h2><?= htmlspecialchars(explode(' ', $cliente['nome'])[0]) ?></h2>
                </div>

                <a href="logout.php" class="logout-btn">
                    <span class="material-symbols-rounded">logout</span>
                </a>
            </div>
        </header>

        <!-- CARDS -->
        <div class="cards-container">
            
            <!-- 1. DOCUMENTOS INICIAIS (RESTORED) -->
            <a href="documentos_iniciais.php" class="portal-card card-initial">
                <div class="pc-content">
                    <div class="pc-icon">🗂️</div>
                    <div class="pc-info">
                        <h3>Documentos Iniciais</h3>
                        <p>Checklist de Entrada</p>
                    </div>
                </div>
                <div class="pc-arrow">➔</div>
            </a>

            <!-- 2. LINHA DO TEMPO -->
            <a href="timeline.php" class="portal-card card-timeline">
                <div class="pc-content">
                    <div class="pc-icon">🧭</div>
                    <div class="pc-info">
                        <h3>Linha do Tempo</h3>
                        <div style="background:#eee; height:4px; width:80px; margin-top:5px; border-radius:2px; overflow:hidden;">
                            <div style="height:100%; background:#198754; width:<?= $porcentagem ?>%;"></div>
                        </div>
                        <p style="font-size:0.7rem; margin-top:3px;"><?= htmlspecialchars(substr($etapa_atual,0,25)) ?>... (<?= $porcentagem ?>%)</p>
                    </div>
                </div>
                <div class="pc-arrow">➔</div>
            </a>

            <!-- 3. PENDÊNCIAS -->
            <a href="pendencias.php" class="portal-card card-pend">
                <div class="pc-content">
                    <div class="pc-icon">
                        <?= $pend_qtd > 0 ? '⚠️' : '✅' ?>
                    </div>
                    <div class="pc-info">
                        <h3>Pendências <?php if($pend_qtd > 0) echo "<span class='badge-count'>$pend_qtd</span>"; ?></h3>
                        <p><?= $pend_qtd > 0 ? 'Aguardando resolução' : 'Nenhuma pendência recente' ?></p>
                    </div>
                </div>
                <div class="pc-arrow">➔</div>
            </a>

            <!-- 4. FINANCEIRO -->
            <a href="financeiro.php" class="portal-card card-fin">
                <div class="pc-content">
                    <div class="pc-icon">💰</div>
                    <div class="pc-info">
                        <h3>Financeiro <?php if($fin_qtd > 0) echo "<span class='badge-count'>$fin_qtd</span>"; ?></h3>
                        <p><?= $fin_qtd > 0 ? 'Pagamento pendente' : 'Nenhum pagamento pendente' ?></p>
                    </div>
                </div>
                <div class="pc-arrow">➔</div>
            </a>
            
            <!-- 5. DOCUMENTOS FINAIS -->
            <a href="documentos.php" class="portal-card card-doc">
                <div class="pc-content">
                    <div class="pc-icon">📂</div>
                    <div class="pc-info">
                        <h3>Documentos Finais</h3>
                        <p>Acesso aos documentos digitais</p>
                    </div>
                </div>
                <div class="pc-arrow">➔</div>
            </a>

            <!-- FOOTER DOWNLOAD -->
             <a href="../../area-cliente/relatorio_cliente.php?id=<?= $cliente['id'] ?>" target="_blank" class="footer-card">
                <div class="fc-icon">🖨️</div>
                <div class="fc-info">
                    <h4>Visão Geral do Processo</h4>
                    <span>Clique para baixar o PDF</span>
                </div>
                <div class="download-btn">⬇️</div>
             </a>

        </div>

    </div>

</body>
</html>
