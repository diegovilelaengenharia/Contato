<?php
// Ensure metrics are available (or default to 0)
$kpi_pre_pendentes = $kpi_pre_pendentes ?? 0;
$count_ani = count($aniversariantes ?? []);
$count_par = count($parados ?? []);
?>

<!-- Bottom-Right Discrete Speed Dial -->
<div class="br-fab-container" id="brFab">
    
    <div class="br-fab-menu">
        
        <!-- 4. Avisos -->
        <a href="#" onclick="document.getElementById('modalNotificacoes').showModal(); return false;" class="br-fab-item">
            <span class="br-fab-label">Avisos</span>
            <div class="br-fab-btn">
                <span class="material-symbols-rounded">notifications</span>
                <?php if($kpi_pre_pendentes > 0): ?>
                    <span class="fab-badge" style="position:absolute; top:-2px; right:-2px; background:#dc3545; color:white; font-size:10px; padding:2px 5px; border-radius:10px; border:2px solid white;"><?= $kpi_pre_pendentes ?></span>
                <?php endif; ?>
            </div>
        </a>

        <!-- 3. Matrículas -->
        <a href="#" class="br-fab-item">
            <span class="br-fab-label">Matrículas</span>
            <div class="br-fab-btn">
                <span class="material-symbols-rounded">assignment_ind</span>
            </div>
        </a>

        <!-- 2. Atende Oliveira -->
        <a href="#" class="br-fab-item">
            <span class="br-fab-label">Atende Oliveira</span>
            <div class="br-fab-btn">
                <span class="material-symbols-rounded">support_agent</span>
            </div>
        </a>

        <!-- 1. Visão Geral (Home) -->
        <a href="gestao_admin_99.php" class="br-fab-item">
            <span class="br-fab-label">Visão Geral</span>
            <div class="br-fab-btn">
                <span class="material-symbols-rounded">dashboard</span>
            </div>
        </a>

    </div>

    <!-- Main Settings Toggle -->
    <button class="br-fab-main" onclick="document.getElementById('brFab').classList.toggle('active')">
        <span class="material-symbols-rounded">settings</span>
    </button>

</div>

<script>
    // Click outside to close
    document.addEventListener('click', function(e) {
        const fab = document.getElementById('brFab');
        if (!fab.contains(e.target) && fab.classList.contains('active')) {
            fab.classList.remove('active');
        }
    });
</script>

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
