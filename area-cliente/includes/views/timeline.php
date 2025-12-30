<?php
// Variáveis do Dashboard
$total_fases = count($fases_padrao);
// Fase index já calculado no dashboard ($fase_index)
?>
<div class="view-header-timeline">
    <h2 style="margin:0;">Jornada do Projeto</h2>
    
    <!-- Visual Summary -->
    <div class="tl-summary-box">
        <div class="tl-sum-text">
            <span style="font-size:2rem; font-weight:700; color:var(--color-primary);"><?= $fase_index + 1 ?></span>
            <span style="color:var(--text-muted); font-size:0.9rem;">de <?= $total_fases ?> etapas concluídas</span>
        </div>
        <div class="tl-mini-dots">
            <?php for($i=0; $i<$total_fases; $i++): 
                $active = ($i <= $fase_index) ? 'active' : '';
            ?>
                <div class="mini-dot <?= $active ?>"></div>
            <?php endfor; ?>
        </div>
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
