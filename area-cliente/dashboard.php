<?php
session_start();
require 'db.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: index.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// --- DATA FETCHING ---
// 1. Client & Details
$stmt = $pdo->prepare("SELECT c.*, d.* FROM clientes c LEFT JOIN processo_detalhes d ON c.id = d.cliente_id WHERE c.id = ?");
$stmt->execute([$cliente_id]);
$data = $stmt->fetch();
$nome_parts = explode(' ', $data['nome']);
$primeiro_nome = $nome_parts[0];
$endereco = $data['imovel_rua'] ?? ($data['endereco_imovel'] ?? 'Endereço não cadastrado');

// 2. Timeline
$stmt = $pdo->prepare("SELECT * FROM processo_movimentos WHERE cliente_id = ? ORDER BY data_movimento DESC");
$stmt->execute([$cliente_id]);
$timeline = $stmt->fetchAll();

// 3. Pendencies
$stmt = $pdo->prepare("SELECT * FROM processo_pendencias WHERE cliente_id = ? ORDER BY FIELD(status, 'pendente','vencido','analise','resolvido'), id DESC");
$stmt->execute([$cliente_id]);
$pendencias = $stmt->fetchAll();

// 4. Finance
$stmt = $pdo->prepare("SELECT * FROM processo_financeiro WHERE cliente_id = ? ORDER BY data_vencimento ASC");
$stmt->execute([$cliente_id]);
$financeiro = $stmt->fetchAll();

// Financial Summary
$fin_stats = ['total'=>0, 'pago'=>0, 'pendente'=>0];
foreach($financeiro as $f) {
    if($f['categoria'] == 'honorarios') $fin_stats['total'] += $f['valor']; // Assuming stats focus on main fees or total? Adjust logic if needed. 
    // Or maybe just sum everything
    if($f['status']=='pago') $fin_stats['pago'] += $f['valor'];
    else $fin_stats['pendente'] += $f['valor'];
}

// Drive IDs
function getDriveId($url) {
    if (preg_match('/folders\/([a-zA-Z0-9-_]+)/', $url, $m)) return $m[1];
    if (preg_match('/id=([a-zA-Z0-9-_]+)/', $url, $m)) return $m[1];
    return null;
}
$drive_id = !empty($data['link_drive_pasta']) ? getDriveId($data['link_drive_pasta']) : null;


