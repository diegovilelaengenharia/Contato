<?php
// Ensure metrics are available (or default to 0)
$kpi_pre_pendentes = $kpi_pre_pendentes ?? 0;
?>
<button class="mobile-menu-toggle" onclick="toggleSidebar()">
    ☰ Menu de Navegação
</button>
<aside class="sidebar" id="mobileSidebar" style="display:flex; flex-direction:column; height:calc(100vh - 45px); position:sticky; top:45px; overflow-y:auto; overflow-x:hidden;">
    
    <!-- Floating Toggle Button (Desktop Only) -->
    <button id="sidebarToggler" onclick="toggleDesktopSidebar()" class="sidebar-toggler" title="Recolher/Expandir Menu">
        <span class="material-symbols-rounded">chevron_left</span>
    </button>
    <script>
        // Logic moved here
        (function(){
            if(localStorage.getItem('sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
        function toggleDesktopSidebar() {
            document.documentElement.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed);
            
            // Icon Rotation logic handled by CSS or updated here
            // We will use CSS rotation for the chevron
        }
    </script>

    <!-- Client List REMOVED per user request -->
    <div style="margin-bottom:20px;"></div>
    
    <nav class="sidebar-menu">
        <h4 style="font-size:0.75rem; text-transform:uppercase; color:#adb5bd; font-weight:700; margin:10px 0 5px 10px;">Principal</h4>
        <a href="gestao_admin_99.php" class="btn-menu <?= (!isset($_GET['cliente_id']) && !isset($_GET['novo']) && !isset($_GET['importar'])) ? 'active' : '' ?>">
            <span class="material-symbols-rounded">dashboard</span>
            <span class="menu-text">Visão Geral</span>
        </a>
        
        <?php 
            // Lógica de Cor: Amarelo se tiver pendências, Padrão (branco) se não.
            $alert_color_style = ($kpi_pre_pendentes > 0) ? 
                'background: linear-gradient(135deg, #fff3cd, #ffecb5); color: #856404; border: 1px solid #ffeeba;' : 
                ''; 
        ?>
        <button onclick="document.getElementById('modalNotificacoes').showModal()" class="btn-menu" style="cursor:pointer; text-align:left; width:100%; font-family:inherit; font-size:inherit; transition: 0.3s; <?= $alert_color_style ?>">
            <span class="material-symbols-rounded">notifications</span>
            <span class="menu-text">Avisos</span>
            <?php if($kpi_pre_pendentes > 0): ?>
                <span style="background:#dc3545; color:white; padding:1px 8px; border-radius:12px; font-size:0.75rem; margin-left:auto; line-height:1.2; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight:bold;"><?= $kpi_pre_pendentes ?></span>
            <?php endif; ?>
        </button>

        <!-- Widget: Aniversariantes (Simplificado) -->
        <?php 
            $count_ani = count($aniversariantes ?? []);
            // Mapeamento de Meses Simples
            $meses_pt = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        ?>
        <div class="btn-menu" onclick="document.getElementById('modalAniversariantes').showModal()" style="cursor:pointer; justify-content: space-between;">
            <span style="display:flex; align-items:center; gap:10px;">
                <span class="material-symbols-rounded" style="color:#fd7e14;">cake</span>
                <span class="menu-text">Aniversários</span>
            </span>
            <span style="background:#fff3cd; color:#856404; padding:2px 8px; border-radius:10px; font-weight:bold; font-size:0.75rem;"><?= $count_ani ?></span>
        </div>

        <!-- Widget: Parados (Simplificado) -->
        <?php $count_par = count($parados ?? []); ?>
        <div class="btn-menu" onclick="document.getElementById('modalParados').showModal()" style="cursor:pointer; justify-content: space-between;">
            <span style="display:flex; align-items:center; gap:10px;">
                <span class="material-symbols-rounded" style="color:#dc3545;">timer_off</span>
                 <span class="menu-text">Parados</span>
            </span>
            <span style="background:#f8d7da; color:#dc3545; padding:2px 8px; border-radius:10px; font-weight:bold; font-size:0.75rem;"><?= $count_par ?></span>
        </div>
        

    </nav>



    <!-- BRANDING FOOTER (Moved from Header) -->
    <div style="margin-top:auto; padding-top:20px; border-top:1px solid #eee; text-align:center; padding-bottom:10px;">
        <img src="../assets/logo.png" alt="Vilela Engenharia" style="height:35px; margin-bottom:8px; opacity:0.8;">
        <h5 style="margin:0 0 5px 0; font-size:0.8rem; text-transform:uppercase; color:#333; font-weight:700;">Gestão Administrativa</h5>
        <div style="font-size:0.7rem; color:#666; line-height:1.4;">
            Eng. Diego Vilela<br>
            CREA-MG: 235474/D<br>
            vilela.eng.mg@gmail.com<br>
            (35) 98452-9577
        </div>
    </div>
</aside>
