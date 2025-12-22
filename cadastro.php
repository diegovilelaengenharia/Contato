<?php
require 'area-cliente/db.php';

$sucesso = false;
$erro = false;

if (isset($_POST['btn_enviar'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO pre_cadastros (nome, cpf_cnpj, email, telefone, endereco_obra, tipo_servico, mensagem, ip_origem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nome'],
            $_POST['cpf_cnpj'],
            $_POST['email'],
            $_POST['telefone'],
            $_POST['endereco_obra'],
            $_POST['tipo_servico'],
            $_POST['mensagem'],
            $_SERVER['REMOTE_ADDR']
        ]);
        $sucesso = true;
    } catch (PDOException $e) {
        $erro = "Erro ao enviar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Inicial | Vilela Engenharia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <style>
        :root { --color-primary: #146c43; --color-bg: #f8f9fa; }
        body { font-family: 'Outfit', sans-serif; background: var(--color-bg); margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; color: #333; }
        .container { background: white; width: 100%; max-width: 600px; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { height: 60px; margin-bottom: 15px; }
        .header h1 { color: var(--color-primary); margin: 0; font-size: 1.8rem; }
        .header p { color: #666; margin-top: 10px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #444; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s; }
        .form-group input:focus { border-color: var(--color-primary); outline: none; box-shadow: 0 0 0 3px rgba(20,108,67,0.1); }
        
        .btn-submit { background: var(--color-primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #0f5132; transform: translateY(-2px); }
        
        .success-box { text-align: center; padding: 40px 20px; }
        .success-icon { font-size: 4rem; color: var(--color-primary); margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <?php if($sucesso): ?>
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h2 style="color:var(--color-primary);">Cadastro Enviado!</h2>
            <p>Recebemos suas informações com sucesso.</p>
            <p>Em breve, nossa equipe entrará em contato para dar os próximos passos no seu projeto.</p>
            <a href="index.html" style="display:inline-block; margin-top:20px; color:var(--color-primary); text-decoration:none; font-weight:bold;">Voltar ao Início</a>
        </div>
    <?php else: ?>
        <div class="header">
            <img src="assets/logo.png" alt="Vilela Engenharia">
            <h1>Início do seu Sonho</h1>
            <p>Preencha os dados abaixo para iniciarmos o cadastro do seu projeto.</p>
        </div>

        <?php if($erro): ?><div style="background:#f8d7da; color:#842029; padding:15px; border-radius:8px; margin-bottom:20px;"><?= $erro ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required placeholder="Seu nome">
            </div>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>CPF ou CNPJ</label>
                    <input type="text" name="cpf_cnpj" placeholder="000.000.000-00">
                </div>
                <div class="form-group">
                    <label>Telefone / WhatsApp</label>
                    <input type="text" name="telefone" required placeholder="(35) 99999-9999">
                </div>
            </div>

            <div class="form-group">
                <label>Email (Opcional)</label>
                <input type="email" name="email" placeholder="seu@email.com">
            </div>

            <div class="form-group">
                <label>Endereço da Obra / Local do Serviço</label>
                <input type="text" name="endereco_obra" required placeholder="Rua, Bairro, Cidade...">
            </div>

            <div class="form-group">
                <label>Tipo de Serviço Desejado</label>
                <select name="tipo_servico">
                    <option value="Projeto Arquitetônico">Projeto Arquitetônico</option>
                    <option value="Regularização">Regularização de Imóvel</option>
                    <option value="Desmembramento">Desmembramento</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>

            <div class="form-group">
                <label>Mensagem ou Observações (Opcional)</label>
                <textarea name="mensagem" rows="3" placeholder="Se quiser, conte mais detalhes..."></textarea>
            </div>

            <button type="submit" name="btn_enviar" class="btn-submit">Enviar Cadastro</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
