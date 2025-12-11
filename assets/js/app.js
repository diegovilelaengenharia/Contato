import { profile, heroActions, links } from '../../data.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Preencher Header (Logo, Nome, Bio)
    renderHeader();

    // 2. Renderizar Hero Actions (WhatsApp, Insta, Share)
    renderHeroActions();

    // 3. Renderizar Serviços
    renderLinks();

    // 4. Inicializar Animações de Entrada
    initializeAnimations();

    // 5. Inicializar Status de Negócio (Aberto/Fechado)
    initBusinessStatus();

    // 6. Inicializar Dark Mode
    initDarkMode();

    // 7. Inicializar SEO (Schema.org)
    initSEO();
});

function renderHeader() {
    const badgeEl = document.querySelector('.badge');
    const titleEl = document.querySelector('.heading h1');
    const nameEl = document.querySelector('.hero-name');
    const roleEl = document.querySelector('.hero-role');
    const imgEl = document.querySelector('.logo img');
    const clientBtn = document.querySelector('.client-area-button');
    const headingDiv = document.querySelector('.heading');

    if (badgeEl && profile.badge) badgeEl.textContent = profile.badge;
    if (titleEl) titleEl.textContent = profile.title || "Vilela Engenharia";
    if (nameEl) nameEl.textContent = profile.name;
    if (roleEl) roleEl.innerHTML = profile.role;
    if (imgEl && profile.logo) imgEl.src = profile.logo;
    if (clientBtn && profile.clientAreaLink) clientBtn.href = profile.clientAreaLink;

    // Render BIO
    if (profile.bio && headingDiv) {
        let bioEl = headingDiv.querySelector('.hero-bio');
        if (!bioEl) {
            bioEl = document.createElement('p');
            bioEl.className = 'hero-bio';
            headingDiv.appendChild(bioEl);
        }
        bioEl.textContent = profile.bio;
    }
}

function renderHeroActions() {
    const container = document.querySelector('.hero-actions');
    if (!container) return;

    container.innerHTML = ''; // Clear

    // 1. Phone Button (Main)
    if (heroActions.main) {
        const btn = document.createElement('a');
        btn.className = `hero-btn ${heroActions.main.className}`;
        btn.href = heroActions.main.href;
        btn.target = '_blank';
        btn.rel = 'noopener';
        btn.innerHTML = `${heroActions.main.icon} <span>${heroActions.main.label}</span>`;
        container.appendChild(btn);
    }

    // 2. Instagram Button (Secondary)
    if (heroActions.secondary) {
        const btn = document.createElement('a');
        btn.className = `hero-btn ${heroActions.secondary.className}`;
        btn.href = heroActions.secondary.href;
        btn.target = '_blank';
        btn.rel = 'noopener';
        btn.innerHTML = `${heroActions.secondary.icon} <span>${heroActions.secondary.label}</span>`;
        container.appendChild(btn);
    }

    // 3. Share Button (Icon only)
    if (heroActions.share) {
        const btn = document.createElement('button');
        btn.className = `hero-btn ${heroActions.share.className}`;
        btn.type = 'button';
        btn.setAttribute('aria-label', heroActions.share.label);
        btn.innerHTML = heroActions.share.icon;

        btn.addEventListener('click', async () => {
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: profile.title,
                        text: `${profile.title} - ${profile.role}`,
                        url: window.location.href
                    });
                } catch (err) {
                    console.error('Share failed:', err);
                }
            } else {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    alert('Link copiado para a área de transferência!');
                } catch (err) {
                    alert('Copie o link: ' + window.location.href);
                }
            }
        });

        container.appendChild(btn);
    }
}

function renderLinks() {
    const container = document.querySelector('.content');
    if (!container) return;

    container.innerHTML = '';

    // Renderizar apenas a lista de serviços, sem grupos
    const section = document.createElement('section');
    section.className = 'links-section';

    // Opcional: Titulo "Serviços" se desejar, mas o user pediu "Na parte de baixo, Colocar apenas os serviços"
    // Vamos colocar um titulo discreto ou sem titulo. "Serviços" fica bom para semântica.
    const heading = document.createElement('h2');
    heading.className = 'section-heading';
    heading.textContent = 'Serviços';
    section.appendChild(heading);

    const list = document.createElement('ul');
    list.className = 'links-grid';

    links.forEach((link, index) => {
        const li = document.createElement('li');
        const delay = (index + 1) * 0.1;
        li.style.setProperty('--animation-delay', `${delay}s`);

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

    section.appendChild(list);
    container.appendChild(section);
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
        const existing = badgeEl.parentNode.querySelector('.status-badge');
        if (existing) existing.remove();

        const statusBadge = document.createElement('span');
        statusBadge.className = `status-badge ${statusClass}`;
        statusBadge.textContent = statusText;
        badgeEl.parentNode.insertBefore(statusBadge, badgeEl.nextSibling);
    }
}

function initDarkMode() {
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

function initSEO() {
    // Schema.org Structured Data
    const schema = {
        "@context": "https://schema.org",
        "@type": "EngineeringService",
        "name": profile.title,
        "image": new URL(profile.logo, window.location.href).href,
        "description": profile.bio || `${profile.title} - ${profile.role}`,
        "telephone": "+5535984529577",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Pouso Alegre",
            "addressRegion": "MG",
            "addressCountry": "BR"
        },
        "url": window.location.href,
        "sameAs": [
            "https://www.instagram.com/diegovilela.eng/"
        ]
    };

    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.text = JSON.stringify(schema);
    document.head.appendChild(script);
}
