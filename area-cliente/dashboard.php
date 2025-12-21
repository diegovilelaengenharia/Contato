<?php
session_start();
require 'db.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: index.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Buscar Detalhes e Link do Drive
$stmtDet = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
$stmtDet->execute([$cliente_id]);
$detalhes = $stmtDet->fetch();

// Buscar Movimentos (Timeline)
$stmt = $pdo->prepare("SELECT * FROM processo_movimentos WHERE cliente_id = ? ORDER BY data_movimento DESC");
$stmt->execute([$cliente_id]);
$timeline = $stmt->fetchAll();

// Fallback para tabela antiga se timeline vazia... (mantido do código anterior se necessário, mas simplificado aqui)
if(count($timeline) == 0) {
    $stmtOld = $pdo->prepare("SELECT * FROM progresso WHERE cliente_id = ? ORDER BY data_fase DESC");
    $stmtOld->execute([$cliente_id]);
    $progresso = $stmtOld->fetchAll();
    
    foreach($progresso as $p) {
        $timeline[] = [
            'data_movimento' => $p['data_fase'],
            'titulo_fase' => $p['fase'],
            'descricao' => $p['descricao'],
            'status_tipo' => 'tramite',
            'departamento_origem' => '',
            'departamento_destino' => '',
            'anexo_url' => ''
        ];
    }
}

