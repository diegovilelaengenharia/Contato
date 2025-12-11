import { profile, heroActions, links, footer } from '../../data.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Preencher Header
    renderHeader();

    // 2. Renderizar Hero Actions
    renderHeroActions();

    // 3. Renderizar Serviços
    renderLinks();

    // 4. Renderizar Footer (Novo)
    renderFooter();

    // 5. Inicializar Animações
    initializeAnimations();

    // 6. Inicializar Status
    initBusinessStatus();

    // 7. Inicializar Dark Mode
    initDarkMode();

    // 8. Inicializar SEO
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

    container.innerHTML = '';

    const createButton = (action, type) => {
        if (!action) return;
        const btn = document.createElement(type === 'button' ? 'button' : 'a');
        btn.className = `hero-btn ${action.className}`;

        if (type === 'link') {
            btn.href = action.href;
            btn.target = '_blank';
            btn.rel = 'noopener';
        } else {
            btn.type = 'button';
        }

        if (action.label) {
            btn.innerHTML = `${action.icon} <span>${action.label}</span>`;
            btn.setAttribute('aria-label', action.label);
        } else {
            btn.innerHTML = action.icon;
            // If no label, ensure flex centering in CSS works for single item
        }

        if (action.label === "") {
            // Treat as icon-only visual style override if needed, 
            // or just rely on the class. 
            // For buttons that were 'primary/secondary' but now have no text:
            btn.style.padding = "12px";
            btn.style.width = "48px";
            btn.style.height = "48px";
            btn.style.borderRadius = "50%";
            btn.style.flex = "0 0 48px";
        }

        container.appendChild(btn);
        return btn;
    };

    createButton(heroActions.main, 'link');
    createButton(heroActions.secondary, 'link');

    // Share button logic
    if (heroActions.share) {
        const btn = createButton(heroActions.share, 'button');
        if (btn) {
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
                        alert('Link copiado!');
                    } catch (err) {
                        console.error('Copy failed');
                    }
                }
            });
        }
    }

    // QR Code logic removed
}

function renderLinks() {
    const container = document.querySelector('.content');
    if (!container) return;

    // Check if wrapping section exists
    let section = container.querySelector('.links-section');
    if (!section) {
        container.innerHTML = '';
        section = document.createElement('section');
        section.className = 'links-section';
        container.appendChild(section);
    } else {
        section.innerHTML = '';
    }

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
}

function renderFooter() {
    const footerEl = document.querySelector('.footer');
    if (!footerEl) return;

    let html = '';

    // Contacts
    if (footer.contacts) {
        html += '<div class="footer-contacts">';
        footer.contacts.forEach(c => {
            html += `<a href="${c.href}" target="_blank" rel="noopener">${c.value}</a>`;
        });
        html += '</div>';
    }

    // Quotation
    if (footer.quotation) {
        html += `
            <blockquote class="footer-quote">
                <p>${footer.quotation.text}</p>
                <cite>${footer.quotation.reference}</cite>
            </blockquote>
        `;
    }

    // Copy
    html += `<div class="footer-copy"><small>${footer.copy}</small></div>`;

    footerEl.innerHTML = html;

    // Re-update year if dynamic
    const yearSpan = document.getElementById('year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();
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

function openQRModal() {
    const existingModal = document.getElementById('qr-modal');
    if (existingModal) {
        existingModal.classList.add('is-open');
        document.body.classList.add('modal-open');
        return;
    }

    const modal = document.createElement('div');
    modal.id = 'qr-modal';
    modal.className = 'modal is-open';
    modal.innerHTML = `
        <div class="modal__overlay" data-close></div>
        <div class="modal__content qr-modal-content">
            <button class="modal__close" data-close>&times;</button>
            <h3>Escaneie para salvar</h3>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(window.location.href)}" alt="QR Code do site" width="250" height="250" />
            <p>Aponte a câmera do seu celular</p>
        </div>
    `;

    document.body.appendChild(modal);
    document.body.classList.add('modal-open');

    modal.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-close')) {
            modal.classList.remove('is-open');
            document.body.classList.remove('modal-open');
            setTimeout(() => modal.remove(), 300);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            modal.classList.remove('is-open');
            document.body.classList.remove('modal-open');
            setTimeout(() => modal.remove(), 300);
        }
    });
}

function initSEO() {
    const schema = {
        "@context": "https://schema.org",
        "@type": "EngineeringService",
        "name": profile.title,
        "image": new URL(profile.logo, window.location.href).href,
        "description": `${profile.title} - ${profile.role}`,
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

    const existing = document.getElementById('schema-json');
    if (existing) existing.remove();

    const script = document.createElement('script');
    script.id = 'schema-json';
    script.type = 'application/ld+json';
    script.text = JSON.stringify(schema);
    document.head.appendChild(script);
}
