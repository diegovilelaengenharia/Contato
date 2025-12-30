<?php
// Garantir que variáveis existem (fallback)
$primeiro_nome = $primeiro_nome ?? 'Cliente';
$etapa_atual = $etapa_atual ?? 'Início';
$progresso_porc = $progresso_porc ?? 10;
$endereco = $endereco ?? 'Endereço não informado';
?>

<!-- HEADER EXPANDIDO & DARK MODE -->
<div class="header-expanded">
    <div class="header-top-row">
        <div class="user-info-block">
            <div class="user-avatar-u">
                <?php if(!empty($data['foto_perfil']) && file_exists(__DIR__ . '/../../' . $data['foto_perfil'])): ?>
                    <img src="<?= htmlspecialchars($data['foto_perfil']) ?>" alt="Foto">
                <?php else: ?>
                    <span><?= strtoupper(substr($primeiro_nome, 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div class="user-text">
                <h1><?= htmlspecialchars($primeiro_nome) ?></h1>
                <p class="address-badges">
                    <span class="badg">🏠 <?= mb_strimwidth($endereco, 0, 25, "...") ?></span>
                    <?php if(!empty($data['area_construida'])): ?>
                        <span class="badg">📐 <?= $data['area_construida'] ?>m²</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <button onclick="toggleTheme()" class="btn-theme-toggle" title="Alternar Tema">
            <span class="material-symbols-rounded">dark_mode</span>
        </button>
    </div>
</div>

<!-- ALERTAS URGENTES (Destaque Máximo) -->
<?php 
$pendencias_abertas = array_filter($pendencias, fn($p) => $p['status'] != 'resolvido');
$count_pend = count($pendencias_abertas);
if($count_pend > 0): 
?>
<div class="urgent-alert fade-in-up" onclick="window.location.href='?view=pendencias'">
    <div class="ua-icon">⚠️</div>
    <div class="ua-content">
        <strong>Ação Necessária!</strong>
        <p>Você tem <?= $count_pend ?> pendência(s) aguardando.</p>
    </div>
    <button class="btn-resolve">RESOLVER</button>
</div>
<?php endif; ?>

<!-- STORY CARD: PROGRESSO REAL -->
<div class="story-card fade-in-up">
    <div class="story-header" style="justify-content:space-between; display:flex;">
        <span class="story-label">Status do Processo</span>
        <span class="story-percent"><?= $progresso_porc ?>%</span>
    </div>
    
    <h2 class="story-title"><?= $etapa_atual ?></h2>
    
    <div class="progress-bar-container">
        <div class="progress-bar-fill" style="width: <?= $progresso_porc ?>%;"></div>
    </div>
    
    <div class="story-footer">
        <small>Última atualização: <?= date('d/m/Y', strtotime($detalhes['ultima_att'] ?? 'now')) ?></small>
        <a href="?view=timeline" style="color:#a8e6cf; text-decoration:none; font-weight:600;">Ver Detalhes →</a>
    </div>
</div>

<!-- QUICK ACTIONS (Compacto) -->
<h3 class="section-title">Acesso Rápido</h3>
<div class="grid-actions-compact fade-in-up" style="animation-delay: 0.1s;">
    <div class="action-btn-mini" onclick="window.location.href='?view=financeiro'">
        <span class="material-symbols-rounded">payments</span>
        <small>Finanças</small>
    </div>
    <div class="action-btn-mini" onclick="window.location.href='?view=arquivos'">
        <span class="material-symbols-rounded">folder_open</span>
        <small>Projetos</small>
    </div>
    <div class="action-btn-mini" onclick="window.location.href='?view=timeline'">
        <span class="material-symbols-rounded">history</span>
        <small>Timeline</small>
    </div>
    <a href="https://wa.me/5535984529577" target="_blank" class="action-btn-mini whatsapp">
        <span class="material-symbols-rounded">chat</span>
        <small>Whatsapp</small>
    </a>
</div>
