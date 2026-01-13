<?php
require 'includes/init.php';

// --- Atualização de Schema ---
require 'includes/schema.php';

// --- Fases Padrão ---
$fases_padrao = [
    "Abertura de Processo (Guichê)", 
    "Fiscalização (Parecer Fiscal)", 
    "Triagem (Documentos Necessários)",
    "Comunicado de Pendências (Triagem)", 
    "Análise Técnica (Engenharia)", 
    "Comunicado (Pendências e Taxas)",
    "Confecção de Documentos", 
    "Avaliação (ITBI/Averbação)", 
    "Processo Finalizado (Documentos Prontos)"
];

// --- Taxas e Multas Padrão ---
$taxas_padrao = require 'config/taxas.php';

// --- Processamento ---
// Helper Function for Finance Table
function renderFinTable($stmt, $title, $color, $cid) {
    if(!$stmt) return;
    $rows = $stmt->fetchAll();
    echo "<div class='kpi-card-new' style='border-top: 4px solid $color; margin-bottom: 20px;'>
            <h4 style='color:$color; font-size: 1rem;'>$title</h4>";
    
    if(count($rows) == 0) {
        echo "<p style='font-style:italic; color:#999;'>Nenhum lançamento encontrado.</p>";
    } else {
        echo "<div class='table-responsive'>
              <table style='width:100%; border-collapse:collapse;'>
                <thead><tr style='border-bottom: 1px solid #eee; text-align:left;'>
                    <th style='padding:10px;'>Descrição</th>
                    <th style='padding:10px;'>Valor</th>
                    <th style='padding:10px;'>Vencimento</th>
                    <th style='padding:10px;'>Status</th>
                    <th style='padding:10px;'></th>
                </tr></thead><tbody>";
        foreach($rows as $r) {
            $badge_class = 'status-badge'; 
            switch($r['status']){
                case 'pago': $badge_class.=' success'; break;
                case 'pendente': $badge_class.=' warning'; break;
                case 'atrasado': $badge_class.=' danger'; break;
                case 'isento': $badge_class.=' info'; break;
            }
            $valor = number_format($r['valor'], 2, ',', '.');
            $data = date('d/m/Y', strtotime($r['data_vencimento']));
            
            echo "<tr style='border-bottom: 1px solid #f8f9fa;'>
                    <td style='padding:10px; font-weight:500;'>{$r['descricao']}</td>
                    <td style='padding:10px; font-weight:bold;'>R$ {$valor}</td>
                    <td style='padding:10px; color:#666;'>{$data}</td>
                    <td style='padding:10px;'><span class='$badge_class' onclick=\"openStatusFinModal({$r['id']}, '{$r['status']}')\" style='cursor:pointer;'>".ucfirst($r['status'])."</span></td>
                    <td style='padding:10px; text-align:right;'>
                        <a href='?cliente_id={$cid}&tab=financeiro&del_fin={$r['id']}' onclick='confirmAction(event, \"Excluir lançamento?\")' style='color:#dc3545;'>🗑️</a>
                    </td>
                  </tr>";
        }
        echo "</tbody></table></div>";
    }
    echo "</div>";
}

// --- Processamento (POST/GET) ---
require 'includes/processamento.php';
require 'includes/exportacao.php';

// --- Consultas Iniciais ---
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome ASC")->fetchAll();
$cliente_ativo = null;
$detalhes = [];

// KPIs Globais
try {
    $kpi_total_clientes = count($clientes);
    $stmt_pre = $pdo->query("SELECT COUNT(*) FROM pre_cadastros WHERE status='pendente'");
    $kpi_pre_pendentes = $stmt_pre ? $stmt_pre->fetchColumn() : 0;
    
    // Total Recebido (Pago)
    $stmt_rec = $pdo->query("SELECT SUM(valor) FROM processo_financeiro WHERE status = 'pago'");
    $total_recebido = $stmt_rec ? $stmt_rec->fetchColumn() : 0;

    // Total Pendente
    $stmt_pen = $pdo->query("SELECT SUM(valor) FROM processo_financeiro WHERE status = 'pendente'");
    $total_pendente = $stmt_pen ? $stmt_pen->fetchColumn() : 0;

} catch (Exception $e) {
    $kpi_total_clientes = 0; $kpi_pre_pendentes = 0;
}

