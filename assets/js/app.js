import { profile, heroActions, links } from '../../data.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Preencher Header
    renderHeader();

    // 2. Renderizar Hero Actions (WhatsApp, VCard)
    renderHeroActions();

    // 3. Renderizar Links
    renderLinks();

    // 4. Inicializar Animações de Entrada
    initializeAnimations();

    // 5. Anexar eventos do Modal
    attachModalEvents();
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
        const isLink = action.type === 'link';
        // Se quisermos suportar buttons também na hero, podemos adaptar.
        // Por enquanto, assumimos links baseados no data.js

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
}

function renderLinks() {
    const listContainer = document.querySelector('.links-grid');
    if (!listContainer) return;

    listContainer.innerHTML = '';

    links.forEach((link, index) => {
        const li = document.createElement('li');
        // Define variavel CSS para o delay da animação
        const delay = (index + 1) * 0.1;
        li.style.setProperty('--animation-delay', `${delay}s`);

        let cardElement;

        // Verifica se é botão de ação (modal) ou link
        if (link.type === 'button') {
            cardElement = document.createElement('button');
            cardElement.type = 'button';
            cardElement.className = `link-card link-card--action ${link.className || ''}`;

            // Atributos específicos para modal se definidos
            if (link.attrs) {
                Object.entries(link.attrs).forEach(([key, value]) => {
                    cardElement.setAttribute(key, value);
                });
            }

            cardElement.setAttribute('aria-label', link.title);
        } else {
            cardElement = document.createElement('a');
            cardElement.className = `link-card ${link.className || ''}`;
            cardElement.href = link.href;
            cardElement.target = '_blank';
            cardElement.rel = 'noopener';
            cardElement.setAttribute('aria-label', link.title);

            if (link.href.startsWith('tel:') || link.href.startsWith('mailto:')) {
                cardElement.removeAttribute('target');
                cardElement.removeAttribute('rel');
            }
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
        listContainer.appendChild(li);
    });
}

function initializeAnimations() {
    requestAnimationFrame(() => {
        document.body.classList.add('animations-ready');
    });
}

function attachModalEvents() {
    const budgetModal = document.getElementById('budget-modal');
    if (!budgetModal) return;

    // Seleciona botões APÓS eles serem renderizados
    const openBudgetButtons = document.querySelectorAll('[data-open-budget]');
    const budgetForm = document.getElementById('budget-form');
    const statusMessage = document.querySelector('[data-budget-status]');

    // Trap Focus helpers
    const focusableSelectors = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';
    let lastFocusedElement = null;
    let focusableElements = [];
    let closeTimeoutId = null;

    const resetStatus = () => {
        if (!statusMessage) return;
        statusMessage.textContent = '';
        statusMessage.classList.remove('is-visible');
    };

    const setStatus = (message) => {
        if (!statusMessage) return;
        statusMessage.textContent = message;
        statusMessage.classList.toggle('is-visible', Boolean(message));
    };

    const updateFocusableElements = () => {
        if (!budgetModal) return;
        focusableElements = Array.from(
            budgetModal.querySelectorAll(focusableSelectors)
        ).filter((element) => !element.hasAttribute('disabled') && element.getAttribute('tabindex') !== '-1');
    };

    const trapFocus = (event) => {
        if (event.key !== 'Tab' || focusableElements.length === 0) return;
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    };

    const openBudgetModal = () => {
        if (!budgetModal) return;
        if (closeTimeoutId) {
            window.clearTimeout(closeTimeoutId);
            closeTimeoutId = null;
        }
        resetStatus();
        lastFocusedElement = document.activeElement;
        budgetModal.removeAttribute('hidden');
        budgetModal.classList.add('is-open');
        document.body.classList.add('modal-open');
        updateFocusableElements();

        const firstField = budgetForm ? budgetForm.querySelector('input, select, textarea') : null;
        const firstInteractive = firstField || focusableElements.find(el => el.tagName !== 'DIV');

        window.requestAnimationFrame(() => {
            if (firstInteractive) firstInteractive.focus();
            else budgetModal.focus();
        });
        budgetModal.addEventListener('keydown', trapFocus);
    };

    const closeBudgetModal = () => {
        if (!budgetModal || !budgetModal.classList.contains('is-open')) return;
        if (closeTimeoutId) {
            window.clearTimeout(closeTimeoutId);
            closeTimeoutId = null;
        }
        budgetModal.classList.remove('is-open');
        budgetModal.setAttribute('hidden', '');
        document.body.classList.remove('modal-open');
        budgetModal.removeEventListener('keydown', trapFocus);
        resetStatus();
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
        lastFocusedElement = null;
    };

    // Listeners para os botões
    openBudgetButtons.forEach((button) => {
        button.addEventListener('click', openBudgetModal);
    });

    // Singleton check para listeners do modal
    if (!budgetModal.dataset.listenersAttached) {
        budgetModal.addEventListener('click', (event) => {
            if (event.target.matches('[data-close-modal]')) {
                closeBudgetModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && budgetModal.classList.contains('is-open')) {
                closeBudgetModal();
            }
        });

        // Setup Form Logic
        if (budgetForm) {
            const validators = {
                telefone(value) {
                    const digits = value.replace(/\D/g, '');
                    if (digits.length < 10 || digits.length > 11) return { valid: false, message: 'Informe um número com DDD.' };
                    return { valid: true };
                },
                cidade(value) {
                    // Validação simples para Cidade / UF
                    if (!/^.+\s\/\s[A-Za-z]{2}$/.test(value)) return { valid: false, message: 'Use o formato Cidade / UF (ex: Pouso Alegre / MG).' };
                    return { valid: true };
                }
            };

            const validateField = (field) => {
                if (!field) return true;
                const value = field.value.trim();
                field.setCustomValidity('');
                if (field.required && !value) {
                    field.setCustomValidity('Preencha este campo.');
                    return false;
                }
                const validator = validators[field.name];
                if (validator) {
                    const res = validator(value);
                    if (!res.valid) {
                        field.setCustomValidity(res.message);
                        return false;
                    }
                }
                return field.checkValidity();
            };

            const handleFieldInteraction = (event) => validateField(event.target);

            budgetForm.querySelectorAll('input, select, textarea').forEach(f => {
                f.addEventListener('input', handleFieldInteraction);
                f.addEventListener('blur', handleFieldInteraction);
            });

            budgetForm.addEventListener('submit', (event) => {
                event.preventDefault();
                let isValid = true;
                budgetForm.querySelectorAll('input, select, textarea').forEach(f => {
                    if (!validateField(f)) {
                        isValid = false;
                        f.reportValidity();
                    }
                });
                if (!isValid) return;

                const fd = new FormData(budgetForm);
                const msg = `Olá, solicito orçamento.\n\nNome: ${fd.get('nome')}\nServiço: ${fd.get('servico')}\nCidade: ${fd.get('cidade')}\nWhatsApp: ${fd.get('telefone')}\nDetalhes: ${fd.get('detalhes')}`;

                const whatsappUrl = `https://wa.me/5535984529577?text=${encodeURIComponent(msg)}`;
                window.open(whatsappUrl, '_blank', 'noopener');

                budgetForm.reset();
                setStatus('Enviado! Verifique seu WhatsApp.');
                closeTimeoutId = setTimeout(closeBudgetModal, 2200);
            });
        }

        budgetModal.dataset.listenersAttached = "true";
    }
}
