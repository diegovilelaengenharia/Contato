<div class="view-header-simple">
    <h2>Financeiro</h2>
    <p>Honorários e Taxas Oficiais.</p>
</div>

<!-- Summary Cards -->
<div class="finance-summary fade-in-up">
    <div class="fin-card highlight">
        <label>A Pagar</label>
        <strong>R$ <?= number_format($fin_stats['pendente'], 2, ',', '.') ?></strong>
    </div>
    <div class="fin-card">
        <label>Pago</label>
        <strong>R$ <?= number_format($fin_stats['pago'], 2, ',', '.') ?></strong>
    </div>
</div>

<div class="finance-sections fade-in-up">

    <!-- SEÇÃO: HONORÁRIOS (Verde) -->
    <div class="fin-section" style="border-top: 5px solid var(--color-primary);">
        <h3 class="fin-sec-header" style="color:var(--color-primary);">
            <span class="material-symbols-rounded">engineering</span> Honorários Técnicos
        </h3>
        <p class="fin-sec-desc">Valores referentes aos serviços da Vilela Engenharia.</p>
        
        <div class="fin-items-list">
            <?php 
            $honorarios = array_filter($financeiro, fn($f) => $f['categoria'] == 'honorarios');
            if(count($honorarios) > 0): foreach($honorarios as $f): 
                include __DIR__ . '/../partials/fin_row.php'; 
            endforeach;
            else: echo "<p class='empty-msg'>Nenhum lançamento.</p>"; endif; 
            ?>
        </div>
    </div>

    <!-- SEÇÃO: TAXAS (Azul/Gov) -->
    <div class="fin-section" style="border-top: 5px solid #0d6efd; margin-top:25px;">
        <h3 class="fin-sec-header" style="color:#0d6efd;">
            <span class="material-symbols-rounded">account_balance</span> Taxas do Governo
        </h3>
        <p class="fin-sec-desc">Taxas de aprovação, multas e emolumentos (Prefeitura/Cartório).</p>
        
        <div class="fin-items-list">
            <?php 
            $taxas = array_filter($financeiro, fn($f) => $f['categoria'] == 'taxas');
            if(count($taxas) > 0): foreach($taxas as $f): 
                include __DIR__ . '/../partials/fin_row.php';
            endforeach;
            else: echo "<p class='empty-msg'>Nenhuma taxa lançada.</p>"; endif; 
            ?>
        </div>
    </div>

</div>

<?php
// Criar o partial inline temporariamente para evitar erro se arquivo não existir
if(!file_exists(__DIR__ . '/../partials/fin_row.php')) {
    if(!is_dir(__DIR__ . '/../partials')) mkdir(__DIR__ . '/../partials', 0755, true);
    $partial_content = '
    <div class="fin-row">
        <div class="fin-icon-status status-<?= $f["status"] ?>">
            <?= ($f["status"]=="pago"?"✓":"$") ?>
        </div>
        <div class="fin-info">
            <strong><?= htmlspecialchars($f["descricao"]) ?></strong>
            <small>Venc: <?= date("d/m/y", strtotime($f["data_vencimento"])) ?></small>
        </div>
        <div class="fin-val">
            <span>R$ <?= number_format($f["valor"], 2, ",", ".") ?></span>
            <?php if($f["status"]=="pendente"): ?>
                <span class="badge-status pend">Aberto</span>
            <?php elseif($f["status"]=="pago"): ?>
                <span class="badge-status paid">Pago</span>
            <?php else: ?>
                <span class="badge-status late">Atrasado</span>
            <?php endif; ?>
        </div>
    </div>';
    file_put_contents(__DIR__ . '/../partials/fin_row.php', $partial_content);
}
?>
