# Roadmap: Evolução Vilela Engenharia

Este documento rastrea o progresso e planeja os próximos passos para a landing page.

## ✅ Concluído
- [x] **Fase 1: Arquitetura Orientada a Dados**: Separação total entre conteúdo (`data.js`) e estrutura (HTML/JS).
- [x] **Fase 2: Identidade Visual & Animações**: Nova paleta de cores (Verde/Amarelo Ouro), animações de entrada e reformulação do design.
- [x] **Refatoração**: Limpeza de código, remoção de duplicatas e arquivos legados.

---

## 🚀 Propostas de Melhoria (Fase 3)

Existem várias oportunidades para elevar o nível da aplicação. Abaixo, apresento sugestões organizadas por categoria:

### 1. Funcionalidades de Negócio
-   **Status "Aberto/Fechado"**: Um indicador dinâmico ao lado do horário ou no topo, mostrando se o escritório está aberto agora com base no horário comercial (automático via JS).
-   **Botão Compartilhar**: Usar a API nativa do celular para compartilhar o cartão de visitas (link) diretamente no WhatsApp/Instagram de outras pessoas com um clique.
-   **Gerador de QR Code**: Um botão que exibe um QR Code na tela para que pessoas próximas possam escanear e salvar o contato imediatamente.

### 2. Experiência do Usuário (UX)
-   **Toggle Modo Escuro**: Embora o site já suporte tema escuro do sistema, um botão manual no topo permite que o usuário escolha a preferência dele.
-   **Skeleton Loading**: Enquanto o JavaScript carrega os dados, exibir um "esqueleto" cinza pulsante no lugar dos cards para evitar que a tela "pule" (layout shift).
-   **Carrossel de Serviços**: No modal ou na página principal, apresentar os serviços com ícones visuais em um formato de lista horizontal ou grid mais elaborado.

### 3. SEO & Performance
-   **Dados Estruturados (Schema.org)**: Adicionar código invisível (JSON-LD) para que o Google entenda que este site representa uma `EngineeringService`, melhorando a exibição nos resultados de busca.
-   **Melhorias PWA**: Tornar o site instalável com mais robustez, permitindo acesso básico offline.

---

## 🎯 Recomendação Imediata

Para o maior impacto com menor esforço, sugiro implementar o **Botão Compartilhar** e o **Status Aberto/Fechado**. São funcionalidades que aumentam a utilidade do cartão digital.
