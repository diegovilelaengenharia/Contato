# Roadmap: Modernização Vilela Engenharia

Este guia define o caminho para transformar o site estático atual em uma aplicação moderna, dinâmica e de fácil manutenção, sem complicar o ambiente de desenvolvimento.

## 🎯 Objetivos
1.  **Facilidade de Manutenção**: Adicionar/Remover links e contatos sem mexer no HTML.
2.  **Identidade Visual Premium**: Aplicar a nova paleta de cores e animações sutis.
3.  **Dinamismo**: O site deve parecer "vivo" e interativo.

---

## 🛣️ O Caminho (Etapas)

### Fase 1: Arquitetura Orientada a Dados (Data-Driven UI)
Atualmente, cada link é um bloco de código HTML. Se quiser mudar o telefone, precisa caçar a linha no meio das tags.
**A Solução**: Separar o "Conteúdo" da "Estrutura".

1.  **Criar `data.js`**: Um arquivo central contendo todas as informações (Nome, Telefone, Links, Redes Sociais).
2.  **Motor de Renderização JS**: Alterar o `index.html` para deixar a lista vazia (`<ul id="links-list"></ul>`) e usar JavaScript para criar os botões automaticamente com base no `data.js`.
    *   *Benefício*: Para adicionar um novo botão, você apenas adiciona uma linha no arquivo de dados.

### Fase 2: Design Premium & Novas Cores
Aplicação da identidade visual definida no plano de cores.

1.  **Variáveis CSS 2.0**: Atualizar `style.css` com a nova paleta:
    *   Verde Principal: `#197e63`
    *   Amarelo Ouro: `#ffba35` (Destaques)
    *   Chumbo: `#4c4b4b` (Textos)
2.  **Micro-interações**:
    *   Feedback visual ao clicar (efeito ripple ou scale).
    *   Animação de entrada escalonada (os botões aparecem um por um suavemente).
3.  **Fundo Dinâmico**:
    *   Substituir o gradiente estático por um padrão sutil ou gradiente animado (CSS puro, leve).

### Fase 3: Funcionalidades Avançadas (Opcional)
1.  **Saudação Dinâmica**: "Bom dia, Diego" ou "Boa tarde" dependendo do horário do visitante.
2.  **Status em Tempo Real**: Indicador de "Aberto agora" ou "Fechado" baseado no horário comercial.

---

## 🚀 Próximos Passos Sugeridos

Recomendo começarmos pela **Fase 2 (Design & Cores)** pois gera impacto visual imediato, ou pela **Fase 1 (Arquitetura)** se sua prioridade for facilitar a edição futura.

**Qual caminho prefere seguir primeiro?**
1.  🎨 Visual: Aplicar as novas cores e realizar o "tapa" no design agora.
2.  ⚙️ Estrutura: Refatorar o código para separar os dados do HTML.