// --- HANDLE UPLOAD POST ---
$msg_toast = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_pendencia') {
    $p_id = $_POST['p_id'];
    $files = $_FILES['arquivos'];
    $total = count($files['name']);
    $success = 0;
    
    $dir = __DIR__ . '/uploads/pendencias/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    for($i=0; $i<$total; $i++) {
        if($files['error'][$i] === 0) {
            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            if(in_array($ext, ['pdf','jpg','jpeg','png','doc','docx'])) {
                $new_name = $p_id . '_' . time() . '_' . $i . '.' . $ext;
                if(move_uploaded_file($files['tmp_name'][$i], $dir . $new_name)) {
                    $pdo->prepare("INSERT INTO processo_pendencias_arquivos (pendencia_id, arquivo_nome, arquivo_path, data_upload) VALUES (?, ?, ?, NOW())")->execute([$p_id, $files['name'][$i], 'uploads/pendencias/'.$new_name]);
                    $success++;
                }
            }
        }
    }
    
    if($success > 0) {
        $pdo->prepare("UPDATE processo_pendencias SET status='anexado' WHERE id=?")->execute([$p_id]);
        $pdo->prepare("INSERT INTO processo_movimentos (cliente_id, titulo_fase, data_movimento, descricao, status_tipo) VALUES (?, '📎 Arquivos Enviados', NOW(), ?, 'upload')")->execute([$cliente_id, "$success arquivo(s) enviado(s) pelo cliente para a pendência #$p_id"]);
        $msg_toast = "Arquivos enviados com sucesso!";
        // Reduce redirect loop by just setting variable
    } else {
        $msg_toast = "Erro ao enviar arquivos. Verifique os formatos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Área do Cliente | Vilela Engenharia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* CRITICAL INLINE STYLES TO PREVENT FOUC OR LOADING ERROR */
        body { font-family: 'Outfit', sans-serif; background-color: #f4f7f6; margin:0; }
        .hidden { display: none !important; }
    </style>
    <script>
        // Check Dark Mode Preference
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark-mode');
    </script>
</head>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Área do Cliente | Vilela Engenharia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <script>
        // Check Dark Mode Preference immediately
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark-mode');
    </script>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme']=='dark' ? 'dark-mode' : '' ?>">

<div class="container mobile-focused">
    
    <header class="main-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <img src="../assets/logo.png" alt="Vilela Engenharia" class="logo-mobile" style="height:40px;">
        <div style="display:flex; gap:10px;">
             <button class="theme-toggle icon-btn" onclick="toggleTheme()" id="theme-btn" style="width:40px; height:40px; border-radius:50%; border:none; background:var(--bg-card); box-shadow:var(--shadow-sm); cursor:pointer;">
                <?= isset($_COOKIE['theme']) && $_COOKIE['theme']=='dark' ? '☀️' : '🌙' ?>
            </button>
            <a href="logout.php" class="icon-btn" style="width:40px; height:40px; border-radius:50%; border:none; background:var(--bg-card); box-shadow:var(--shadow-sm); display:flex; align-items:center; justify-content:center; text-decoration:none;">🛑</a>
        </div>
    </header>

    <section class="card-resume fade-in">
        <div class="resume-info">
            <div class="client-avatar">
                <?php if(!empty($data['foto_perfil']) && file_exists(__DIR__ . '/' . $data['foto_perfil'])): ?>
                    <img src="<?= htmlspecialchars($data['foto_perfil']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                <?php else: ?>
                    <?= strtoupper(substr($primeiro_nome, 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="resume-text">
                <h2 style="margin:0; font-size:1.4rem;">Olá, <?= htmlspecialchars($primeiro_nome) ?></h2>
                <p style="margin:0; opacity:0.9; font-size:0.9rem;"><?= htmlspecialchars($endereco) ?></p>
            </div>
        </div>
        
        <div class="process-summary">
            <div class="progress-labels" style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:5px;">
                <span>Status: <strong><?= $data['etapa_atual'] ?? 'Em Análise' ?></strong></span>
                <span>75%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 75%;"></div>
            </div>
        </div>

        <!-- Placeholder for Data Modal -->
        <!-- <button class="btn-data-modal" onclick="openModal('modal-dados')">🔍 Ver Dados Cadastrais e Contrato</button> -->
    </section>

    <nav class="quick-nav">
        <button class="nav-card active" onclick="showSection('timeline', this)">
            <span class="icon">🕒</span>
            <span>Linha do Tempo</span>
        </button>
        <button class="nav-card" onclick="showSection('pendencias', this)">
            <span class="icon">⚠️</span>
            <span>Pendências</span>
            <?php if(count($pendencias) > 0): ?>
                <span class="badge"><?= count($pendencias) ?></span>
            <?php endif; ?>
        </button>
        <button class="nav-card" onclick="showSection('financeiro', this)">
            <span class="icon">💰</span>
            <span>Finanças</span>
        </button>
        <button class="nav-card" onclick="showSection('docs', this)">
            <span class="icon">☁️</span>
            <span>Nuvem</span>
        </button>
    </nav>

    <main class="content-area">
        
        <div id="sec-timeline" class="content-section fade-in">
            <h3 class="section-title">Evolução da Obra</h3>
            <div class="vertical-timeline">
                <?php if(count($timeline) > 0): foreach($timeline as $t): ?>
                <div class="timeline-item">
                    <div class="time-marker"></div>
                    <div class="time-content">
                        <small><?= date('d/m/Y', strtotime($t['data_movimento'])) ?></small>
                        <h4><?= htmlspecialchars($t['titulo_fase']) ?></h4>
                        <p><?= nl2br(str_replace('||COMENTARIO_USER||', '', htmlspecialchars($t['descricao']))) ?></p>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-muted">Nenhum histórico disponível.</p>
                <?php endif; ?>
            </div>
        </div>

        <div id="sec-pendencias" class="content-section hidden fade-in">
            <h3 class="section-title">Itens Pendentes</h3>
            <?php 
                $active_pendencies = array_filter($pendencias, fn($p) => $p['status'] != 'resolvido');
                if(empty($active_pendencies)): 
            ?>
                <div class="empty-state" style="text-align:center; padding:30px; color:var(--text-muted);">
                    <h3>🎉 Tudo Limpo!</h3>
                    <p>Nenhuma pendência encontrada.</p>
                </div>
            <?php else: ?>
                <?php foreach($pendencias as $p): ?>
                <div class="pendencia-card <?= $p['status'] == 'resolvido' ? 'resolvido' : '' ?>" style="background:var(--bg-card); padding:15px; border-radius:12px; border:1px solid var(--border-color); margin-bottom:15px;">
                    <div class="p-status" style="font-weight:bold; font-size:0.8rem; margin-bottom:5px; color:<?= $p['status']=='resolvido'?'#198754':'#dc3545' ?>">
                        <?= strtoupper($p['status']) ?>
                    </div>
                    <div class="p-desc"><?= $p['descricao'] ?></div>
                     <?php if($p['status']!='resolvido'): ?>
                        <button class="btn btn-primary" onclick="openUploadModal(<?= $p['id'] ?>)" style="width:100%; margin-top:10px; font-size:0.9rem;">
                            📎 Resolver / Anexar
                        </button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="sec-financeiro" class="content-section hidden fade-in">
            <h3 class="section-title">Resumo Financeiro</h3>
            <div class="fin-summary-cards">
                <div class="fin-card paid">
                    <span>Pago</span>
                    <strong>R$ <?= number_format($fin_stats['pago'], 2, ',', '.') ?></strong>
                </div>
                <div class="fin-card pending">
                    <span>Aberto</span>
                    <strong>R$ <?= number_format($fin_stats['pendente'], 2, ',', '.') ?></strong>
                </div>
            </div>
             <!-- List -->
             <div class="finance-list" style="margin-top:20px;">
                <?php foreach($financeiro as $f): ?>
                    <div style="background:var(--bg-card); padding:15px; border-radius:10px; border:1px solid var(--border-color); margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:600;"><?= htmlspecialchars($f['descricao']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-muted);"><?= date('d/m/Y', strtotime($f['data_vencimento'])) ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:bold;">R$ <?= number_format($f['valor'], 2, ',', '.') ?></div>
                            <div style="font-size:0.7rem; text-transform:uppercase; color:<?= $f['status']=='pago'?'#198754':'#dc3545' ?>"><?= $f['status'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
             </div>
        </div>

        <div id="sec-docs" class="content-section hidden fade-in">
            <h3 class="section-title">Arquivos em Nuvem</h3>
            <div class="drive-container">
                <?php if($drive_id): ?>
                    <iframe src="https://drive.google.com/embeddedfolderview?id=<?= $drive_id ?>#list" width="100%" height="400" style="border:none; border-radius:12px;"></iframe>
                <?php else: ?>
                    <p style="text-align:center;">Nenhuma pasta vinculada.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <a href="https://wa.me/5535984529577" class="fab-whatsapp" target="_blank">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
    </a>

</div>

<!-- UPLOAD MODAL (Re-integrated for functionality) -->
<div id="uploadModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Anexar Arquivos</h3>
            <button class="modal-close" onclick="closeUploadModal()">×</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_pendencia">
                <input type="hidden" name="p_id" id="modal_p_id">
                <p>Selecione um ou mais arquivos:</p>
                <input type="file" name="arquivos[]" multiple accept=".pdf,image/*,.doc,.docx" style="margin-bottom:20px; width:100%;">
                <button type="submit" class="btn btn-primary" style="width:100%">Enviar</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Lógica de Troca de Seções
    function showSection(id, btn) {
        document.querySelectorAll('.content-section').forEach(s => s.classList.add('hidden'));
        document.querySelectorAll('.nav-card').forEach(b => b.classList.remove('active'));
        document.getElementById('sec-' + id).classList.remove('hidden');
        btn.classList.add('active');
    }

    // Dark Mode
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        document.documentElement.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        document.cookie = "theme=" + (isDark ? 'dark' : 'light') + "; path=/";
        document.getElementById('theme-btn').innerText = isDark ? '☀️' : '🌙';
    }
    
    // Upload Modal
    function openUploadModal(pId) {
        document.getElementById('modal_p_id').value = pId;
        document.getElementById('uploadModal').classList.add('active');
    }
    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('active');
    }
</script>
</body>
</html>
