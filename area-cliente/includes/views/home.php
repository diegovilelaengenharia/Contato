<?php
// Dados Iniciais e Fallbacks
$primeiro_nome = $primeiro_nome ?? 'Cliente';
$etapa_atual = $detalhes['etapa_atual'] ?? 'Início';
$fase_index_atual = array_search($etapa_atual, $fases_padrao);
if($fase_index_atual === false) $fase_index_atual = 0;
$progresso_pct = round((($fase_index_atual + 1) / count($fases_padrao)) * 100);

// Cores e Icones por Status (Dinâmico)
$status_color = 'var(--color-primary)';
$status_icon = 'engineering';
if($progresso_pct >= 100) { $status_color = '#198754'; $status_icon = 'check_circle'; }
?>

<div class="fade-in-up" style="padding-bottom: 80px;">
    
    <!-- HEADER SIMPLES -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <span style="font-size:0.9rem; color:#666;">Bem-vindo(a),</span>
            <h1 style="margin:0; font-size:1.5rem; color:#333;"><?= htmlspecialchars($primeiro_nome) ?></h1>
        </div>
        <div style="text-align:right;">
             <span style="display:block; font-size:0.75rem; color:#999;">Processo ID</span>
             <strong style="color:var(--color-primary);">#<?= str_pad($cliente_id, 3, '0', STR_PAD_LEFT) ?></strong>
        </div>
    </div>

    <!-- MAIN STATUS CARD (HERO) -->
    <div style="background: linear-gradient(135deg, <?= $status_color ?> 0%, #2980b9 100%); padding:25px; border-radius:16px; color:white; margin-bottom:25px; box-shadow:0 10px 25px rgba(0,0,0,0.15); position:relative; overflow:hidden;">
        <!-- Decorator Circle -->
        <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
        
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
            <div style="background:rgba(255,255,255,0.2); padding:10px; border-radius:50%; display:flex;">
                <span class="material-symbols-rounded" style="font-size:1.8rem;"><?= $status_icon ?></span>
            </div>
            <div>
                <span style="display:block; font-size:0.85rem; opacity:0.9;">Fase Atual</span>
                <strong style="font-size:1.2rem;"><?= $etapa_atual ?></strong>
            </div>
        </div>

        <div style="margin-top:10px;">
            <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:5px;">
                <span>Progresso Geral</span>
                <strong><?= $progresso_pct ?>%</strong>
            </div>
            <div style="background:rgba(0,0,0,0.2); height:6px; border-radius:3px; overflow:hidden;">
                <div style="width:<?= $progresso_pct ?>%; height:100%; background:white; border-radius:3px;"></div>
            </div>
        </div>
    </div>

    <!-- DASHBOARD GRID (QUICK ACCESS) -->
    <h3 style="margin-bottom:15px; font-size:1.1rem; color:#444;">Acesso Rápido</h3>
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:25px;">
        
        <!-- Timeline -->
        <div onclick="window.location.href='?view=timeline'" style="background:white; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); cursor:pointer; text-align:center;">
            <span class="material-symbols-rounded" style="font-size:2rem; color:var(--color-primary); margin-bottom:5px;">history_edu</span>
            <strong style="display:block; color:#333; font-size:0.95rem;">Linha do Tempo</strong>
            <span style="font-size:0.75rem; color:#777;">Ver Histórico</span>
        </div>

        <!-- Documentos -->
        <div onclick="window.location.href='?view=documents'" style="background:white; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); cursor:pointer; text-align:center;">
             <span class="material-symbols-rounded" style="font-size:2rem; color:#e67e22; margin-bottom:5px;">folder_open</span>
             <strong style="display:block; color:#333; font-size:0.95rem;">Documentos</strong>
             <span style="font-size:0.75rem; color:#777;">Acessar Arquivos</span>
        </div>

        <!-- Conhecimento -->
        <div onclick="window.location.href='?view=conhecimento'" style="background:white; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); cursor:pointer; text-align:center;">
             <span class="material-symbols-rounded" style="font-size:2rem; color:#2c3e50; margin-bottom:5px;">school</span>
             <strong style="display:block; color:#333; font-size:0.95rem;">Conhecimento</strong>
             <span style="font-size:0.75rem; color:#777;">Glossário & Leis</span>
        </div>

        <!-- Contato/Suporte -->
        <div onclick="window.open('https://wa.me/5537998399321', '_blank')" style="background:white; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); cursor:pointer; text-align:center;">
             <span class="material-symbols-rounded" style="font-size:2rem; color:#25D366; margin-bottom:5px;">support_agent</span>
             <strong style="display:block; color:#333; font-size:0.95rem;">WhatsApp</strong>
             <span style="font-size:0.75rem; color:#777;">Falar com Eng.</span>
        </div>
    </div>

    <!-- FICHA TÉCNICA RESUMIDA -->
    <div style="background:white; border-radius:16px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:1.1rem; color:#333;">Ficha do Imóvel</h3>
            <span class="material-symbols-rounded" style="color:#999; font-size:1.2rem;">info</span>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
            <div style="background:#f8f9fa; padding:10px; border-radius:8px;">
                <span style="display:block; font-size:0.7rem; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Área Total</span>
                <strong style="color:#2c3e50;"><?= htmlspecialchars($data['area_total_final'] ?? '--') ?> m²</strong>
            </div>
            <div style="background:#f8f9fa; padding:10px; border-radius:8px;">
                 <span style="display:block; font-size:0.7rem; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Área Const.</span>
                 <strong style="color:#2c3e50;"><?= htmlspecialchars($data['area_existente'] ?? '--') ?> m²</strong>
            </div>
            <div style="background:#f8f9fa; padding:10px; border-radius:8px;">
                 <span style="display:block; font-size:0.7rem; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Taxa Ocup.</span>
                 <strong style="color:#2c3e50;"><?= htmlspecialchars($data['taxa_ocupacao'] ?? '--') ?>%</strong>
            </div>
            <div style="background:#f8f9fa; padding:10px; border-radius:8px;">
                 <span style="display:block; font-size:0.7rem; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Zoneamento</span>
                 <strong style="color:#2c3e50;">Z-resid</strong>
            </div>
        </div>
        
        <div style="margin-top:20px; border-top:1px dashed #eee; padding-top:15px; text-align:center;">
             <p style="margin:0; font-size:0.85rem; color:#666;">
                <?= htmlspecialchars($data['processo_objeto'] ?? 'Regularização de Imóvel') ?>
             </p>
             <small style="color:#aaa; font-size:0.7rem; display:block; margin-top:5px;"><?= htmlspecialchars($endereco) ?></small>
        </div>
    </div>