if (isset($_GET['cliente_id'])) {
    $id = $_GET['cliente_id'];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?"); $stmt->execute([$id]);
    $cliente_ativo = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?"); $stmt->execute([$id]);
    $detalhes = $stmt->fetch();
    if(!$detalhes) $detalhes = [];
}
$active_tab = $_GET['tab'] ?? 'andamento';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Vilela Engenharia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="admin_style.css?v=<?= time() ?>">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
</head>
<body style="background-color: #f8f9fa;">

    <!-- 1. SIDEBAR PREMIUM -->
    <nav class="sidebar-premium">
        <div class="logo-area">
            <img src="../assets/logo.png" alt="Vilela Engenharia">
        </div>

        <div class="sidebar-content">
            <a href="gestao_admin_99.php" class="sidebar-link <?= !$cliente_ativo ? 'active' : '' ?>">
                <span class="material-symbols-rounded">dashboard</span> Visão Geral
            </a>
            
            <a href="gerenciar_cliente.php" class="sidebar-link">
                <span class="material-symbols-rounded">person_add</span> Novo Cliente
            </a>

            <!-- CLIENT CONTEXT -->
            <?php if ($cliente_ativo): ?>
                <div class="sidebar-section-title">CLIENTE SELECIONADO</div>
                <div style="padding: 0 15px; margin-bottom: 10px;">
                    <h3 style="margin:0; font-size: 0.95rem; color:#333;"><?= htmlspecialchars($cliente_ativo['nome']) ?></h3>
                </div>

                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=andamento" class="sidebar-link <?= ($active_tab=='andamento')?'active':'' ?>">
                    <span class="material-symbols-rounded">history</span> Histórico
                </a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=documentos" class="sidebar-link <?= ($active_tab=='documentos')?'active':'' ?>">
                    <span class="material-symbols-rounded">folder</span> Documentos
                </a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=pendencias" class="sidebar-link <?= ($active_tab=='pendencias')?'active':'' ?>">
                    <span class="material-symbols-rounded">warning</span> Pendências
                </a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=financeiro" class="sidebar-link <?= ($active_tab=='financeiro')?'active':'' ?>">
                    <span class="material-symbols-rounded">attach_money</span> Financeiro
                </a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=arquivos" class="sidebar-link <?= ($active_tab=='arquivos')?'active':'' ?>">
                    <span class="material-symbols-rounded">cloud_upload</span> Arquivos
                </a>
            <?php endif; ?>

            <div class="sidebar-section-title">GERAL</div>
            <a href="avisos_gerais.php" class="sidebar-link">
                <span class="material-symbols-rounded">campaign</span> Avisos Gerais
            </a>
            <a href="admin_config.php" class="sidebar-link">
                <span class="material-symbols-rounded">settings</span> Configurações
            </a>
            <a href="logout.php" class="sidebar-link" style="color: #dc3545;">
                <span class="material-symbols-rounded">logout</span> Sair
            </a>
        </div>
    </nav>

    <!-- 2. MAIN CONTENT -->
    <main class="main-content-premium">
        
        <?php if (!$cliente_ativo): ?>
            <!-- DASHBOARD HOME VIEW -->
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 1.8rem; color: #333; margin: 0;">Painel Administrativo</h1>
                <span style="color: #666; font-size: 0.9rem;"><?= date('d/m/Y') ?></span>
            </div>

            <!-- KPIS GRID (7 Cards) -->
            <div class="kpi-grid-premium">
                <!-- 1. Recebido (Verde) -->
                <div class="kpi-card-new kpi-recebido">
                    <h4>Total Recebido</h4>
                    <span class="value">R$ <?= number_format($total_recebido, 2, ',', '.') ?></span>
                </div>
                <!-- 2. Pendente (Amarelo) -->
                <div class="kpi-card-new kpi-pendente">
                    <h4>A Receber</h4>
                    <span class="value">R$ <?= number_format($total_pendente, 2, ',', '.') ?></span>
                </div>
                <!-- 3. Ticket Médio (Roxo) -->
                <div class="kpi-card-new kpi-medio">
                    <h4>Ticket Médio</h4>
                    <span class="value">R$ --</span>
                </div>
                <!-- 4. Margem (Verde Agua) -->
                <div class="kpi-card-new kpi-margem">
                    <h4>Margem Líquida</h4>
                    <span class="value">-- %</span>
                </div>
                <!-- 5. Total Clientes (Cinza) -->
                <div class="kpi-card-new kpi-clientes">
                    <h4>Total Clientes</h4>
                    <span class="value"><?= $kpi_total_clientes ?></span>
                </div>
                <!-- 6. Ativos (Azul) -->
                <div class="kpi-card-new kpi-ativos">
                    <h4>Processos Ativos</h4>
                    <span class="value">--</span>
                </div>
                <!-- 7. Prazo Médio (Laranja) -->
                <div class="kpi-card-new kpi-prazo">
                    <h4>Prazo Médio</h4>
                    <span class="value">-- dias</span>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h2 style="font-size: 1.4rem; color: #333;">Clientes Recentes</h2>
                <div style="position: relative;">
                    <input type="text" id="filtroNome" onkeyup="filtrarClientes()" placeholder="Buscar cliente..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #ddd; width: 250px;">
                    <span class="material-symbols-rounded" style="position: absolute; left: 10px; top: 10px; color: #999;">search</span>
                </div>
            </div>

            <!-- CLIENT LIST LOOP -->
            <div id="lista-clientes">
            <?php foreach($clientes as $c): 
                // Get Details for Display
                $stmtD = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?"); 
                $stmtD->execute([$c['id']]);
                $d = $stmtD->fetch();
                
                // Get Phase Progress
                $etapa = $d['etapa_atual'] ?? '';
                $found_idx = array_search($etapa, $fases_padrao);
                $percent = ($found_idx !== false) ? round((($found_idx + 1) / count($fases_padrao)) * 100) : 0;

                // Avatar
                $avatar_path = glob("uploads/avatars/avatar_{$c['id']}.*");
                $avatar_url = !empty($avatar_path) ? $avatar_path[0] . '?v=' . time() : '../assets/avatar_placeholder.png'; // Use a placeholder if needed
            ?>
                <div class="client-card-premium cliente-item">
                    <div class="cc-left">
                        <?php if(!empty($avatar_path)): ?>
                            <img src="<?= $avatar_url ?>" class="cc-avatar">
                        <?php else: ?>
                            <div class="cc-avatar" style="background:#e9ecef; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#555; font-size:1.2rem;">
                                <?= substr($c['nome'],0,1) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="cc-info">
                            <h3><?= htmlspecialchars($c['nome']) ?></h3>
                            <div class="cc-tags">
                                <span class="cc-tag">
                                    <span class="material-symbols-rounded" style="font-size:14px;">folder</span>
                                    <?= substr($d['tipo_servico'] ?? 'N/A', 0, 20) ?>
                                </span>
                                <span class="cc-tag">
                                    <span class="material-symbols-rounded" style="font-size:14px;">call</span>
                                    <?= $d['contato_tel'] ?? '--' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cc-right">
                        <div class="cc-progress">
                            <div class="cc-stage-text">
                                <span class="cc-stage-label">Fase Atual</span>
                                <span class="cc-stage-val"><?= $etapa ?: 'Não iniciado' ?></span>
                            </div>
                            <div class="progress-circle" data-p="<?= $percent ?>" style="--p:<?= $percent ?>deg;"></div>
                        </div>

                        <div class="cc-actions">
                            <a href="?cliente_id=<?= $c['id'] ?>&tab=andamento" class="btn-action-primary" style="text-decoration:none; display:flex; align-items:center; justify-content:center; border-radius:8px; height:38px;">
                                Acessar
                            </a>
                            <a href="gerenciar_cliente.php?id=<?= $c['id'] ?>" class="btn-icon-action" title="Editar">
                                <span class="material-symbols-rounded">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- CLIENT CONTEXT VIEW -->
            
            <!-- HEADER DO CLIENTE (Simplificado pois já tem na sidebar) -->
            <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between;">
                <div>
                     <h2 style="margin: 0; font-size: 1.5rem; color: #146c43;">Painel do Cliente</h2>
                     <p style="margin: 5px 0 0 0; color: #666;">Gerenciando: <strong><?= htmlspecialchars($cliente_ativo['nome']) ?></strong></p>
                </div>
                <div>
                     <!-- Ações Rápidas -->
                     <button onclick="document.getElementById('modalAndamento').showModal()" class="btn-action-primary" style="border:none; cursor:pointer;">+ Novo Andamento</button>
                </div>
            </div>

            <!-- CONTENT BASED ON TAB -->
            <div class="admin-tab-content" style="display:block;">
                <?php 
                if ($active_tab == 'andamento') {
                    require 'includes/modals/timeline.php'; // Modal Definition
                    
                    // Render Timeline Logic
                    // ... (Copiar Lógica de Renderização da Timeline do arquivo original mas limpo) ...
                    echo "<h3 class='admin-title'>Histórico do Processo</h3>";
                    echo "<div class='admin-table-container'><table class='admin-table'><thead><tr><th>Data</th><th>Evento</th><th style='text-align:center;'>Ação</th></tr></thead><tbody>";
                    
                    $hist = $pdo->prepare("SELECT * FROM processo_movimentos WHERE cliente_id=? ORDER BY data_movimento DESC");
                    $hist->execute([$cliente_ativo['id']]);
                    foreach($hist->fetchAll() as $h) {
                         echo "<tr>
                                <td>".date('d/m/Y H:i', strtotime($h['data_movimento']))."</td>
                                <td>
                                    <strong>".htmlspecialchars($h['titulo_fase'])."</strong><br>
                                    <span style='color:#666;'>".explode("||COMENTARIO_USER||", $h['descricao'])[0]."</span>
                                </td>
                                <td style='text-align:center;'>
                                    <a href='?cliente_id={$cliente_ativo['id']}&tab=andamento&del_hist={$h['id']}' style='color:#dc3545;'>🗑️</a>
                                </td>
                              </tr>";
                    }
                    echo "</tbody></table></div>";

                } elseif ($active_tab == 'financeiro') {
                    require 'includes/modals/financeiro.php';
                    echo "<h3 class='admin-title' style='margin-bottom:20px;'>Fluxo Financeiro</h3>";
                    echo "<div style='text-align:right; margin-bottom:15px;'><button onclick=\"document.getElementById('modalFinanceiro').showModal()\" class='btn-action-primary' style='border:none; padding:8px 15px; cursor:pointer;'>+ Novo Lançamento</button></div>";
                    
                    // Honorários
                    $stmt = $pdo->prepare("SELECT * FROM processo_financeiro WHERE cliente_id=? AND categoria='honorarios' ORDER BY data_vencimento ASC");
                    $stmt->execute([$cliente_ativo['id']]);
                    renderFinTable($stmt, "Honorários e Serviços", "#198754", $cliente_ativo['id']);

                } elseif ($active_tab == 'pendencias') {
                    require 'includes/modals/pendencias.php';
                    echo "<h3 class='admin-title'>Pendências e Documentos</h3>";
                    
                     $stmt_pend = $pdo->prepare("SELECT * FROM processo_pendencias WHERE cliente_id=? ORDER BY status ASC, id DESC");
                     $stmt_pend->execute([$cliente_ativo['id']]);
                     $pends = $stmt_pend->fetchAll();

                     echo "<div style='text-align:right; margin-bottom:15px;'><button onclick=\"document.getElementById('modalNovaPendencia').showModal()\" class='btn-action-primary' style='border:none; padding:8px 15px; cursor:pointer;'>+ Nova Pendência</button></div>";

                     // Simple Table for now
                     echo "<table class='admin-table'><thead><tr><th>Descrição</th><th>Status</th><th>Ações</th></tr></thead><tbody>";
                     foreach($pends as $p) {
                         $status_color = ($p['status']=='resolvido') ? 'success' : 'warning';
                         echo "<tr>
                                <td>{$p['descricao']}</td>
                                <td><span class='status-badge $status_color'>".strtoupper($p['status'])."</span></td>
                                <td>...</td>
                               </tr>";
                     }
                     echo "</tbody></table>";
                     
                } elseif ($active_tab == 'arquivos') {
                    echo "<h3 class='admin-title'>Arquivos do Drive</h3>";
                    // Only show form and iframe
                    ?>
                    <form method="POST" style="background:#fff; padding:20px; border-radius:12px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                        <input type="hidden" name="cliente_id" value="<?= $cliente_ativo['id'] ?>">
                        <div class="admin-form-group">
                            <label>Link da Pasta (Google Drive)</label>
                            <input type="text" name="link_drive_pasta" value="<?= $detalhes['link_drive_pasta']??'' ?>" class="admin-form-input" style="width:100%; border:1px solid #ddd; padding:10px; border-radius:6px;">
                        </div>
                        <button type="submit" name="btn_salvar_arquivos" class="btn-action-primary" style="border:none; height:40px; cursor:pointer; margin-top:10px;">Salvar Link</button>
                    </form>
                    <?php
                    // Embed logic...
                    if(!empty($detalhes['link_drive_pasta'])): 
                        $drive_url = $detalhes['link_drive_pasta'];
                        $embed_url = $drive_url; 
                        if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $drive_url, $matches)) {
                            $embed_url = "https://drive.google.com/embeddedfolderview?id=" . $matches[1] . "#list";
                        } elseif (preg_match('/id=([a-zA-Z0-9_-]+)/', $drive_url, $matches)) {
                             $embed_url = "https://drive.google.com/embeddedfolderview?id=" . $matches[1] . "#list";
                        }
                    ?>
                        <div style="height: 600px; width:100%; border:1px solid #ddd; border-radius:12px; overflow:hidden;">
                            <iframe src="<?= htmlspecialchars($embed_url) ?>" width="100%" height="100%" frameborder="0" style="border:0;"></iframe>
                        </div>
                    <?php endif;
                }
                ?>
            </div>

        <?php endif; ?>

    </main>

    <script>
    function filtrarClientes() {
        let input = document.getElementById('filtroNome').value.toUpperCase();
        let cards = document.getElementsByClassName('cliente-item');
        for (let i = 0; i < cards.length; i++) {
            let name = cards[i].getElementsByTagName('h3')[0].innerText;
            if (name.toUpperCase().indexOf(input) > -1) {
                cards[i].style.display = "flex";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
    </script>
</body>
</html>
