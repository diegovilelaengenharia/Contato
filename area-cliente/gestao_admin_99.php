<?php
session_start();
require 'db.php';

// --- Configuração e Segurança ---
$minha_senha_mestra = "VilelaAdmin2025"; // Mantida para referência ou dupla checagem futura

// Verifica Sessão
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Se não estiver logado, manda para o login unificado
    header("Location: index.php");
    exit;
}

// Logout
if (isset($_GET['sair'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- Processamento de Formulários ---

// --- Processamento de Formulários ---

// 0. Salvar Detalhes (Abas 1, 2, 3, 4)
if (isset($_POST['salvar_detalhes'])) {
    $cid = $_POST['cliente_id'];
    
    // Verifica se já existe registro na tabela detalhes
    $check = $pdo->prepare("SELECT id FROM processo_detalhes WHERE cliente_id = ?");
    $check->execute([$cid]);
    $exists = $check->fetch();

    if ($exists) {
        $sql = "UPDATE processo_detalhes SET 
            tipo_pessoa=?, cpf_cnpj=?, rg_ie=?, estado_civil=?, profissao=?, endereco_residencial=?, contato_email=?, contato_tel=?,
            inscricao_imob=?, num_matricula=?, endereco_imovel=?, area_terreno=?, area_construida=?, zoneamento=?,
            resp_tecnico=?, registro_prof=?, num_art_rrt=?,
            status_taxa_aprovacao=?, status_issqn=?, status_multas=?
            WHERE cliente_id=?";
        $params = [
            $_POST['tipo_pessoa'], $_POST['cpf_cnpj'], $_POST['rg_ie'], $_POST['estado_civil'], $_POST['profissao'], $_POST['endereco_residencial'], $_POST['contato_email'], $_POST['contato_tel'],
            $_POST['inscricao_imob'], $_POST['num_matricula'], $_POST['endereco_imovel'], $_POST['area_terreno'], $_POST['area_construida'], $_POST['zoneamento'],
            $_POST['resp_tecnico'], $_POST['registro_prof'], $_POST['num_art_rrt'],
            isset($_POST['status_taxa_aprovacao']) ? 1 : 0, isset($_POST['status_issqn']) ? 1 : 0, isset($_POST['status_multas']) ? 1 : 0,
            $cid
        ];
    } else {
        $sql = "INSERT INTO processo_detalhes (
            tipo_pessoa, cpf_cnpj, rg_ie, estado_civil, profissao, endereco_residencial, contato_email, contato_tel,
            inscricao_imob, num_matricula, endereco_imovel, area_terreno, area_construida, zoneamento,
            resp_tecnico, registro_prof, num_art_rrt,
            status_taxa_aprovacao, status_issqn, status_multas,
            cliente_id
        ) VALUES (?,?,?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?, ?,?,?, ?)";
        $params = [
            $_POST['tipo_pessoa'], $_POST['cpf_cnpj'], $_POST['rg_ie'], $_POST['estado_civil'], $_POST['profissao'], $_POST['endereco_residencial'], $_POST['contato_email'], $_POST['contato_tel'],
            $_POST['inscricao_imob'], $_POST['num_matricula'], $_POST['endereco_imovel'], $_POST['area_terreno'], $_POST['area_construida'], $_POST['zoneamento'],
            $_POST['resp_tecnico'], $_POST['registro_prof'], $_POST['num_art_rrt'],
            isset($_POST['status_taxa_aprovacao']) ? 1 : 0, isset($_POST['status_issqn']) ? 1 : 0, isset($_POST['status_multas']) ? 1 : 0,
            $cid
        ];
    }
    
    try {
        $pdo->prepare($sql)->execute($params);
        $sucesso = "Dados do processo atualizados com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao salvar detalhes: " . $e->getMessage();
    }
}

// 1. Cadastrar Cliente
if (isset($_POST['novo_cliente'])) {
    $nome = $_POST['nome'];
    $user = $_POST['usuario'];
    $pass = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, usuario, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $user, $pass]);
        $novo_id = $pdo->lastInsertId();
        
        // Cria registro vazio em detalhes
        $pdo->prepare("INSERT INTO processo_detalhes (cliente_id) VALUES (?)")->execute([$novo_id]);
        
        $sucesso = "Cliente $nome cadastrado!";
    } catch (PDOException $e) {
        $erro = "Erro: Usuário já existe ou dados inválidos.";
    }
}

