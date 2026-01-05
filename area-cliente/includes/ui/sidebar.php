<?php
// Ensure metrics are available (or default to 0)
$kpi_pre_pendentes = $kpi_pre_pendentes ?? 0;
$count_ani = count($aniversariantes ?? []);
$count_par = count($parados ?? []);
?>

<!-- Top-Left Fixed Circular Buttons -->
<div style="position:fixed; top:20px; left:20px; z-index:2000; display:flex; flex-direction:column; gap:15px;">
    
    <!-- 1. Visão Geral (Home) -->
    <a href="gestao_admin_99.php" class="tl-nav-btn" data-title="Visão Geral" style="background:var(--color-primary); color:white;">
        <span class="material-symbols-rounded">dashboard</span>
    </a>

    <!-- 2. Avisos -->
    <button onclick="document.getElementById('modalNotificacoes').showModal()" class="tl-nav-btn" data-title="Avisos" style="background:#fff; color:#ffc107; border: 1px solid #ffc107;">
        <span class="material-symbols-rounded">notifications</span>
        <?php if($kpi_pre_pendentes > 0): ?>
            <span class="tl-nav-badge"><?= $kpi_pre_pendentes ?></span>
        <?php endif; ?>
    </button>
    
</div>

<!-- Mobile Branding Footer (Optional: Fixed at bottom left or removed?) 
     User requested discreet buttons. Let's keep it clean and remove branding from screen, 
     maybe just keep it in the header if it exists or footer if we add one.
     For now, removed from sidebar as requested.
-->

<script>
    function toggleFab() {
        document.querySelector('.fab-container').classList.toggle('active');
        const icon = document.querySelector('.fab-main .material-symbols-rounded');
        // Icon rotation handled by CSS
    }
    
    // Auto-close on click outside
    document.addEventListener('click', function(event) {
        const fab = document.querySelector('.fab-container');
        const isClickInside = fab.contains(event.target);
        if (!isClickInside && fab.classList.contains('active')) {
            fab.classList.remove('active');
        }
    });
</script>