// Buscar Documentos
$stmtDoc = $pdo->prepare("SELECT * FROM documentos WHERE cliente_id = ?");
$stmtDoc->execute([$cliente_id]);
$documentos = $stmtDoc->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel do Cliente | Vilela Engenharia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../assets/logo.png" type="image/png">
    <style>
        :root {
            --color-bg: #f4f7f6;
            --color-surface: #ffffff;
            --color-text: #333333;
            --color-text-subtle: #666666;
            --color-border: #e0e0e0;
            --color-primary: #198754;
            --color-primary-strong: #146c43;
            --shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        body.dark-mode {
            --color-bg: #121212;
            --color-surface: #1e1e1e;
            --color-text: #e0e0e0;
            --color-text-subtle: #a0a0a0;
            --color-border: #333333;
            --shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        body { background-color: var(--color-bg); color: var(--color-text); font-family: 'Outfit', sans-serif; margin: 0; padding: 0; transition: background-color 0.3s, color 0.3s; }
        .container { width: min(1000px, 95%); margin: 40px auto; }
        
        .header-panel { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
        
        .card { background: var(--color-surface); padding: 32px; border-radius: 12px; box-shadow: var(--shadow); margin-bottom: 30px; border: 1px solid var(--color-border); }
        
        /* Timeline style */
        .timeline-item { border-left: 3px solid var(--color-primary); padding-left: 24px; margin-bottom: 32px; position: relative; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-item::before { content: ''; width: 14px; height: 14px; background: var(--color-primary); border-radius: 50%; position: absolute; left: -8.5px; top: 0; }
        .timeline-date { font-size: 0.9rem; color: var(--color-text-subtle); margin-bottom: 4px; display: block; }
        .timeline-title { font-weight: 700; color: var(--color-primary); margin: 0 0 8px; font-size: 1.2rem; }
        .timeline-desc { color: var(--color-text); opacity: 0.9; }

        /* Doc Links */
        .links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .doc-link { display: flex; align-items: center; gap: 16px; padding: 20px; border: 1px solid var(--color-border); border-radius: 8px; text-decoration: none; color: var(--color-text); transition: all 0.2s ease; background: var(--color-surface); }
        .doc-link:hover { transform: translateY(-3px); box-shadow: var(--shadow); border-color: var(--color-primary); }
        
        h1 { margin: 0; font-size: clamp(1.5rem, 3vw, 2rem); color: var(--color-text); }
        .badge-panel { background: var(--color-primary); color: white; padding: 4px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 700; display: inline-block; margin-top: 5px; }
        
        .btn-drive { color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-drive:hover { transform: translateY(-2px); filter: brightness(1.1); }
        
        .btn-logout { color: #d32f2f; text-decoration: none; font-weight: 600; padding: 8px 16px; border: 1px solid #d32f2f; border-radius: 12px; transition: 0.2s; }
        .btn-logout:hover { background: #fee; }

        .btn-toggle-theme { background: none; border: 1px solid var(--color-border); color: var(--color-text); padding: 8px 12px; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-family: inherit; font-size: 0.9rem; margin-right: 10px; }
        .btn-toggle-theme:hover { background: var(--color-border); }

        /* Stepper Client */
        .client-stepper { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; position: relative; overflow-x: auto; padding-bottom: 10px; }
        .client-stepper::-webkit-scrollbar { height: 6px; }
        .client-stepper::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
        .client-stepper::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 3px; background: var(--color-border); z-index: 0; }
        .s-item { position: relative; z-index: 1; text-align: center; min-width: 80px; display: flex; flex-direction: column; align-items: center; }
        .s-circle { width: 32px; height: 32px; background: var(--color-surface); border: 3px solid var(--color-border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--color-text-subtle); font-size: 14px; transition: 0.3s; }
        .s-label { margin-top: 8px; font-size: 0.75rem; color: var(--color-text-subtle); max-width: 100px; line-height: 1.2; font-weight: 500; transition: 0.3s;}
        
        .s-item.active .s-circle { border-color: var(--color-primary); background: var(--color-primary); color: white; }
        .s-item.active .s-label { color: var(--color-primary); font-weight: 700; }
        .s-item.completed .s-circle { border-color: var(--color-primary); background: var(--color-primary); color: white; opacity: 0.7; }
        .s-item.completed .s-label { color: var(--color-primary); opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-panel">
            <div style="width:100%;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <div>
                        <h1>Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?></h1>
                        <span class="badge-panel">Acompanhamento Online</span>
                    </div>
                    <div style="display:flex; align-items:center;">
                        <button class="btn-toggle-theme" onclick="toggleTheme()">🌓 Tema</button>
                        <a href="logout.php" class="btn-logout">Sair</a>
                    </div>
                </div>

                <!-- Botões Customizados -->
                <div style="display:flex; gap:15px; flex-wrap:wrap; margin-top:20px;">
                    
                    <!-- 1. Cadastro Inicial (Cinza) -->
                    <!-- Mapeado para Iniciais ou Pasta Geral se Iniciais vazio -->
                    <?php 
                        $link1 = !empty($detalhes['link_doc_iniciais']) ? $detalhes['link_doc_iniciais'] : ($detalhes['link_drive_pasta'] ?? '');
                        if(!empty($link1)): 
                    ?>
                        <a href="<?= htmlspecialchars($link1) ?>" target="_blank" class="btn-drive" style="background-color:#6c757d;">
                             Cadastro Inicial
                        </a>
                    <?php endif; ?>

                    <!-- 2. Status e Pendências (Amarelo) -->
                    <?php if(!empty($detalhes['link_doc_pendencias'])): ?>
                        <a href="<?= htmlspecialchars($detalhes['link_doc_pendencias']) ?>" target="_blank" class="btn-drive" style="background-color:#ffc107; color: #333;">
                            ⚠️ Status e Pendências
                        </a>
                    <?php endif; ?>

                    <!-- 3. Links e Documentos Finais (Verde) -->
                    <?php if(!empty($detalhes['link_doc_finais'])): ?>
                        <a href="<?= htmlspecialchars($detalhes['link_doc_finais']) ?>" target="_blank" class="btn-drive" style="background-color:#198754;">
                            ✅ Links e Documentos Finais
                        </a>
                    <?php endif; ?>

                </div>

                <!-- Stepper -->
                <?php 
                $etapa_atual = $detalhes['etapa_atual'] ?? '';
                // Mapa simples para highlight
                $mapa_fases = [
                    "Abertura de Processo (Guichê)" => "Guichê",
                    "Fiscalização (Parecer Fiscal)" => "Fiscalização",
                    "Triagem (Documentos Necessários)" => "Triagem",
                    "Comunicado de Pendências (Triagem)" => "Pendências",
                    "Análise Técnica (Engenharia)" => "Engenharia",
                    "Comunicado (Pendências e Taxas)" => "Taxas",
                    "Confecção de Documentos" => "Docs",
                    "Avaliação (ITBI/Averbação)" => "Avaliação",
                    "Processo Finalizado (Documentos Prontos)" => "Finalizado"
                ];
                $keys = array_keys($mapa_fases);
                $found_index = array_search($etapa_atual, $keys);
                if($found_index === false) $found_index = -1;
                ?>

                <div class="client-stepper">
                    <?php 
                    $i = 0;
                    foreach($mapa_fases as $full => $label): 
                        $status_class = '';
                        if ($i < $found_index) $status_class = 'completed';
                        else if ($i === $found_index) $status_class = 'active';
                    ?>
                        <div class="s-item <?= $status_class ?>">
                            <div class="s-circle"><?= ($i < $found_index) ? '✔' : ($i + 1) ?></div>
                            <span class="s-label"><?= $label ?></span>
                        </div>
                    <?php $i++; endforeach; ?>
                </div>

            </div>
        </header>

        <section class="timeline-section">
            <h2 style="color:var(--color-text); margin-bottom:20px;">Histórico Detalhado</h2>
            <?php if(count($timeline) > 0): ?>
                <?php foreach($timeline as $t): ?>
                    <div class="card timeline-item">
                        <span class="timeline-date"><?= date('d/m/Y \à\s H:i', strtotime($t['data_movimento'])) ?></span>
                        <h3 class="timeline-title"><?= htmlspecialchars($t['titulo_fase']) ?></h3>
                        <div class="timeline-desc"><?= nl2br(htmlspecialchars($t['descricao'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <p style="color:var(--color-text-subtle);">Nenhuma movimentação registrada ainda.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="docs-section">
            <h2 style="color:var(--color-text); margin-bottom:20px;">Outros Anexos</h2>
            <div class="links-grid">
                <?php foreach($documentos as $doc): ?>
                    <a href="<?= htmlspecialchars($doc['link_drive']) ?>" target="_blank" class="doc-link">
                        <div class="doc-icon">📄</div>
                        <div>
                            <div style="font-weight:600;"><?= htmlspecialchars($doc['titulo']) ?></div>
                            <div style="font-size:0.8rem; color:var(--color-text-subtle);">Clique para abrir</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if(count($documentos) == 0): ?>
                <p style="color:var(--color-text-subtle);">Nenhum documento avulso anexado.</p>
            <?php endif; ?>
        </section>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        // Carregar Tema Salvo
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
                <?php 
                $fases_padrao = [
                    "Guichê", "Fiscalização", "Triagem", "Pendências", "Engenharia", "Taxas", "Docs", "Avaliação", "Finalizado"
                ]; 
                // Mapa simples para índices (pois o nome completo no banco é longo)
                // Vamos tentar achar 'like' ou correspondência exata
                // Para simplificar, vamos assumir que a ordem é fixa.
                // Mas o banco tem o texto inteiro. Vamos usar busca de substring pra "highlight"
                
                $etapa_atual = $detalhes['etapa_atual'] ?? '';
                $found_index = -1;
               
                // Tenta achar o index da etapa atual baseada nos nomes curtos vs longos
                // Mapeamento Longo -> Curto (Key -> Label)
                $mapa_fases = [
                    "Abertura de Processo (Guichê)" => "Guichê",
                    "Fiscalização (Parecer Fiscal)" => "Fiscalização",
                    "Triagem (Documentos Necessários)" => "Triagem",
                    "Comunicado de Pendências (Triagem)" => "Pendências",
                    "Análise Técnica (Engenharia)" => "Engenharia",
                    "Comunicado (Pendências e Taxas)" => "Taxas",
                    "Confecção de Documentos" => "Docs",
                    "Avaliação (ITBI/Averbação)" => "Avaliação",
                    "Processo Finalizado (Documentos Prontos)" => "Finalizado"
                ];
                
                $keys = array_keys($mapa_fases);
                $found_index = array_search($etapa_atual, $keys);
                if($found_index === false) $found_index = -1;
                ?>

                <div class="client-stepper">
                    <?php 
                    $i = 0;
                    foreach($mapa_fases as $full => $label): 
                        $status_class = '';
                        if ($i < $found_index) $status_class = 'completed';
                        else if ($i === $found_index) $status_class = 'active';
                    ?>
                        <div class="s-item <?= $status_class ?>">
                            <div class="s-circle"><?= ($i < $found_index) ? '✔' : ($i + 1) ?></div>
                            <span class="s-label"><?= $label ?></span>
                        </div>
                    <?php $i++; endforeach; ?>
                </div>

            </div>
        </header>

        <section class="timeline-section">
            <h2 class="section-heading" style="margin-top:0; margin-bottom: 30px; margin-left: 20px;">Linha do Tempo do Processo</h2>
            
            <?php 
            // Os dados já foram preparados no início do arquivo (variável $timeline)
            ?>

            <?php if(count($timeline) > 0): ?>
                <?php foreach($timeline as $mov): ?>
                    <div class="timeline-card">
                        <!-- Ícone Dinâmico conforme Status -->
                        <?php
                            $icon = "🔄"; // Default
                            $bgClass = "status-tramite";
                            switch($mov['status_tipo']) {
                                case 'inicio': $icon = "🚩"; $bgClass = "status-inicio"; break;
                                case 'pendencia': $icon = "⚠️"; $bgClass = "status-pendencia"; break;
                                case 'documento': $icon = "📄"; $bgClass = "status-documento"; break;
                                case 'conclusao': $icon = "✅"; $bgClass = "status-conclusao"; break;
                            }
                        ?>
                        <div class="timeline-icon <?= $bgClass ?>"><?= $icon ?></div>

                        <div class="timeline-header">
                            <span class="timeline-date"><?= date('d/m/Y \à\s H:i', strtotime($mov['data_movimento'])) ?></span>
                            <?php if(!empty($mov['prazo_previsto'])): ?>
                                <span style="font-size:0.8rem; color:#d97706; font-weight:600;">Previsão: <?= date('d/m/Y', strtotime($mov['prazo_previsto'])) ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="timeline-title"><?= htmlspecialchars($mov['titulo_fase']) ?></h3>
                        
                        <?php if(!empty($mov['departamento_origem']) || !empty($mov['departamento_destino'])): ?>
                            <div class="timeline-flow">
                                <span><?= htmlspecialchars($mov['departamento_origem'] ?: 'Início') ?></span>
                                <span class="flow-arrow">➜</span>
                                <strong><?= htmlspecialchars($mov['departamento_destino'] ?: 'Conclusão') ?></strong>
                            </div>
                        <?php endif; ?>

                        <p class="timeline-desc"><?= nl2br(htmlspecialchars($mov['descricao'])) ?></p>

                        <?php if(!empty($mov['anexo_url'])): ?>
                            <a href="<?= htmlspecialchars($mov['anexo_url']) ?>" target="_blank" class="timeline-attachment">
                                📎 <?= htmlspecialchars($mov['anexo_nome'] ?: 'Visualizar Anexo') ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($mov['usuario_responsavel'])): ?>
                            <div style="margin-top:12px; font-size:0.8rem; color:#999;">
                                Resp: <?= htmlspecialchars($mov['usuario_responsavel']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--color-text-subtle); margin-left: 20px;">Nenhuma atualização recente encontrada para o seu processo.</p>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2 class="section-heading" style="margin-top:0;">Documentos e Arquivos</h2>
            <div class="links-grid">
                <?php if(count($documentos) > 0): ?>
                    <?php foreach($documentos as $doc): ?>
                        <a href="<?= htmlspecialchars($doc['link_drive']) ?>" target="_blank" class="doc-link">
                            <span class="doc-icon">📄</span>
                            <div>
                                <strong style="display:block; margin-bottom:4px;"><?= htmlspecialchars($doc['titulo']) ?></strong>
                                <small style="color: var(--color-text-subtle);">Clique para acessar</small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--color-text-subtle);">Nenhum documento disponível ainda.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>
