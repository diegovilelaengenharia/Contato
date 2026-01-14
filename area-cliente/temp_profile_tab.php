                    <?php if ($active_tab == 'perfil'): ?>
                        <div class="admin-tab-content" style="background:transparent; padding:0; box-shadow:none; border:none;">

                            <div style="display:grid; grid-template-columns: 350px 1fr; gap:30px; align-items:start;">

                                <!-- COLUNA 1: CARD DO CLIENTE (Sticky) -->
                                <div style="background:#fff; border-radius:20px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.05); text-align:center; position:sticky; top:20px;">

                                    <!-- Avatar Large -->
                                    <div style="position:relative; width:120px; height:120px; margin:0 auto 20px auto;">
                                        <img src="<?= $avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($cliente_ativo['nome']) . '&background=random&size=128' ?>"
                                            style="width:100%; height:100%; border-radius:50%; object-fit:cover; border:4px solid #f8f9fa; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
                                        <button onclick="document.getElementById('avatar_input').click()" style="position:absolute; bottom:0; right:0; background:#198754; color:white; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center;" title="Alterar Foto">
                                            <span class="material-symbols-rounded" style="font-size:1.2rem;">photo_camera</span>
                                        </button>
                                        <form method="POST" enctype="multipart/form-data" id="form_avatar">
                                            <input type="file" name="avatar_upload" id="avatar_input" style="display:none;" onchange="document.getElementById('form_avatar').submit()">
                                        </form>
                                    </div>

                                    <h2 style="margin:0 0 5px 0; color:#2c3e50; font-size:1.4rem; font-weight:700;"><?= htmlspecialchars($cliente_ativo['nome']) ?></h2>
                                    <p style="margin:0 0 15px 0; color:#6c757d; font-size:0.9rem; font-weight:500;">Cliente desde <?= date('Y', strtotime($cliente_ativo['created_at'])) ?></p>

                                    <!-- Status Badge -->
                                    <div style="display:inline-block; padding:6px 15px; background:#e8f5e9; color:#198754; border-radius:20px; font-size:0.85rem; font-weight:600; margin-bottom:25px;">
                                        <?= strtoupper($detalhes['etapa_atual'] ?? 'CADASTRO') ?>
                                    </div>

                                    <!-- Actions List -->
                                    <div style="text-align:left; display:flex; flex-direction:column; gap:10px;">
                                        <a href="gerenciar_cliente.php?id=<?= $cliente_ativo['id'] ?>" class="btn-action-profile" style="background:#f8f9fa; color:#333;">
                                            <span class="material-symbols-rounded">edit_square</span> Editar Dados Completos
                                        </a>
                                        <a href="relatorio_cliente.php?id=<?= $cliente_ativo['id'] ?>" target="_blank" class="btn-action-profile" style="background:#e3f2fd; color:#0d6efd;">
                                            <span class="material-symbols-rounded">picture_as_pdf</span> Gerar Relatório PDF
                                        </a>
                                        <a href="?delete_cliente=<?= $cliente_ativo['id'] ?>" onclick="return confirm('Tem certeza? Isso apagará tudo!')" class="btn-action-profile" style="background:#fff5f5; color:#dc3545; border:1px solid #f5c2c7;">
                                            <span class="material-symbols-rounded">delete</span> Excluir Cliente
                                        </a>
                                    </div>
                                </div>

                                <!-- COLUNA 2: RESUMO E DASHBOARD -->
                                <div style="display:flex; flex-direction:column; gap:25px;">

                                    <!-- Bloco 1: Informações Pessoais e Contato -->
                                    <div style="background:#fff; border-radius:15px; padding:25px; box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                                        <h3 style="margin:0 0 20px 0; font-size:1.1rem; color:#2c3e50; border-bottom:1px solid #eee; padding-bottom:10px;">📋 Dados Cadastrais</h3>

                                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:20px;">
                                            <div>
                                                <label style="font-size:0.75rem; color:#999; text-transform:uppercase; font-weight:700;">CPF / CNPJ</label>
                                                <div style="font-size:1rem; color:#333; font-weight:600;"><?= $detalhes['cpf_cnpj'] ?? '--' ?></div>
                                            </div>
                                            <div>
                                                <label style="font-size:0.75rem; color:#999; text-transform:uppercase; font-weight:700;">E-mail</label>
                                                <div style="font-size:1rem; color:#333; font-weight:600;"><?= $detalhes['email'] ?? '--' ?></div>
                                            </div>
                                            <div>
                                                <label style="font-size:0.75rem; color:#999; text-transform:uppercase; font-weight:700;">Telefone</label>
                                                <div style="font-size:1rem; color:#333; font-weight:600;"><?= $detalhes['contato_tel'] ?? '--' ?></div>
                                            </div>
                                            <div>
                                                <label style="font-size:0.75rem; color:#999; text-transform:uppercase; font-weight:700;">Local da Obra</label>
                                                <div style="font-size:1rem; color:#333; font-weight:600;"><?= $detalhes['endereco_obra'] ?? '--' ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bloco 2: Métricas (Financeiro, Pendencias, Timeline) -->
                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

                                        <!-- Financeiro -->
                                        <div style="background:#fff; border-radius:15px; padding:20px; box-shadow:0 5px 20px rgba(0,0,0,0.03); border-left:5px solid #198754;">
                                            <h4 style="margin:0 0 15px 0; font-size:0.9rem; color:#555;">💰 Resumo Financeiro</h4>
                                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                                <span style="color:#777; font-size:0.9rem;">Pago:</span>
                                                <span style="font-weight:700; color:#198754;">R$ <?= number_format($finSnapshot['total_pago'] ?? 0, 2, ',', '.') ?></span>
                                            </div>
                                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                                <span style="color:#777; font-size:0.9rem;">Em Aberto:</span>
                                                <span style="font-weight:700; color:#ffc107;">R$ <?= number_format($finSnapshot['total_aberto'] ?? 0, 2, ',', '.') ?></span>
                                            </div>
                                            <?php if (($finSnapshot['total_atrasado'] ?? 0) > 0): ?>
                                                <div style="display:flex; justify-content:space-between; margin-top:5px; padding-top:5px; border-top:1px dashed #ddd;">
                                                    <span style="color:#dc3545; font-size:0.9rem; font-weight:700;">Em Atraso:</span>
                                                    <span style="font-weight:700; color:#dc3545;">R$ <?= number_format($finSnapshot['total_atrasado'] ?? 0, 2, ',', '.') ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Pendências e Timeline -->
                                        <div style="display:flex; flex-direction:column; gap:20px;">

                                            <!-- Pendências -->
                                            <div style="background:#fff; border-radius:15px; padding:15px; box-shadow:0 5px 20px rgba(0,0,0,0.03); display:flex; align-items:center; gap:15px;">
                                                <div style="background:<?= (($penSnapshot['qtd'] ?? 0) > 0) ? '#ffebee' : '#e8f5e9' ?>; color:<?= (($penSnapshot['qtd'] ?? 0) > 0) ? '#c62828' : '#1b5e20' ?>; width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.2rem;">
                                                    <?= $penSnapshot['qtd'] ?? 0 ?>
                                                </div>
                                                <div>
                                                    <div style="font-size:0.9rem; font-weight:700; color:#333;">Pendências Ativas</div>
                                                    <div style="font-size:0.8rem; color:#777;"><?= (($penSnapshot['qtd'] ?? 0) > 0) ? 'O cliente precisa resolver' : 'Tudo em dia!' ?></div>
                                                </div>
                                            </div>

                                            <!-- Última Movimentação -->
                                            <div style="background:#fff; border-radius:15px; padding:15px; box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                                                <label style="font-size:0.75rem; color:#999; text-transform:uppercase; font-weight:700; display:block; margin-bottom:5px;">🚀 Última Atualização</label>
                                                <?php if ($lastMov): ?>
                                                    <div style="font-weight:600; color:#333; line-height:1.2;"><?= htmlspecialchars($lastMov['titulo']) ?></div>
                                                    <div style="font-size:0.8rem; color:#198754; margin-top:3px;"><?= date('d/m/Y', strtotime($lastMov['data_movimento'])) ?></div>
                                                <?php else: ?>
                                                    <div style="color:#aaa; font-style:italic; font-size:0.9rem;">Nenhum registro recente.</div>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- CSS for Profile Layout -->
                            <style>
                                .btn-action-profile {
                                    display: flex;
                                    align-items: center;
                                    gap: 10px;
                                    padding: 12px 15px;
                                    border-radius: 10px;
                                    text-decoration: none;
                                    font-weight: 600;
                                    font-size: 0.9rem;
                                    transition: all 0.2s;
                                }

                                .btn-action-profile:hover {
                                    transform: translateX(5px);
                                    filter: brightness(0.95);
                                }

                                @media (max-width: 900px) {
                                    div[style*="grid-template-columns: 350px 1fr"] {
                                        grid-template-columns: 1fr !important;
                                    }

                                    div[style*="top:20px"] {
                                        position: static !important;
                                    }
                                }
                            </style>

                        </div>
                    <?php endif; ?>