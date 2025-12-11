# Plano de Implementação: Atualização da Identidade Visual

## Objetivo
Atualizar a aplicação para refletir a nova paleta de cores oficial da Vilela Engenharia, garantindo consistência em todos os arquivos e estados de interação.

## Novas Cores
- **Verde Principal**: `#197e63` (Ação, ícones, check)
- **Cinza Escuro (Chumbo)**: `#4c4b4b` (Títulos, textos, telhado)
- **Amarelo Ouro**: `#ffba35` (Detalhes, hover, destaque)
- **Cinza de Apoio**: `#848484` (Subtítulos, divisórias)
- **Off-White (Fundo)**: `#f9fafd` (Background)

## Tarefas

### 1. Metadados e Manifesto
- [x] Atualizar `manifest.json`: `theme_color` e `background_color` para `#197e63`.
- [x] Atualizar `index.html`: `<meta name="theme-color">` para `#197e63`.

### 2. Estilos (style.css)
- [x] Verificar e refinar as variáveis `:root` para garantir os valores exatos.
- [x] Implementar o efeito de hover "Amarelo Ouro" nos botões principais:
    - `.client-area-button`
    - `.budget-form__submit`
    - Botões de ação rápida (`.hero-whatsapp`?)
- [x] Confirmar que gradientes antigos foram removidos ou atualizados para variações sutis das novas cores.
- [x] Verificar cores dos ícones SVG inline se necessário (atualmente usam `currentColor` ou variáveis).

### 3. Validação
- [ ] Verificar visualização das cores no modo claro e escuro.
- [ ] Garantir que o contraste seja acessível.

### 4. Publicação
- [ ] Commit e Push para o GitHub.
