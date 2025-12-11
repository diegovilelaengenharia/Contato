export const profile = {
    name: "Diego Tarcisio Nunes Vilela",
    role: "Engenheiro Civil &middot; CREA 235.474/D",
    badge: "Engenheiro Civil",
    title: "Vilela Engenharia",
    logo: "assets/logo.jpg",
    clientAreaLink: "https://vilela.eng.br/portal"
};

export const heroActions = [
    {
        label: "(35) 98452-9577",
        href: "https://wa.me/5535984529577?text=Ola%20Vilela%20Engenharia",
        icon: null,
        className: "hero-whatsapp",
        type: "link"
    },
    {
        label: "Salvar Número",
        href: "assets/diego-vilela.vcf",
        icon: null,
        className: "hero-vcard",
        download: true,
        type: "link"
    },
    {
        label: "Compartilhar",
        icon: null,
        className: "hero-share",
        type: "action",
        id: "share"
    },
    {
        label: "QR Code",
        icon: null,
        className: "hero-qr",
        type: "action",
        id: "qr"
    }
];

export const links = {
    contact: {
        title: "Contatos",
        items: [
            {
                id: "whatsapp",
                title: "WhatsApp",
                subtitle: "Fale com a equipe agora",
                href: "https://wa.me/5535984529577?text=Ola%20Vilela%20Engenharia",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>`,
                className: "link-card--whatsapp"
            },
            {
                id: "phone",
                title: "Ligar agora",
                subtitle: "(35) 98452-9577",
                href: "tel:+5535984529577",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M6.62 10.79a15.91 15.91 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1-.24 12.36 12.36 0 0 0 3.88.62 1 1 0 0 1 1 1v3.57a1 1 0 0 1-1 1A17 17 0 0 1 3 5a1 1 0 0 1 1-1h3.55a1 1 0 0 1 1 1 12.36 12.36 0 0 0 .62 3.88 1 1 0 0 1-.24 1z"></path></svg>`,
                className: "link-card--phone"
            },
            {
                id: "email",
                title: "E-mail",
                subtitle: "diegonunesvilela@gmail.com",
                href: "mailto:diegonunesvilela@gmail.com",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"></path></svg>`,
                className: "link-card--email"
            }
        ]
    },
    services: {
        title: "Serviços",
        items: [
            {
                id: "project-city",
                title: "Projeto de Prefeitura",
                subtitle: "Aprovação e regularização",
                href: "https://wa.me/5535984529577?text=Ola,%20gostaria%20de%20saber%20mais%20sobre%20Projeto%20de%20Prefeitura",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M12 2L2 12h3v8h6v-6h2v6h6v-8h3L12 2zm0 2.84L19.5 12h-1.5v6h-2v-6h-8v6h-2v-6H4.5L12 4.84z"></path></svg>`,
                className: "link-card--service"
            },
            {
                id: "regularization",
                title: "Regularização de Imóvel",
                subtitle: "Regularize sua obra",
                href: "https://wa.me/5535984529577?text=Ola,%20gostaria%20de%20saber%20mais%20sobre%20Regularizacao%20de%20Imovel",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v7h7v9H6z"></path></svg>`,
                className: "link-card--service"
            },
            {
                id: "consulting",
                title: "Consultoria",
                subtitle: "Tire suas dúvidas técnicas",
                href: "https://wa.me/5535984529577?text=Ola,%20gostaria%20de%20saber%20mais%20sobre%20Consultoria",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm0 14H6l-2 2V4h16v12z"></path></svg>`,
                className: "link-card--service"
            }
        ]
    },
    social: {
        title: "Redes Sociais",
        items: [
            {
                id: "instagram",
                title: "Instagram",
                subtitle: "@diegovilela.eng",
                href: "https://www.instagram.com/diegovilela.eng/",
                icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm5 3.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 5A1.5 1.5 0 1 0 10.5 12 1.5 1.5 0 0 0 12 13.5zm4.25-6.75a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"></path></svg>`,
                className: "link-card--instagram"
            }
        ]
    }
};

export const footer = {
    copy: "&copy; <span id='year'>2025</span> Vilela Engenharia. Todos os direitos reservados."
};
