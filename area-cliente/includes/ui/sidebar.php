<?php
// Ensure metrics are available (or default to 0)
$kpi_pre_pendentes = $kpi_pre_pendentes ?? 0;
$count_ani = count($aniversariantes ?? []);
$count_par = count($parados ?? []);
?>

<!-- Fixed Sidebar Navigation (2 Buttons) -->
<div style="position:fixed; bottom:20px; left:20px; z-index:2000; display:flex; flex-direction:column; gap:10px;">
    
    <!-- 2. Avisos -->
    <button onclick="document.getElementById('modalNotificacoes').showModal()" style="display:flex; align-items:center; gap:10px; padding:12px 20px; background:white; border:1px solid #e0e0e0; border-radius:30px; cursor:pointer; font-weight:600; font-size:0.9rem; color:#444; box-shadow:0 4px 10px rgba(0,0,0,0.05); transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <span class="material-symbols-rounded" style="color:#ffc107;">notifications</span>
        Avisos
        <?php if($kpi_pre_pendentes > 0): ?>
            <span style="background:#dc3545; color:white; padding:2px 8px; border-radius:10px; font-size:0.75rem; margin-left:5px;"><?= $kpi_pre_pendentes ?></span>
        <?php endif; ?>
    </button>
    
    <!-- 1. Visão Geral (Home) -->
    <a href="gestao_admin_99.php" style="display:flex; align-items:center; gap:10px; padding:12px 20px; background:var(--color-primary); border:none; border-radius:30px; text-decoration:none; font-weight:600; font-size:0.9rem; color:white; box-shadow:0 4px 10px rgba(20, 108, 67, 0.3); transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <span class="material-symbols-rounded">dashboard</span>
        Visão Geral
    </a>

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
