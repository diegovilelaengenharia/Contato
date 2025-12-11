import { profile, heroActions, links } from '../../data.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Preencher Header
    renderHeader();

    // 2. Renderizar Hero Actions (WhatsApp, VCard, Share)
    renderHeroActions();

    // 3. Renderizar Links (Agrupados)
    renderLinks();

    // 4. Inicializar Animações de Entrada
    initializeAnimations();

    // 5. Inicializar Status de Negócio (Aberto/Fechado)
    initBusinessStatus();

    // 6. Inicializar Dark Mode
    initDarkMode();
});

function renderHeader() {
    const badgeEl = document.querySelector('.badge');
    const titleEl = document.querySelector('.heading h1');
    const nameEl = document.querySelector('.hero-name');
    const roleEl = document.querySelector('.hero-role');
    const imgEl = document.querySelector('.logo img');
    const clientBtn = document.querySelector('.client-area-button');

    if (badgeEl && profile.badge) badgeEl.textContent = profile.badge;
    if (titleEl) titleEl.textContent = profile.title || "Vilela Engenharia";
    if (nameEl) nameEl.textContent = profile.name;
    if (roleEl) roleEl.innerHTML = profile.role;
    if (imgEl && profile.logo) imgEl.src = profile.logo;
    if (clientBtn && profile.clientAreaLink) clientBtn.href = profile.clientAreaLink;
}

function renderHeroActions() {
    const container = document.querySelector('.hero-actions');
    if (!container) return;

    container.innerHTML = heroActions.map(action => {
        // Botão de compartilhar
        if (action.type === 'action' && action.id === 'share') {
            return `
            <button class="${action.className}" type="button" data-share-button aria-label="${action.label}">
               ${action.label}
            </button>
            `;
        }

        const downloadAttr = action.download ? 'download' : '';
        const targetAttr = action.target !== false ? 'target="_blank" rel="noopener"' : '';

        return `
        <a class="${action.className}" href="${action.href}" 
           ${downloadAttr} 
           ${targetAttr}>
           ${action.label}
        </a>
        `;
    }).join('');

    // Attach Listeners
    const shareBtn = container.querySelector('[data-share-button]');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: profile.title,
                        text: `${profile.title} - ${profile.role} (Via WhatsApp)`,
                        url: window.location.href
                    });
                } catch (err) {
                    console.error('Share failed:', err);
                }
            } else {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    const originalText = shareBtn.textContent;
                    shareBtn.textContent = "Link copiado!";
                    setTimeout(() => shareBtn.textContent = originalText, 2000);
                } catch (err) {
                    alert('Copie o link: ' + window.location.href);
                }
            }
        });
    }
}

function renderLinks() {
    const container = document.querySelector('.content');
    if (!container) return;

    // Limpar container mas manter o titulo se houver (na arquitetura atual o 'links-grid' era filho direto de content? Não, era dentro de content.)
    // Vamos recriar a estrutura dentro de .content para suportar grupos.
    container.innerHTML = '';

    // O objeto links agora tem chaves: contact, services, social
    // Vamos iterar sobre as chaves na ordem desejada
    const groups = ['contact', 'social', 'services']; // Ordem requisitada: Contato, Redes, Serviços? O user pediu 3 grupos (contato, redes sociais, serviços)
    // Mas no prompt: "Gostaria de ter 3 grupos (contato, redes sociais, serviços)"
    // Vamos seguir essa ordem.

    let globalIndex = 0; // Para stagger animation continua

    groups.forEach(groupKey => {
        const group = links[groupKey];
        if (!group) return;

        const groupSection = document.createElement('section');
        groupSection.className = 'links-group';

        const groupTitle = document.createElement('h2');
        groupTitle.className = 'links-group-title';
        groupTitle.textContent = group.title;
        groupSection.appendChild(groupTitle);

        const list = document.createElement('ul');
        list.className = 'links-grid';

        group.items.forEach((link) => {
            const li = document.createElement('li');
            const delay = (globalIndex + 1) * 0.1;
            li.style.setProperty('--animation-delay', `${delay}s`);
            globalIndex++;

            const cardElement = document.createElement('a');
            cardElement.className = `link-card ${link.className || ''}`;
            cardElement.href = link.href;
            cardElement.target = '_blank';
            cardElement.rel = 'noopener';
            cardElement.setAttribute('aria-label', link.title);

            if (link.href.startsWith('tel:') || link.href.startsWith('mailto:')) {
                cardElement.removeAttribute('target');
                cardElement.removeAttribute('rel');
            }

            cardElement.innerHTML = `
                <span class="icon" aria-hidden="true">
                    ${link.icon}
                </span>
                <span class="text">
                    <span class="title">${link.title}</span>
                    <span class="subtitle">${link.subtitle}</span>
                </span>
            `;

            li.appendChild(cardElement);
            list.appendChild(li);
        });

        groupSection.appendChild(list);
        container.appendChild(groupSection);
    });
}

function initializeAnimations() {
    requestAnimationFrame(() => {
        document.body.classList.add('animations-ready');
    });
}

function initBusinessStatus() {
    const now = new Date();
    const day = now.getDay();
    const hour = now.getHours();
    const isOpen = (day >= 1 && day <= 5) && (hour >= 8 && hour < 18);

    const statusText = isOpen ? "Aberto agora" : "Fechado agora";
    const statusClass = isOpen ? "status-open" : "status-closed";

    const badgeEl = document.querySelector('.badge');
    if (badgeEl && badgeEl.parentNode) {
        // Evitar duplicar se já existir (em hot reload ou re-render)
        const existing = badgeEl.parentNode.querySelector('.status-badge');
        if (existing) existing.remove();

        const statusBadge = document.createElement('span');
        statusBadge.className = `status-badge ${statusClass}`;
        statusBadge.textContent = statusText;
        badgeEl.parentNode.insertBefore(statusBadge, badgeEl.nextSibling);
    }
}

function initDarkMode() {
    // Evitar criar botão duplicado
    if (document.querySelector('.theme-toggle')) return;

    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'theme-toggle';
    toggleBtn.setAttribute('aria-label', 'Alternar tema escuro');
    toggleBtn.type = 'button';
    toggleBtn.innerHTML = `
        <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
    `;

    document.body.appendChild(toggleBtn);

    const savedTheme = localStorage.getItem('theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
        document.body.classList.add('dark-mode');
    }

    toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
}