</div>
$etapa_atual = $etapa_atual ?? 'Início';
$fases_total = count($fases_padrao);
$fase_atual_idx = $fase_index; // Vem do dashboard.php

// Pega iniciais
$iniciais = strtoupper(substr($primeiro_nome, 0, 1));

// Data Formatada
$data_inicio = isset($data['data_cadastro']) ? date('d/m/Y', strtotime($data['data_cadastro'])) : '--/--/----';
?>

<!-- HOME VIEW -->
<div class="fade-in-up">
    <!-- RICH PROPERTY CARD (Substitui Olá Cliente) -->
    <div class="property-card fade-in-up">
        <!-- Background Image Area -->
        <div class="pc-image" style="background-image: url('<?= !empty($data['foto_capa_obra']) ? htmlspecialchars($data['foto_capa_obra']) : '../assets/obra-placeholder.jpg' ?>');">
            <div class="pc-overlay"></div>
            <div class="pc-content-top">
                <span class="pc-status-badge">
                    <span class="material-symbols-rounded">engineering</span>
                    <?= $etapa_atual ?>
                </span>
                <span class="pc-id">Processo: <?= htmlspecialchars($data['processo_numero'] ?? '---') ?></span>
            </div>
        </div>

        <!-- Info Content -->
        <div class="pc-info">
            <h1 class="pc-title"><?= htmlspecialchars($data['processo_objeto'] ?? 'Regularização de Edificação') ?></h1>
            <p class="pc-address">
                <span class="material-symbols-rounded">location_on</span>
                <?= htmlspecialchars($endereco) ?>
                <?php if(!empty($data['geo_coords'])): ?>
                    <small style="opacity:0.7; font-size:0.8rem;">(<?= htmlspecialchars($data['geo_coords']) ?>)</small>
                <?php endif; ?>
            </p>

            <!-- Technical Grid (Oliveira/MG Spec) -->
            <div class="pc-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                 <div class="pc-grid-item">
                    <label>Área Existente</label>
                    <strong><?= htmlspecialchars($data['area_existente'] ?? '--') ?> m²</strong>
                </div>
                <div class="pc-grid-item">
                    <label>Área Acréscimo</label>
                    <strong><?= htmlspecialchars($data['area_acrescimo'] ?? '--') ?> m²</strong>
                </div>
                 <div class="pc-grid-item">
                    <label>Área Permeável</label>
                    <strong><?= htmlspecialchars($data['area_permeavel'] ?? '--') ?> m²</strong>
                </div>

                <div class="pc-grid-item">
                    <label>Taxa Ocupação</label>
                    <strong><?= htmlspecialchars($data['taxa_ocupacao'] ?? '--') ?>%</strong>
                </div>
                <div class="pc-grid-item">
                    <label>Coef. Aprov. (CA)</label>
                    <strong><?= htmlspecialchars($data['fator_aproveitamento'] ?? '--') ?></strong>
                </div>
                 <div class="pc-grid-item highlight">
                    <label>Valor Venal Proposto</label>
                    <strong>R$ <?= htmlspecialchars($data['valor_venal'] ?? '--') ?></strong>
                </div>
            </div>
        </div>
    </div>


    <!-- ASSISTANT TIP -->
    <div class="assistant-tip fade-in-up">
        <div class="at-icon">ℹ️</div>
        <div class="at-content">
            <strong>Informativo Técnico</strong>
            <p>Este painel apresenta o status do licenciamento urbanístico em tempo real. Acompanhe abaixo o trâmite processual e a emissão das peças técnicas.</p>
        </div>
    </div>

    <!-- Quick Stats Grid (Modified to show Deliverables) -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:25px;">
        <!-- Status Card -->
        <div class="stat-card" onclick="window.location.href='?view=timeline'" style="cursor:pointer; background:var(--color-card-bg);">
            <div class="stat-icon" style="background:var(--color-primary-light); color:var(--color-primary);">🚀</div>
            <div class="stat-info">
                <span class="stat-label">Fase Atual</span>
                <span class="stat-value" style="font-size:1rem; color:var(--color-primary);"><?= $etapa_atual ?: 'Início' ?></span>
            </div>
        </div>

        <!-- Deliverables Card -->
        <?php 
            // Count official docs
            $stmtDocs = $pdo->prepare("SELECT COUNT(*) FROM processo_movimentos WHERE cliente_id = ? AND tipo_movimento = 'documento'");
            $stmtDocs->execute([$cliente_id]);
            $countDocs = $stmtDocs->fetchColumn();
        ?>
        <div class="stat-card" onclick="window.location.href='?view=timeline'" style="cursor:pointer; background:var(--color-card-bg);">
            <div class="stat-icon" style="background:#e8f5e9; color:#198754;">📜</div>
            <div class="stat-info">
                <span class="stat-label">Documentos</span>
                <span class="stat-value" style="color:#198754;"><?= $countDocs ?> emitidos</span>
            </div>
        </div>
    </div>


