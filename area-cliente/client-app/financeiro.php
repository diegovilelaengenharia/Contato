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

// 2. FETCH FINANCIAL DATA
$stmt = $pdo->prepare("SELECT * FROM processo_financeiro WHERE cliente_id = ? ORDER BY data_vencimento ASC");
$stmt->execute([$cliente_id]);
$lancamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. CALCULATE TOTALS
$total_pago = 0;
$total_pendente = 0;
$total_atrasado = 0;

foreach($lancamentos as $l) {
    if($l['status'] == 'pago') $total_pago += $l['valor'];
    elseif($l['status'] == 'pendente') $total_pendente += $l['valor'];
    elseif($l['status'] == 'atrasado') $total_atrasado += $l['valor'];
}

// FORMATTER
function formatMoney($val) {
    return 'R$ ' . number_format($val, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro</title>
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- STYLES -->
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    
    <style>
        body { background: #f4f6f8; }
        
        /* HEADER - YELLOW THEME (Premium) */
        .page-header {
            background: linear-gradient(135deg, #fff3cd 0%, #ffecb5 100%); /* Light Yellow Gradient */
            border-bottom: none;
            padding: 30px 25px; 
            border-bottom-left-radius: 30px; 
            border-bottom-right-radius: 30px;
            box-shadow: 0 10px 30px rgba(255, 193, 7, 0.15); 
            margin-bottom: 25px;
            display: flex; align-items: center; justify-content: space-between;
            color: #664d03; /* Dark Yellow/Brown Text */
            position: relative;
            overflow: hidden;
            border: 1px solid #ffe69c;
        }
        
        .page-header::after {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 150px; height: 150px; background: rgba(255,255,255,0.4);
            border-radius: 50%; pointer-events: none;
        }

        .btn-back {
            text-decoration: none; color: #664d03; font-weight: 600; 
            display: flex; align-items: center; gap: 8px;
            padding: 10px 20px; 
            background: white; 
            border-radius: 25px;
            transition: 0.3s;
            font-size: 0.95rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #ffe69c;
        }
        .btn-back:hover { background: #fffdf5; transform: translateX(-3px); }
        .btn-back:active { transform: scale(0.95); }
        
        .header-title-box {
            display: flex; flex-direction: column; align-items: flex-end; text-align: right;
        }
        .header-title-main { font-size: 1.4rem; font-weight: 700; letter-spacing: -0.5px; color: #664d03; }
        .header-title-sub { font-size: 0.8rem; opacity: 0.8; font-weight: 500; margin-top: 2px; color: #856404; }

        .fin-summary {
            display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
            margin-bottom: 25px;
        }
        
        .fin-card-kpi {
            background: white; padding: 15px; border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            border: 1px solid #eee; text-align: center;
        }
        
        .fin-card-kpi small {
            display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px;
        }
        
        .fin-card-kpi strong {
            display: block; font-size: 1.1rem; color: #333; margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="app-container" style="padding: 0;">
        
        <!-- HEADER -->
        <div class="page-header">
            <!-- Left: Back Button -->
            <a href="index.php" class="btn-back">
                <span class="material-symbols-rounded">arrow_back</span> Voltar
            </a>

            <!-- Right: Title & Icon -->
            <div style="display:flex; align-items:center; gap:15px; z-index:2;">
                 <div class="header-title-box">
                    <span class="header-title-main">Financeiro</span>
                    <span class="header-title-sub">Pagamentos e Previsões</span>
                 </div>
                 
                 <!-- Icon -->
                 <div style="background: white; border:1px solid #dee2e6; color: #ffc107; width: 55px; height: 55px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    💰
                 </div>
            </div>
        </div>
        <div style="padding: 0 20px;">
            <!-- KPI SUMMARY -->
            <div class="fin-summary">
                <div class="fin-card-kpi">
                    <small>Total Pago</small>
                    <strong style="color: #198754;"><?= formatMoney($total_pago) ?></strong>
                </div>
                <div class="fin-card-kpi">
                    <small>A Pagar</small>
                    <strong style="color: #ffc107;"><?= formatMoney($total_pendente + $total_atrasado) ?></strong>
                </div>
            </div>

            <!-- LISTA -->
            <?php if(empty($lancamentos)): ?>
                <div style="text-align:center; padding: 40px; color:#999;">
                    <div style="font-size:3rem; margin-bottom:10px; opacity:0.3;">💸</div>
                    <p>Nenhum lançamento financeiro encontrado.</p>
                </div>
            <?php else: ?>
                
                <div style="margin-bottom: 30px;">
                    <?php foreach($lancamentos as $l): 
                        $status_class = '';
                        $status_label = '';
                        $status_color = '#666'; // Default
                        
                        if($l['status'] == 'pago'){ 
                            $status_class = 'status-pago'; 
                            $status_label = 'Pago'; 
                            $status_color = '#198754';
                        }
                        elseif($l['status'] == 'atrasado'){ 
                            $status_class = 'status-atrasado'; 
                            $status_label = 'Atrasado'; 
                            $status_color = '#dc3545';
                        }
                        elseif($l['status'] == 'isento'){ 
                            $status_class = ''; 
                            $status_label = 'Isento'; 
                            $status_color = '#999';
                        }
                        else { 
                            $status_class = 'status-pendente'; 
                            $status_label = 'Pendente'; 
                            $status_color = '#ffc107';
                        }
                    ?>
                    
                    <div class="fin-premium-row <?= $status_class ?>">
                        <div class="fp-left">
                            <h4><?= htmlspecialchars($l['descricao']) ?></h4>
                            <span>Vencimento: <?= date('d/m/Y', strtotime($l['data_vencimento'])) ?></span>
                        </div>
                        <div class="fp-right">
                            <span class="fp-price"><?= formatMoney($l['valor']) ?></span>
                            <span class="fp-badge" style="color: <?= $status_color ?>; font-weight:800;"><?= $status_label ?></span>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                </div>
                
            <?php endif; ?>

            <!-- WHATSAPP CTA -->
            <div style="text-align: center; margin-top: 20px;">
                 <a href="https://wa.me/5535984529577?text=Ola,%20gostaria%20de%20falar%20sobre%20o%20financeiro." style="display:inline-block; font-size: 0.85rem; color: #146c43; text-decoration: none; font-weight: 600; padding: 10px 20px; background: #d1e7dd; border-radius: 20px;">
                    Dúvidas sobre pagamentos? Fale conosco.
                 </a>
            </div>
        </div>

        <!-- FLOATING ACTION BUTTON -->
        <!-- FLOATING SOCIAL BUTTONS -->
        <div class="floating-social-container">
            <!-- Instagram -->
            <a href="https://instagram.com/vilela.engenharia" target="_blank" class="social-btn instagram">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="white"/>
                </svg>
                <div class="social-tooltip">Siga no Instagram</div>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/5535984529577?text=Ol%C3%A1%2C%20gostaria%20de%20falar%20sobre%20meu%20processo." target="_blank" class="social-btn whatsapp">
                <span class="material-symbols-rounded" style="font-size:35px;">chat</span>
                <div class="social-tooltip">Fale conosco</div>
            </a>
        </div>

    </div>

</body>
</html>
