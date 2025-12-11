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

    // 9. Inicializar Animações de Scroll
    initScrollAnimations();

    // 10. Renderizar Floating WhatsApp
    renderFloatingWA();

    // 11. Renderizar FAQ (Desativado a pedido)
    // renderFAQ();

    // 12. Renderizar Regularização CTA (Desativado a pedido)
    // renderRegularizationCTA();

    // 13. Renderizar Dicas (Knowledge) (Desativado a pedido)
    // renderTips();
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
        }

        // Removed icon-only hack

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
        // Removed inline animation-delay in favor of class-based scroll reveal or keep hybrid
        li.className = 'fade-in-section';
        // We can add stagger inline if we want precise control or just rely on natural scroll
        li.style.transitionDelay = `${index * 0.1}s`;

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

function initScrollAnimations() {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Animate only once
            }
        });
    }, observerOptions);

    const elements = document.querySelectorAll('.fade-in-section');
    elements.forEach(el => observer.observe(el));

    // Also add footer to animation
    const footer = document.querySelector('.footer');
    if (footer) {
        footer.classList.add('fade-in-section');
        observer.observe(footer);
    }
}

function renderFloatingWA() {
    if (document.querySelector('.floating-wa')) return;

    const link = document.createElement('a');
    link.href = heroActions.main.href; // Use same link as Hero
    link.className = 'floating-wa';
    link.target = '_blank';
    link.rel = 'noopener';
    link.setAttribute('aria-label', 'Falar no WhatsApp');
    link.innerHTML = heroActions.main.icon; // Reuse valid SVG

    document.body.appendChild(link);
}

function renderFAQ() {
    // Append to content container, after links
    const container = document.querySelector('.content');
    if (!container) return;

    // Check if FAQ section exists
    if (document.querySelector('.faq-section')) return;

    const faqSection = document.createElement('section');
    faqSection.className = 'faq-section fade-in-section';

    const heading = document.createElement('h2');
    heading.className = 'section-heading';
    heading.textContent = 'Perguntas Frequentes';
    heading.style.textAlign = 'center';
    faqSection.appendChild(heading);

    import('../../data.js').then(({ faq }) => {
        if (!faq) return;

        faq.forEach(item => {
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';

            accordionItem.innerHTML = `
                <button class="accordion-header" aria-expanded="false">
                    ${item.question}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="accordion-content">
                    <p>${item.answer}</p>
                </div>
            `;

            const header = accordionItem.querySelector('.accordion-header');
            header.addEventListener('click', () => {
                const isOpen = accordionItem.classList.contains('active');

                // Close others (optional - nice for mobile)
                document.querySelectorAll('.accordion-item').forEach(i => {
                    i.classList.remove('active');
                    i.querySelector('.accordion-header').setAttribute('aria-expanded', 'false');
                });

                if (!isOpen) {
                    accordionItem.classList.add('active');
                    header.setAttribute('aria-expanded', 'true');
                }
            });

            faqSection.appendChild(accordionItem);
        });

        container.appendChild(faqSection);
    });
}

function renderRegularizationCTA() {
    const container = document.querySelector('.content');
    if (!container) return;

    if (document.querySelector('.cta-box')) return;

    import('../../data.js').then(({ regularizationCTA }) => {
        if (!regularizationCTA) return;

        const cta = document.createElement('div');
        cta.className = 'cta-box fade-in-section';

        cta.innerHTML = `
            <h3>${regularizationCTA.title}</h3>
            <p>${regularizationCTA.text}</p>
            <a href="${regularizationCTA.href}" target="_blank" rel="noopener" class="cta-button">
                ${regularizationCTA.buttonLabel}
            </a>
        `;

        // Insert before FAQ if exists to maintain order Services -> Reg CTA -> FAQ
        const faq = document.querySelector('.faq-section');
        if (faq) {
            container.insertBefore(cta, faq);
        } else {
            container.appendChild(cta);
        }
    });
}

function renderTips() {
    const container = document.querySelector('.content');
    if (!container) return;
    if (document.querySelector('.tips-section')) return;

    import('../../data.js').then(({ tips }) => {
        if (!tips) return;

        const section = document.createElement('section');
        section.className = 'tips-section fade-in-section';

        const heading = document.createElement('h2');
        heading.className = 'section-heading';
        heading.textContent = 'Dicas do Engenheiro';
        section.appendChild(heading);

        const list = document.createElement('div');
        list.className = 'tips-grid';

        tips.forEach(tip => {
            const card = document.createElement('div');
            card.className = 'tip-card';
            card.innerHTML = `
                <div class="tip-icon">${tip.icon}</div>
                <div class="tip-title">${tip.title}</div>
                <div class="tip-text">${tip.text}</div>
            `;
            list.appendChild(card);
        });

        section.appendChild(list);

        // Insert before CTA if exists: Services -> Tips -> CTA -> FAQ
        const cta = document.querySelector('.cta-box');
        const faq = document.querySelector('.faq-section');

        if (cta) {
            container.insertBefore(section, cta);
        } else if (faq) {
            container.insertBefore(section, faq);
        } else {
            container.appendChild(section);
        }
    });
}