<!-- STEP PROCESS (MODERN) -->
<h3 style="margin-bottom:15px; padding-left:5px;">Status do Projeto</h3>

<div class="stepper-scroll-container fade-in-up" id="stepperContainer">
    <div class="stepper-track">
        <?php foreach($fases_padrao as $idx => $nome_fase): 
            $status_class = ''; // default future
            $icon_content = $idx + 1;
            
            if($idx < $fase_atual_idx) {
                $status_class = 'completed';
                $icon_content = '✓';
            } elseif($idx == $fase_atual_idx) {
                $status_class = 'active';
            }
        ?>
        <div class="step-item <?= $status_class ?>" id="step-<?= $idx ?>">
            <div class="step-line"></div>
            <div class="step-circle"><?= $icon_content ?></div>
            <div class="step-label"><?= $nome_fase ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- DETAILED PROCESS SUMMARY -->
<div class="process-summary fade-in-up" style="text-align: left;">
    <span class="summary-highlight" style="text-align:center; margin-bottom:15px;">Fase Atual: <?= $etapa_atual ?></span>
    
    <div class="data-grid-summary" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:20px;">
        <div class="dgs-item" style="background:var(--bg-app); padding:10px; border-radius:8px;">
            <label style="font-size:0.7rem; color:var(--text-muted); display:block;">Início do Processo</label>
            <strong style="font-size:0.95rem;"><?= $data_inicio ?></strong>
        </div>
        <div class="dgs-item" style="background:var(--bg-app); padding:10px; border-radius:8px;">
            <label style="font-size:0.7rem; color:var(--text-muted); display:block;">Documentos</label>
            <strong style="font-size:0.95rem;"><?= $total_docs ?? 0 ?> Anexados</strong>
        </div>
    </div>

    <!-- CENTRAL DE CONHECIMENTO LINK -->
    <div onclick="window.location.href='?view=conhecimento'" style="background:white; border:1px solid var(--border-color); padding:15px; border-radius:12px; display:flex; align-items:center; gap:15px; cursor:pointer; margin-bottom:20px; box-shadow:var(--shadow-card);">
        <div style="background:var(--color-primary-light); color:var(--color-primary); padding:10px; border-radius:50%;">
            <span class="material-symbols-rounded">menu_book</span>
        </div>
        <div style="flex:1;">
            <strong style="display:block; color:var(--text-main); font-size:1rem;">Central de Conhecimento</strong>
            <span style="font-size:0.85rem; color:var(--text-muted);">Glossário Técnico e Legislação Local</span>
        </div>
        <div style="color:var(--text-muted);">
            <span class="material-symbols-rounded">chevron_right</span>
        </div>
    </div>

    <p class="summary-desc" style="text-align:justify;">
        Nesta etapa, nossa equipe técnica está focada em <strong><?= strtolower($etapa_atual) ?></strong>. 
        Mantenha-se atento às notificações para qualquer necessidade de documento adicional. 
        O progresso é atualizado automaticamente conforme as aprovações ocorrem.
    </p>
</div>

<script>
// Auto-scroll to active step
document.addEventListener('DOMContentLoaded', () => {
    const activeStep = document.querySelector('.step-item.active');
    if(activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
});
</script>
