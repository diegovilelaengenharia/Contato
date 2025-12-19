<?php
// gestao_admin_99.php - Painel Administrativo
session_start();
require 'db.php';

// Senha mestra simples para acesso ao painel (RECOMENDADO ALTERAR DEPOIS)
$minha_senha_mestra = "VilelaAdmin2025"; 

if (isset($_POST['login_admin'])) {
    if ($_POST['senha_mestra'] === $minha_senha_mestra) {
        $_SESSION['admin_logado'] = true;
    } else {
        echo "<script>alert('Senha errada!');</script>";
    }
}

// Lógica de Login (Admin)
if (!isset($_SESSION['admin_logado'])) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login | Vilela Engenharia</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../style.css">
        <style>
            body { 
                display: flex; 
                flex-direction: column;
                align-items: center; 
                justify-content: center; 
                min-height: 100vh; 
                background: var(--color-bg); 
                padding: 20px;
            }
            .login-card {
                background: white; 
                padding: 40px; 
                border-radius: var(--radius-large); 
                box-shadow: var(--shadow-soft); 
                width: min(400px, 100%); 
                text-align: center;
            }
            .logo-img { height: 80px; width: auto; margin-bottom: 24px; }
            input { width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: var(--radius-small); font-family: inherit; }
            button { width: 100%; background: var(--color-primary); color: white; border: none; padding: 12px; border-radius: var(--radius-small); font-weight: 700; cursor: pointer; transition: 0.2s; }
            button:hover { background: var(--color-primary-strong); }
        </style>
    </head>
    <body>
        <div class="login-card">
            <img src="../assets/logo.png" alt="Vilela Engenharia" class="logo-img">
            <h2 style="margin: 0 0 24px; color: var(--color-primary-strong);">Acesso Administrativo</h2>
            <form method="POST">
                <input type="password" name="senha_mestra" placeholder="Senha do Administrador" REQUIRED>
                <button type="submit" name="login_admin">Entrar no Painel</button>
            </form>
            <p style="margin-top: 20px; font-size: 0.9rem;"><a href="../index.html" style="color: var(--color-text-subtle); text-decoration: none;">&larr; Voltar ao site</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// --- LÓGICA DO PAINEL ---

// 1. Cadastrar Novo Cliente
if (isset($_POST['novo_cliente'])) {
    $nome = $_POST['nome'];
    $user = $_POST['usuario'];
    $pass = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO clientes (nome, usuario, senha) VALUES (?, ?, ?)");
    if($stmt->execute([$nome, $user, $pass])) {
        $msg = "Cliente cadastrado com sucesso!";
        $msg_type = "success";
    } else {
        $msg = "Erro ao cadastrar. Usuário já existe?";
        $msg_type = "error";
    }
}

