<?php
// Variáveis do Dashboard
$total_fases = count($fases_padrao);
// Fase index vem do dashboard
$fase_atual_idx = $fase_index;
?>
<div class="view-header-timeline" style="margin-bottom:10px;">
    <h2>Jornada do Projeto</h2>
</div>

<!-- STEPPER (COPIED FROM HOME FOR CONSISTENCY) -->
<div class="stepper-scroll-container fade-in-up" style="margin-bottom:20px;">
    <div class="stepper-track">
        <?php foreach($fases_padrao as $idx => $nome_fase): 
            $status_class = ''; 
            $icon_content = $idx + 1;
            
            if($idx < $fase_atual_idx) {
                $status_class = 'completed';
                $icon_content = '✓';
            } elseif($idx == $fase_atual_idx) {
                $status_class = 'active';
            }
        ?>
        <div class="step-item <?= $status_class ?>">
            <div class="step-line"></div>
            <div class="step-circle"><?= $icon_content ?></div>
            <div class="step-label"><?= $nome_fase ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- PERCENT BADGE HIGH IMPACT -->
<div class="fade-in-up" style="text-align:center; margin-bottom:30px;">
     <div style="display:inline-flex; align-items:center; gap:10px; background:var(--color-primary-light); padding:10px 25px; border-radius:30px; box-shadow:var(--shadow-soft);">
        <span style="font-weight:800; font-size:1.5rem; color:var(--color-primary-dark);"><?= $progresso_porc ?>%</span>
        <span style="font-size:0.9rem; color:var(--color-primary-dark); font-weight:600; text-transform:uppercase;">Concluído</span>
     </div>
</div>


<div class="timeline-container fade-in-up">
    <?php if(count($timeline) > 0): foreach($timeline as $t): 
        $parts = explode("||COMENTARIO_USER||", $t['descricao']);
        $sys_desc = $parts[0];
        $admin_note = count($parts) > 1 ? $parts[1] : null;

        $icon = '📅'; 
        if(stripos($t['titulo_fase'], 'Início') !== false) $icon = '🚀';
        if(stripos($t['titulo_fase'], 'Conclusão') !== false || stripos($t['titulo_fase'], 'Pronto') !== false) $icon = '🎉';
        if(stripos($t['titulo_fase'], 'Pendência') !== false) $icon = '⚠️';
    ?>
    
    <div class="timeline-item">
        <div class="tl-icon"><?= $icon ?></div>
        <div class="tl-content">
            <span class="tl-date"><?= date('d/m/Y \à\s H:i', strtotime($t['data_movimento'])) ?></span>
            <h3 class="tl-title"><?= htmlspecialchars($t['titulo_fase']) ?></h3>
            <div class="tl-body">
                <?= $sys_desc ?>
            </div>
            <?php if($admin_note): ?>
            <div class="tl-admin-note">
                <strong>👷 Obs. do Engenheiro:</strong>
                <p><?= nl2br(htmlspecialchars($admin_note)) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php endforeach; else: ?>
        <div class="empty-state"><p>Nenhuma movimentação.</p></div>
    <?php endif; ?>
</div>

<script>
// Auto-scroll stepper
document.addEventListener('DOMContentLoaded', () => {
    const active = document.querySelector('.stepper-scroll-container .step-item.active');
    if(active) active.scrollIntoView({ behavior: 'auto', block: 'nearest', inline: 'center' });
});
</script>