// 2. Adicionar Progresso (Timeline)
if (isset($_POST['novo_movimento'])) {
    $sql = "INSERT INTO processo_movimentos (cliente_id, titulo_fase, data_movimento, descricao, status_tipo, departamento_origem, departamento_destino, usuario_responsavel, anexo_url, anexo_nome) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([
        $_POST['cliente_id'], $_POST['titulo'], $_POST['data'], $_POST['descricao'], $_POST['status_tipo'],
        $_POST['origem'], $_POST['destino'], $_POST['responsavel'], $_POST['anexo_url'], $_POST['anexo_nome']
    ]);
    $sucesso = "Movimento registrado na timeline!";
}

// 2.1 Exclusão Movimento
if (isset($_GET['del_mov'])) {
    $pdo->prepare("DELETE FROM processo_movimentos WHERE id=?")->execute([$_GET['del_mov']]);
    header("Location: ?cliente_id=".$_GET['cid']."&tab=timeline"); exit;
}

// 2.2 Atualizar Etapa Atual (Stepper)
if (isset($_POST['atualizar_etapa'])) {
    $nova_etapa = $_POST['nova_etapa'];
    $cid = $_POST['cliente_id'];
    
    try {
        // Atualiza a tabela de detalhes
        // (Assume que o registro detalhes já existe, pois é criado ao criar cliente)
        $pdo->prepare("UPDATE processo_detalhes SET etapa_atual = ? WHERE cliente_id = ?")->execute([$nova_etapa, $cid]);
        
        // Opcional: Registrar automaticamente na timeline
        $titulo = "Atualização de Fase: " . $nova_etapa;
        $desc = "Processo avançou para a fase de " . $nova_etapa;
        
        $sql = "INSERT INTO processo_movimentos (cliente_id, titulo_fase, data_movimento, descricao, status_tipo) VALUES (?, ?, NOW(), ?, 'conclusao')";
        $pdo->prepare($sql)->execute([$cid, $titulo, $desc]);

        $sucesso = "Fase atualizada para: $nova_etapa";
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar etapa: " . $e->getMessage();
    }
}

