<div class="admin-mobile-header" style="display:none; background:#146c43; color:white; padding:15px 20px; text-align:center; border-bottom:4px solid #0f5132;">
    <img src="../assets/logo.png" alt="Vilela Engenharia" style="height:45px; margin-bottom:10px; display:block; margin-left:auto; margin-right:auto;">
    <h3 style="margin:0 0 5px 0; font-size:1.1rem; text-transform:uppercase; letter-spacing:1px; font-weight:800;">Gestão Administrativa</h3>
    <div style="font-size:0.85rem; opacity:0.9; line-height:1.4;">
        Eng. Diego Vilela &nbsp;|&nbsp; CREA-MG: 235474/D<br>
        vilela.eng.mg@gmail.com &nbsp;|&nbsp; (35) 98452-9577
    </div>
</div>

<!-- Top Fixed Navigation Bar -->
<div class="top-nav-container">
    
    <!-- 1. Visão Geral -->
    <a href="gestao_admin_99.php" class="top-nav-btn" style="border-color:var(--color-primary);">
        <span class="material-symbols-rounded" style="color:var(--color-primary);">dashboard</span>
        Visão Geral
    </a>

    <!-- 2. Clientes (Dropdown) -->
    <div class="top-nav-dropdown" style="position:relative;">
        <button class="top-nav-btn" onclick="toggleTopNavDropdown(this)" style="cursor:pointer;">
            <span class="material-symbols-rounded" style="color:#0d6efd;">groups</span>
            Clientes
            <span class="material-symbols-rounded" style="font-size:1rem; margin-left:5px; color:#aaa;">expand_more</span>
        </button>
        <div class="top-nav-dropdown-menu">
            <?php 
            // Ensure $clientes is available. If not, fetch it lightly.
            if(!isset($clientes)) {
                $clientes_nav = $pdo->query("SELECT id, nome FROM users WHERE tipo='cliente' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $clientes_nav = $clientes;
            }
            
            if(empty($clientes_nav)): ?>
                <div style="padding:10px; color:#666; font-size:0.9rem;">Nenhum cliente</div>
            <?php else: ?>
                <?php foreach($clientes_nav as $cnav): ?>
                    <a href="?cliente_id=<?= $cnav['id'] ?>" class="dropdown-item">
                        <?= htmlspecialchars($cnav['nome']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. Acesso Rápido (Dropdown) -->
    <div class="top-nav-dropdown" style="position:relative;">
        <button class="top-nav-btn" onclick="toggleTopNavDropdown(this)" style="cursor:pointer;">
            <span class="material-symbols-rounded" style="color:#6610f2;">link</span>
            Acesso Rápido
            <span class="material-symbols-rounded" style="font-size:1rem; margin-left:5px; color:#aaa;">expand_more</span>
        </button>
        <div class="top-nav-dropdown-menu" style="width:220px;">
            <a href="https://oliveira.atende.net/atendenet?source=pwa" target="_blank" class="dropdown-item">
                <span class="material-symbols-rounded" style="font-size:1.1rem; vertical-align:middle; margin-right:5px; color:#009688;">support_agent</span>
                Atende Oliveira
            </a>
            <a href="https://ridigital.org.br/VisualizarMatricula/DefaultVM.aspx?from=menu" target="_blank" class="dropdown-item">
                <span class="material-symbols-rounded" style="font-size:1.1rem; vertical-align:middle; margin-right:5px; color:#6f42c1;">assignment_ind</span>
                Matrículas
            </a>
        </div>
    </div>

    <!-- 4. Avisos -->
    <button onclick="document.getElementById('modalNotificacoes').showModal()" class="top-nav-btn" style="cursor:pointer;">
        <span class="material-symbols-rounded" style="color:#fd7e14;">notifications</span>
        Avisos
        <?php if(isset($kpi_pre_pendentes) && $kpi_pre_pendentes > 0): ?>
            <span class="fab-badge-top"><?= $kpi_pre_pendentes ?></span>
        <?php endif; ?>
    </button>

</div>
<div style="height:60px;"></div> <!-- Spacer to push content down -->

<script>
function toggleTopNavDropdown(btn) {
    // Prevent event bubbling to window
    event.stopPropagation();
    
    const dropdown = btn.closest('.top-nav-dropdown');
    // Toggle current
    dropdown.classList.toggle('active');
}

// Close when clicking outside
window.addEventListener('click', function(e) {
    if (!e.target.closest('.top-nav-dropdown')) {
        document.querySelectorAll('.top-nav-dropdown.active').forEach(d => {
            d.classList.remove('active');
        });
    }
});
</script>
