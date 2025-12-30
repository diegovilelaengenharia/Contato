<?php
// Fallbacks
$primeiro_nome = $primeiro_nome ?? 'Cliente';
$etapa_atual = $etapa_atual ?? 'Início';
$fases_total = count($fases_padrao);
$fase_atual_idx = $fase_index; // Vem do dashboard.php

// Pega iniciais
$iniciais = strtoupper(substr($primeiro_nome, 0, 1));
?>

<!-- HEADER PREMIUM "MASTER CARD" -->
<div class="header-premium-card fade-in-up">
    <button onclick="toggleTheme()" class="hp-theme-toggle" title="Alterar Tema">
        <span class="material-symbols-rounded">dark_mode</span>
    </button>
    
    <div class="hp-top">
        <div class="hp-avatar">
            <?php if(!empty($data['foto_perfil']) && file_exists(__DIR__ . '/../../' . $data['foto_perfil'])): ?>
                <img src="<?= htmlspecialchars($data['foto_perfil']) ?>" alt="Foto" id="userPhoto">
            <?php else: ?>
                <span><?= $iniciais ?></span>
            <?php endif; ?>
        </div>
        <div class="hp-client-info">
            <h1><?= htmlspecialchars($primeiro_nome) ?></h1>
            <p><span class="material-symbols-rounded" style="font-size:16px;">id_card</span> Cliente #<?= $cliente_id ?></p>
        </div>
    </div>
    
    <div class="hp-details">
        <div class="hp-item">
            <label>Local da Obra</label>
            <span><?= mb_strimwidth($endereco, 0, 30, '...') ?></span>
        </div>
        <div class="hp-item">
            <label>Metragem</label>
            <span><?= $data['area_construida'] ?? '-- ' ?>m² (Const.)</span>
        </div>
    </div>
</div>

<!-- ALERTAS URGENTES (Mantido, mas estilizado melhor via css global) -->
<?php 
$pendencias_abertas = array_filter($pendencias, fn($p) => $p['status'] != 'resolvido');
if(count($pendencias_abertas) > 0): 
?>
<div class="urgent-alert fade-in-up" onclick="window.location.href='?view=pendencias'">
    <div class="ua-icon">🔔</div>
    <div class="ua-content">
        <strong>Atenção Necessária</strong>
        <p>Você possui pendências em aberto. Toque para resolver.</p>
    </div>
    <span class="material-symbols-rounded">chevron_right</span>
</div>
<?php endif; ?>


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

<!-- RESUMO DO PROCESSO -->
<div class="process-summary fade-in-up">
    <span class="summary-highlight">Fase Atual: <?= $etapa_atual ?></span>
    <p class="summary-desc">Estamos trabalhando nesta etapa. Assim que concluída, avançaremos automaticamente para a próxima fase.</p>
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