// 3. Adicionar Documento
if (isset($_POST['novo_doc'])) {
    $stmt = $pdo->prepare("INSERT INTO documentos (cliente_id, titulo, link_drive) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['cliente_id'], $_POST['titulo'], $_POST['link']]);
    $sucesso = "Documento anexado!";
}

if (isset($_GET['delete_cliente'])) {
    $pdo->prepare("DELETE FROM clientes WHERE id = ?")->execute([$_GET['delete_cliente']]);
    header("Location: ?"); exit;
}

// --- Consultas ---
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome ASC")->fetchAll();
$cliente_ativo = null;
$detalhes = null;
$docs_ativo = [];

if (isset($_GET['cliente_id'])) {
    $id_selecionado = $_GET['cliente_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id_selecionado]);
    $cliente_ativo = $stmt->fetch();

    // Busca Detalhes
    $stmt = $pdo->prepare("SELECT * FROM processo_detalhes WHERE cliente_id = ?");
    $stmt->execute([$id_selecionado]);
    $detalhes = $stmt->fetch();
    if(!$detalhes) $detalhes = []; 

    // Documentos
    $stmt = $pdo->prepare("SELECT * FROM documentos WHERE cliente_id = ? ORDER BY id DESC");
    $stmt->execute([$id_selecionado]);
    $docs_ativo = $stmt->fetchAll();
}

// Controle de Aba Ativa
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'requerente';

// Fases Padrão
$fases_padrao = [
    "Abertura de Processo (Guichê)",
    "Fiscalização (Parecer Fiscal)",
    "Triagem (Documentos Necessários)",
    "Comunicado de Pendências (Triagem)",
    "Análise Técnica (Engenharia)",
    "Comunicado (Pendências e Taxas)",
    "Confecção de Documentos",
    "Avaliação (ITBI/Averbação)",
    "Processo Finalizado (Documentos Prontos)"
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Gestão | Vilela Engenharia</title>
    <!-- Fontes e CSS mantidos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/logo.png" type="image/png">
    <style>
        /* CSS Admin Base */
        body { background-color: #f4f7f6; display: block; padding: 0; }
        .admin-header { background: var(--color-primary-strong); color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top:0; z-index: 100; }
        .admin-container { display: grid; grid-template-columns: 260px 1fr; gap: 20px; max-width: 1600px; margin: 20px auto; padding: 0 20px; align-items: start; }
        
        .sidebar { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px; position: sticky; top: 80px; }
        .client-list { list-style: none; padding: 0; margin: 0; max-height: 70vh; overflow-y: auto; }
        .client-list li a { display: block; padding: 10px; border-radius: 6px; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .client-list li a:hover { background: #e6f2ee; }
        .client-list li a.active { background: var(--color-primary); color: white; border-color: transparent; }

        /* Abas */
        .tabs-header { display: flex; gap: 5px; margin-bottom: 0; border-bottom: 1px solid #ddd; overflow-x: auto; white-space: nowrap; padding-bottom: 5px; }
        .tab-btn { padding: 10px 20px; background: #e0e0e0; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; color: #555; text-decoration: none; display: inline-block; }
        .tab-btn.active { background: white; color: var(--color-primary-strong); border: 1px solid #ddd; border-bottom: 1px solid white; margin-bottom: -1px; }

        .tab-content { background: white; padding: 30px; border: 1px solid #ddd; border-radius: 0 8px 8px 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); min-height: 500px; }
        
        /* Formulários */
        .form-section-title { font-size: 1.1rem; color: var(--color-primary); border-bottom: 2px solid #eee; padding-bottom: 5px; margin: 20px 0 15px 0; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: bold; color: #666; margin-bottom: 4px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; } /* box-sizing fix */
        
        .btn-save { background: var(--color-primary); color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; margin-top: 20px; width: 100%; }
        .btn-save:hover { background: var(--color-primary-strong); }

        /* Stepper Admin Style */
        .stepper-list { list-style: none; padding: 0; margin-bottom: 30px; border: 1px solid #eee; border-radius: 8px; overflow: hidden; }
        .stepper-item { display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; background: #fff; }
        .stepper-item:last-child { border-bottom: none; }
        .stepper-item.current { background: #e8f5e9; border-left: 4px solid var(--color-primary); }
        .stepper-btn { background: #ddd; color: #555; border: none; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; transition: 0.2s; }
        .stepper-btn:hover { background: #ccc; }
        .stepper-btn.active { background: var(--color-primary); color: white; cursor: default; }

        /* Responsividade Mobile */
        @media (max-width: 768px) {
            .admin-container { grid-template-columns: 1fr; display: block; }
            .sidebar { position: static; margin-bottom: 20px; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; }
            .admin-header { padding: 1rem; flex-direction: column; gap: 10px; align-items: flex-start; }
            .admin-header a { align-self: flex-end; }
            .form-grid { grid-template-columns: 1fr; }
            .tab-content { padding: 15px; }
            .tabs-header { padding-bottom: 10px; }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="../assets/logo.png" alt="Logo" style="height: 40px;">
        <div>
            <h1 style="margin:0; font-size:1.2rem;">Sistema de Regularização</h1>
            <span style="font-size:0.8rem; opacity: 0.8;">Gestão Administrativa</span>
        </div>
    </div>
    <a href="?sair=true" style="color: white; text-decoration: underline;">Sair</a>
</header>

<div class="admin-container">
    <aside class="sidebar">
        <a href="?novo=true" style="display:block; text-align:center; background:#efb524; padding:10px; border-radius:6px; color:black; font-weight:bold; text-decoration:none; margin-bottom:15px;">+ Novo Cliente</a>
        <h4 style="margin: 10px 0; font-size: 0.9rem; color: #888; text-transform: uppercase;">Meus Clientes</h4>
        <ul class="client-list">
            <?php foreach($clientes as $c): ?>
                <li>
                    <a href="?cliente_id=<?= $c['id'] ?>" class="<?= ($cliente_ativo && $cliente_ativo['id'] == $c['id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['nome']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main>
        <?php if(isset($sucesso)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:6px;"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if(isset($erro)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; margin-bottom:20px; border-radius:6px;"><?= $erro ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['novo'])): ?>
            <div class="tab-content" style="border-radius: 8px;">
                <h2>Cadastrar Novo Cliente</h2>
                <form method="POST">
                    <div class="form-group"><label>Nome</label><input type="text" name="nome" required></div>
                    <div class="form-group"><label>Usuário (CPF/Email)</label><input type="text" name="usuario" required></div>
                    <div class="form-group"><label>Senha Inicial</label><input type="text" name="senha" required></div>
                    <button type="submit" name="novo_cliente" class="btn-save">Cadastrar</button>
                </form>
            </div>

        <?php elseif($cliente_ativo): ?>
            
            <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h1 style="margin: 0; color: #333; font-size: 1.5rem;"><?= htmlspecialchars($cliente_ativo['nome']) ?></h1>
                <a href="?delete_cliente=<?= $cliente_ativo['id'] ?>" onclick="return confirm('ATENÇÃO: Confirmar exclusão?')" style="color:red; font-size:0.8rem; background: #ffebeb; padding: 5px 10px; border-radius: 4px;">Excluir Cliente</a>
            </div>

            <!-- Navegação de Abas -->
            <div class="tabs-header">
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=requerente" class="tab-btn <?= $active_tab=='requerente'?'active':'' ?>">🧑 Requerente</a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=imovel" class="tab-btn <?= $active_tab=='imovel'?'active':'' ?>">🏠 Lote</a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=engenharia" class="tab-btn <?= $active_tab=='engenharia'?'active':'' ?>">📐 Eng.</a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=financeiro" class="tab-btn <?= $active_tab=='financeiro'?'active':'' ?>">💰 Finan.</a>
                <a href="?cliente_id=<?= $cliente_ativo['id'] ?>&tab=timeline" class="tab-btn <?= $active_tab=='timeline'?'active':'' ?>">🚦 Status</a>
            </div>

            <div class="tab-content">
                
                <?php if($active_tab == 'timeline'): ?>
                    <!-- ABA 5: TIMELINE & STEPPER -->
                    <h3>Status Atual do Processo</h3>
                    <p style="color:#666; margin-bottom:20px;">Marque em qual etapa o processo se encontra atualmente. Isso atualizará a barra de progresso do cliente.</p>

                    <ul class="stepper-list">
                        <?php 
                        $etapa_atual_db = $detalhes['etapa_atual'] ?? '';
                        foreach($fases_padrao as $fase): 
                            $is_current = ($etapa_atual_db === $fase);
                        ?>
                            <li class="stepper-item <?= $is_current ? 'current' : '' ?>">
                                <span style="font-weight: 500; font-size: 1rem;"><?= $fase ?></span>
                                <?php if($is_current): ?>
                                    <button class="stepper-btn active">✅ Atual</button>
                                <?php else: ?>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="cliente_id" value="<?= $cliente_ativo['id'] ?>">
                                        <input type="hidden" name="nova_etapa" value="<?= $fase ?>">
                                        <button type="submit" name="atualizar_etapa" class="stepper-btn">Definir como Atual</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h4 style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">Adicionar Nota ou Movimento Extra</h4>
                    <form method="POST" style="background:#fafafa; padding:20px; border:1px solid #eee; border-radius:6px; margin-bottom:20px;">
                        <input type="hidden" name="cliente_id" value="<?= $cliente_ativo['id'] ?>">
                        <div class="form-grid">
                            <div class="form-group"><label>Título</label><input type="text" name="titulo" required></div>
                            <div class="form-group"><label>Data</label><input type="datetime-local" name="data" value="<?= date('Y-m-d\TH:i') ?>"></div>
                            <div class="form-group"><label>Status</label>
                                <select name="status_tipo">
                                    <option value="tramite">Trâmite (Azul)</option>
                                    <option value="inicio">Início (Roxo)</option>
                                    <option value="pendencia">Pendência (Amarelo)</option>
                                    <option value="conclusao">Conclusão (Verde)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:10px;"><label>Nota / Descrição</label><input type="text" name="descricao"></div>
                        <button type="submit" name="novo_movimento" class="btn-save" style="margin-top:10px;">Registrar Nota</button>
                    </form>

                    <h4>Histórico Completo</h4>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; font-size:0.9rem; border-collapse:collapse; min-width: 600px;">
                            <thead style="background:#f0f0f0;"><tr><th>Data</th><th>Fase</th><th>Detalhes</th><th>Ação</th></tr></thead>
                            <tbody>
                                <?php 
                                $movs = $pdo->prepare("SELECT * FROM processo_movimentos WHERE cliente_id = ? ORDER BY data_movimento DESC");
                                $movs->execute([$cliente_ativo['id']]);
                                foreach($movs->fetchAll() as $m): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:10px;"><?= date('d/m/y H:i', strtotime($m['data_movimento'])) ?></td>
                                    <td style="padding:10px;"><strong><?= htmlspecialchars($m['titulo_fase']) ?></strong></td>
                                    <td style="padding:10px;"><?= htmlspecialchars($m['descricao']) ?></td>
                                    <td style="padding:10px;"><a href="?cid=<?= $cliente_ativo['id'] ?>&del_mov=<?= $m['id'] ?>" style="color:red;" onclick="return confirm('Apagar?')">x</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- FORMS UNIFICADOS (Resto mantido sem alterações lógicas profundas, só o form wrapper) -->
                    <form method="POST">
                        <input type="hidden" name="cliente_id" value="<?= $cliente_ativo['id'] ?>">
                        <input type="hidden" name="active_tab_source" value="<?= $active_tab ?>">

                        <?php if($active_tab == 'requerente'): ?>
                            <h3>📂 Dados do Requerente</h3>
                            <div style="background: #e3f2fd; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #bbdefb;">
                                <label style="font-weight:bold; display:block; margin-bottom:5px; color:#0d47a1;">📂 Link da Pasta no Google Drive</label>
                                <input type="url" name="link_drive_pasta" value="<?= $detalhes['link_drive_pasta']??'' ?>" style="width:100%; padding:10px; border:1px solid #90caf9; border-radius:4px;">
                            </div>
                            <!-- Campos do Requerente (resumido p/ brevidade do diff, mas mantendo funcionalidade) -->
                            <div class="form-grid">
                                <div class="form-group"><label>Tipo</label><select name="tipo_pessoa"><option value="Fisica">PF</option><option value="Juridica">PJ</option></select></div>
                                <div class="form-group"><label>CPF/CNPJ</label><input type="text" name="cpf_cnpj" value="<?= $detalhes['cpf_cnpj']??'' ?>"></div>
                                <div class="form-group"><label>RG/IE</label><input type="text" name="rg_ie" value="<?= $detalhes['rg_ie']??'' ?>"></div>
                                <div class="form-group"><label>Civil</label><input type="text" name="estado_civil" value="<?= $detalhes['estado_civil']??'' ?>"></div>
                            </div>
                            <div class="form-group"><label>Profissão</label><input type="text" name="profissao" value="<?= $detalhes['profissao']??'' ?>"></div>
                            <div class="form-group"><label>Endereço</label><input type="text" name="endereco_residencial" value="<?= $detalhes['endereco_residencial']??'' ?>"></div>
                            <div class="form-grid"><div class="form-group"><label>Email</label><input type="text" name="contato_email" value="<?= $detalhes['contato_email']??'' ?>"></div><div class="form-group"><label>Tel</label><input type="text" name="contato_tel" value="<?= $detalhes['contato_tel']??'' ?>"></div></div>

                        <?php elseif($active_tab == 'imovel'): ?>
                            <!-- Imovel Layout -->
                            <h3>🏠 Lote e Imóvel</h3>
                             <div class="form-grid">
                                <div class="form-group"><label>Inscrição</label><input type="text" name="inscricao_imob" value="<?= $detalhes['inscricao_imob']??'' ?>"></div>
                                <div class="form-group"><label>Matrícula</label><input type="text" name="num_matricula" value="<?= $detalhes['num_matricula']??'' ?>"></div>
                                <div class="form-group"><label>Zoneamento</label><input type="text" name="zoneamento" value="<?= $detalhes['zoneamento']??'' ?>"></div>
                            </div>
                            <div class="form-group"><label>Endereço Imóvel</label><input type="text" name="endereco_imovel" value="<?= $detalhes['endereco_imovel']??'' ?>"></div>
                            <div class="form-grid">
                                <div class="form-group"><label>Área Terreno</label><input type="text" name="area_terreno" value="<?= $detalhes['area_terreno']??'' ?>"></div>
                                <div class="form-group"><label>Área Constr.</label><input type="text" name="area_construida" value="<?= $detalhes['area_construida']??'' ?>"></div>
                            </div>

                        <?php elseif($active_tab == 'engenharia'): ?>
                             <h3>📐 Engenharia</h3>
                             <div class="form-grid">
                                 <div class="form-group"><label>Responsável</label><input type="text" name="resp_tecnico" value="<?= $detalhes['resp_tecnico']??'' ?>"></div>
                                 <div class="form-group"><label>CREA/CAU</label><input type="text" name="registro_prof" value="<?= $detalhes['registro_prof']??'' ?>"></div>
                                 <div class="form-group"><label>ART/RRT</label><input type="text" name="num_art_rrt" value="<?= $detalhes['num_art_rrt']??'' ?>"></div>
                             </div>

                        <?php elseif($active_tab == 'financeiro'): ?>
                             <h3>💰 Financeiro</h3>
                             <div class="form-group"><label><input type="checkbox" name="status_taxa_aprovacao" value="1" <?= ($detalhes['status_taxa_aprovacao']??0)?'checked':'' ?>> Taxa Aprovação</label></div>
                             <div class="form-group"><label><input type="checkbox" name="status_issqn" value="1" <?= ($detalhes['status_issqn']??0)?'checked':'' ?>> ISSQN</label></div>
                             <div class="form-group"><label><input type="checkbox" name="status_multas" value="1" <?= ($detalhes['status_multas']??0)?'checked':'' ?>> Multas</label></div>
                        <?php endif; ?>

                        <div style="margin-top:20px;"><button type="submit" name="salvar_detalhes" class="btn-save">Salvar Alterações</button></div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Docs -->
            <div class="card" style="margin-top: 30px;">
                <h3>Anexos (Drive)</h3>
                <form method="POST" style="background:#f8f9fa; padding:15px; border-radius:6px; display:flex; flex-wrap:wrap; gap:10px;">
                    <input type="hidden" name="cliente_id" value="<?= $cliente_ativo['id'] ?>">
                    <input type="text" name="titulo" placeholder="Nome do Arquivo" required style="flex:1; min-width:200px; padding:8px;">
                    <input type="url" name="link" placeholder="Link Drive" required style="flex:2; min-width:200px; padding:8px;">
                    <button type="submit" name="novo_doc" style="background:var(--color-primary); color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Anexar</button>
                </form>
                <div style="margin-top:15px;">
                    <?php foreach($docs_ativo as $d): ?>
                        <div style="padding:10px; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span>📄 <?= htmlspecialchars($d['titulo']) ?></span>
                            <a href="<?= htmlspecialchars($d['link_drive']) ?>" target="_blank">Abrir</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>
    </main>
</div>
</body>
</html>