// 2. Adicionar Progresso
if (isset($_POST['novo_progresso'])) {
    $stmt = $pdo->prepare("INSERT INTO progresso (cliente_id, fase, data_fase, descricao) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['cliente_id'], $_POST['fase'], $_POST['data'], $_POST['desc']]);
    $msg = "Progresso atualizado!";
    $msg_type = "success";
}

// 3. Adicionar Documento
if (isset($_POST['novo_doc'])) {
    $stmt = $pdo->prepare("INSERT INTO documentos (cliente_id, titulo, link_drive) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['cliente_id'], $_POST['titulo'], $_POST['link']]);
    $msg = "Documento adicionado!";
    $msg_type = "success";
}

// Buscar clientes para o select
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão Vilela | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/logo.png" type="image/png">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            padding: 40px 20px; 
            max-width: 1100px; 
            margin: 0 auto; 
            background-color: var(--color-bg);
            color: var(--color-text);
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 40px; 
            background: white;
            padding: 20px 30px;
            border-radius: var(--radius-large);
            box-shadow: var(--shadow-soft);
        }
        
        .logo-section { display: flex; align-items: center; gap: 16px; }
        .logo-section img { height: 50px; width: auto; }
        .logo-section h1 { margin: 0; font-size: 1.5rem; color: var(--color-primary-strong); }

        .dashboard-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 24px; 
        }

        .card { 
            background: white; 
            padding: 30px; 
            border-radius: var(--radius-large); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-soft); 
        }

        .card h3 { 
            margin-top: 0; 
            color: var(--color-primary-strong); 
            font-size: 1.25rem; 
            border-bottom: 2px solid var(--color-surface-soft); 
            padding-bottom: 12px; 
            margin-bottom: 20px;
        }

        /* Form Elements */
        label { display: block; margin-bottom: 6px; font-weight: 500; color: var(--color-text-subtle); font-size: 0.9rem; }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 16px; 
            border: 1px solid #e0e0e0; 
            border-radius: var(--radius-small); 
            font-family: inherit; 
            background: #fafafa;
            transition: 0.2s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(27, 154, 128, 0.1);
        }

        button.btn-action { 
            width: 100%; 
            background: var(--color-primary); 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: var(--radius-small); 
            cursor: pointer; 
            font-weight: 700; 
            font-size: 1rem;
            transition: 0.2s; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        button.btn-action:hover { background: var(--color-primary-strong); }

        .btn-logout { 
            color: #d32f2f; 
            text-decoration: none; 
            font-weight: 600; 
            padding: 10px 20px; 
            border: 1px solid rgba(211, 47, 47, 0.2); 
            border-radius: 99px; 
            transition: 0.2s; 
        }
        .btn-logout:hover { background: #feebeb; border-color: #d32f2f; }

        /* Types of cards */
        .card-new-client { border-top: 4px solid var(--color-primary); }
        .card-progress { border-top: 4px solid var(--color-accent); }
        .card-docs { border-top: 4px solid #4a90e2; }

        /* Notification */
        .notification {
            position: fixed; top: 20px; right: 20px; z-index: 100;
            padding: 16px 24px; border-radius: 12px; color: white; font-weight: 600;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        }
        .success { background: var(--color-primary); }
        .error { background: #d32f2f; }
        
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

    <?php if(isset($msg)): ?>
        <div class="notification <?= $msg_type ?>" onclick="this.style.display='none'">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <header class="header">
        <div class="logo-section">
            <img src="../assets/logo.png" alt="Logo">
            <h1>Gestão Administrativa</h1>
        </div>
        <a href="logout.php" class="btn-logout">Sair do Sistema</a>
    </header>
    
    <div class="dashboard-grid">
        
        <!-- CARD 1: NOVO CLIENTE -->
        <div class="card card-new-client">
            <h3>👤 Cadastrar Cliente</h3>
            <form method="POST">
                <label>Nome Completo</label>
                <input type="text" name="nome" placeholder="Ex: João da Silva" required>
                
                <label>Login de Acesso</label>
                <input type="text" name="usuario" placeholder="Ex: joaosilva (sem espaços)" required>
                
                <label>Senha Inicial</label>
                <input type="text" name="senha" placeholder="Ex: 123456" required>
                
                <button type="submit" name="novo_cliente" class="btn-action">Criar Acesso</button>
            </form>
        </div>

        <!-- CARD 2: PROGRESSO -->
        <div class="card card-progress">
            <h3>📈 Atualizar Progresso</h3>
            <form method="POST">
                <label>Selecione o Cliente</label>
                <select name="cliente_id" required>
                    <option value="">-- Escolha --</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Fase / Status</label>
                <input type="text" name="fase" placeholder="Ex: Protocolo Prefeitura" required>
                
                <label>Data</label>
                <input type="date" name="data" value="<?= date('Y-m-d') ?>" required>
                
                <label>Descrição Detalhada</label>
                <textarea name="desc" placeholder="Detalhes sobre esta etapa..." rows="3" required></textarea>
                
                <button type="submit" name="novo_progresso" class="btn-action" style="background: var(--color-accent); color: #333;">Registrar Fase</button>
            </form>
        </div>

        <!-- CARD 3: DOCUMENTOS -->
        <div class="card card-docs">
            <h3>📂 Anexar Documentos</h3>
            <form method="POST">
                <label>Selecione o Cliente</label>
                <select name="cliente_id" required>
                    <option value="">-- Escolha --</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Título do Arquivo</label>
                <input type="text" name="titulo" placeholder="Ex: Alvará de Construção.pdf" required>
                
                <label>Link (Google Drive/Dropbox)</label>
                <input type="url" name="link" placeholder="https://..." required>
                
                <button type="submit" name="novo_doc" class="btn-action" style="background: #4a90e2;">Salvar Documento</button>
            </form>
        </div>

    </div>

    <script>
        // Auto-hide notification
        setTimeout(function() {
            const notif = document.querySelector('.notification');
            if(notif) {
                notif.style.opacity = '0';
                setTimeout(() => notif.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>
