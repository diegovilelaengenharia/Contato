
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
        icon: null, // Icons for hero might be handle differently or we can add SVG strings here if we want full dynanism
        className: "hero-whatsapp",
        type: "link"
    },
    {
        label: "Salve meu contato",
        href: "assets/diego-vilela.vcf",
        icon: null,
        className: "hero-vcard",
        download: true,
        type: "link"
    }
];

export const links = [
    {
        id: "whatsapp",
        title: "WhatsApp",
        subtitle: "Fale com a equipe agora",
        href: "https://wa.me/5535984529577?text=Ola%20Vilela%20Engenharia",
        icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M12 2a10 10 0 0 0-8.66 15.14L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.13l-.29-.18-3 .79.8-2.91-.19-.3A8 8 0 1 1 12 20zm4.37-5.73-.52-.26a1.32 1.32 0 0 0-1.15.04l-.4.21a.5.5 0 0 1-.49 0 8.14 8.14 0 0 1-2.95-2.58.5.5 0 0 1 0-.49l.21-.4a1.32 1.32 0 0 0 .04-1.15l-.26-.52a1.32 1.32 0 0 0-1.18-.73h-.37a1 1 0 0 0-1 .86 3.47 3.47 0 0 0 .18 1.52A10.2 10.2 0 0 0 13 15.58a3.47 3.47 0 0 0 1.52.18 1 1 0 0 0 .86-1v-.37a1.32 1.32 0 0 0-.73-1.18z"></path></svg>`,
        className: "link-card--whatsapp",
        type: "link"
    },
    {
        id: "phone",
        title: "Ligar agora",
        subtitle: "(35) 98452-9577",
        href: "tel:+5535984529577",
        icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M6.62 10.79a15.91 15.91 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1-.24 12.36 12.36 0 0 0 3.88.62 1 1 0 0 1 1 1v3.57a1 1 0 0 1-1 1A17 17 0 0 1 3 5a1 1 0 0 1 1-1h3.55a1 1 0 0 1 1 1 12.36 12.36 0 0 0 .62 3.88 1 1 0 0 1-.24 1z"></path></svg>`,
        className: "link-card--phone",
        type: "link"
    },
    {
        id: "email",
        title: "E-mail",
        subtitle: "diegonunesvilela@gmail.com",
        href: "mailto:diegonunesvilela@gmail.com",
        icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"></path></svg>`,
        className: "link-card--email",
        type: "link"
    },
    {
        id: "instagram",
        title: "Instagram",
        subtitle: "@diegovilela.eng",
        href: "https://www.instagram.com/diegovilela.eng/",
        icon: `<svg viewBox="0 0 24 24" role="presentation"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm5 3.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 5A1.5 1.5 0 1 0 10.5 12 1.5 1.5 0 0 0 12 13.5zm4.25-6.75a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"></path></svg>`,
        className: "link-card--instagram",
        type: "link"
    }
];

export const footer = {
    copy: "&copy; <span id='year'>2025</span> Vilela Engenharia. Todos os direitos reservados."
};
