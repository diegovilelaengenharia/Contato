import React, { useState, useEffect, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import { 
  Menu, 
  X, 
  Phone, 
  Mail, 
  MapPin, 
  CheckCircle, 
  HardHat, 
  Ruler, 
  FileText, 
  Building2,
  ArrowRight,
  Instagram,
  Linkedin,
  Star,
  Quote
} from 'lucide-react';

// Componente para animação de entrada (Fade In)
const FadeInSection = ({ children, delay = 0 }: { children: React.ReactNode, delay?: number }) => {
  const [isVisible, setVisible] = useState(false);
  const domRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => setVisible(entry.isIntersecting));
    });
    if (domRef.current) observer.observe(domRef.current);
    return () => {
      if (domRef.current) observer.unobserve(domRef.current);
    };
  }, []);

  return (
    <div
      ref={domRef}
      className={`transition-all duration-1000 ease-out transform ${
        isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
      }`}
      style={{ transitionDelay: `${delay}ms` }}
    >
      {children}
    </div>
  );
};

const App = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  // Link do WhatsApp
  const whatsappLink = "https://wa.me/5511999999999?text=Olá,%20vim%20pelo%20site%20da%20Vilela%20Engenharia.";

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

  const navLinks = [
    { name: 'Início', href: '#home' },
    { name: 'Serviços', href: '#servicos' },
    { name: 'Projetos', href: '#projetos' },
    { name: 'Sobre', href: '#sobre' },
    { name: 'Contato', href: '#contato' },
  ];

  return (
    <div className="font-sans text-slate-800 bg-slate-50">
      {/* Navbar */}
      <nav className="fixed w-full z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-slate-100 transition-all">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex-shrink-0 flex items-center">
              <span className="text-2xl font-bold text-primary tracking-tight">
                Vilela<span className="text-secondary">.</span>Eng
              </span>
            </div>
            
            {/* Desktop Menu */}
            <div className="hidden md:flex space-x-8">
              {navLinks.map((link) => (
                <a 
                  key={link.name} 
                  href={link.href} 
                  className="text-slate-600 hover:text-accent font-medium transition-colors text-sm uppercase tracking-wide"
                >
                  {link.name}
                </a>
              ))}
              <a 
                href={whatsappLink}
                target="_blank"
                rel="noopener noreferrer"
                className="bg-primary text-white px-4 py-2 rounded-md hover:bg-slate-800 transition-colors flex items-center gap-2 text-sm font-semibold shadow-md hover:shadow-lg"
              >
                <Phone size={16} />
                Fale Conosco
              </a>
            </div>

            {/* Mobile Menu Button */}
            <div className="md:hidden flex items-center">
              <button 
                onClick={toggleMenu}
                className="text-slate-600 hover:text-primary p-2 focus:outline-none"
              >
                {isMenuOpen ? <X size={28} /> : <Menu size={28} />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile Menu Dropdown */}
        {isMenuOpen && (
          <div className="md:hidden bg-white border-t border-slate-100 absolute w-full shadow-lg animate-in slide-in-from-top-5">
            <div className="px-4 pt-2 pb-6 space-y-2">
              {navLinks.map((link) => (
                <a
                  key={link.name}
                  href={link.href}
                  onClick={() => setIsMenuOpen(false)}
                  className="block px-3 py-3 rounded-md text-base font-medium text-slate-600 hover:text-primary hover:bg-slate-50 border-b border-slate-50 last:border-0"
                >
                  {link.name}
                </a>
              ))}
              <a 
                href={whatsappLink}
                target="_blank"
                rel="noopener noreferrer"
                className="block w-full text-center mt-4 bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 font-bold transition-colors shadow-md"
              >
                Falar no WhatsApp
              </a>
            </div>
          </div>
        )}
      </nav>

      {/* Hero Section */}
      <section id="home" className="relative pt-24 pb-20 md:pt-36 md:pb-28 overflow-hidden">
        <div className="absolute inset-0 z-0">
          <img 
            src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
            alt="Canteiro de obras" 
            className="w-full h-full object-cover opacity-15"
          />
          <div className="absolute inset-0 bg-gradient-to-b from-slate-50/60 via-slate-50/80 to-slate-50"></div>
        </div>

        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center md:text-left">
          <div className="md:w-3/4 lg:w-2/3">
            <FadeInSection>
              <div className="inline-block px-3 py-1 mb-6 text-xs font-bold tracking-widest text-primary uppercase bg-yellow-400 rounded-sm">
                Engenharia Civil & Construção
              </div>
              <h1 className="text-4xl md:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
                Transformando Ideias em <span className="text-accent relative inline-block">
                  Estruturas Sólidas
                  <svg className="absolute w-full h-3 -bottom-1 left-0 text-yellow-400 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none">
                    <path d="M0 5 Q 50 10 100 5" stroke="currentColor" strokeWidth="8" fill="none" opacity="0.5" />
                  </svg>
                </span>
              </h1>
              <p className="text-lg md:text-xl text-slate-600 mb-10 leading-relaxed max-w-2xl">
                Da concepção do projeto à entrega das chaves. A Vilela Engenharia oferece soluções técnicas com precisão, segurança e economia para sua obra.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a 
                  href={whatsappLink}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="bg-primary hover:bg-slate-800 text-white px-8 py-4 rounded-lg font-bold text-lg shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 ring-4 ring-slate-100"
                >
                  Solicitar Orçamento <ArrowRight size={20} />
                </a>
                <a 
                  href="#servicos"
                  className="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-8 py-4 rounded-lg font-bold text-lg shadow-sm hover:shadow transition-all flex items-center justify-center"
                >
                  Ver Serviços
                </a>
              </div>
            </FadeInSection>
          </div>
        </div>
      </section>

      {/* Services Section */}
      <section id="servicos" className="py-20 bg-white">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <FadeInSection>
              <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Nossos Serviços</h2>
              <div className="w-24 h-1.5 bg-secondary mx-auto rounded-full"></div>
              <p className="mt-6 text-slate-600 max-w-2xl mx-auto text-lg">
                Atuação completa para garantir a integridade e o sucesso do seu empreendimento.
              </p>
            </FadeInSection>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <ServiceCard 
              delay={100}
              icon={<Ruler size={32} className="text-white" />}
              title="Projetos Estruturais"
              description="Cálculo estrutural preciso para concreto armado e metálicas, focando na segurança e redução de custos de materiais."
            />
            <ServiceCard 
              delay={200}
              icon={<HardHat size={32} className="text-white" />}
              title="Gerenciamento de Obras"
              description="Fiscalização técnica, controle de cronograma e gestão de equipe para evitar desperdícios e atrasos."
            />
            <ServiceCard 
              delay={300}
              icon={<FileText size={32} className="text-white" />}
              title="Laudos & Vistorias"
              description="Perícias técnicas, laudos de reforma (NBR 16.280) e inspeção predial para condomínios e residências."
            />
            <ServiceCard 
              delay={400}
              icon={<Building2 size={32} className="text-white" />}
              title="Regularização"
              description="Aprovação de projetos na prefeitura, obtenção de Habite-se, desdobro de lote e unificação."
            />
            <ServiceCard 
              delay={500}
              icon={<CheckCircle size={32} className="text-white" />}
              title="Reformas"
              description="Execução de obras e reformas de alto padrão com equipe qualificada e supervisão constante de engenheiro."
            />
            <ServiceCard 
              delay={600}
              icon={<MapPin size={32} className="text-white" />}
              title="Consultoria Técnica"
              description="Assessoria técnica para compra de imóveis, análise de viabilidade e diagnóstico de patologias."
            />
          </div>
        </div>
      </section>

      {/* Projects Section */}
      <section id="projetos" className="py-20 bg-slate-50 border-t border-slate-200">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <FadeInSection>
            <div className="flex flex-col md:flex-row justify-between items-end mb-12">
              <div className="max-w-2xl">
                <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Obras em Destaque</h2>
                <div className="w-24 h-1.5 bg-secondary rounded-full"></div>
                <p className="mt-4 text-slate-600">Confira alguns dos projetos onde nossa engenharia fez a diferença.</p>
              </div>
              <a href={whatsappLink} className="hidden md:inline-flex items-center gap-2 text-accent font-bold hover:text-primary transition-colors mt-4 md:mt-0">
                Ver portfólio completo <ArrowRight size={16} />
              </a>
            </div>
          </FadeInSection>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <ProjectCard 
              image="https://images.unsplash.com/photo-1600607686527-6fb886090705?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
              title="Residência Alto Padrão"
              category="Projeto & Execução"
              description="Execução completa de residência com 350m², incluindo estrutura, acabamentos e área de lazer."
            />
            <ProjectCard 
              image="https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
              title="Reforço Estrutural Comercial"
              category="Reforma & Estrutura"
              description="Intervenção estrutural para ampliação de carga em galpão logístico, mantendo a operação ativa."
            />
          </div>
          
          <div className="mt-8 text-center md:hidden">
             <a href={whatsappLink} className="inline-flex items-center gap-2 text-accent font-bold hover:text-primary transition-colors">
                Ver mais projetos no WhatsApp <ArrowRight size={16} />
              </a>
          </div>
        </div>
      </section>

      {/* About/CTA Section */}
      <section id="sobre" className="py-20 bg-primary text-white relative overflow-hidden">
         {/* Background pattern */}
         <div className="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-secondary rounded-full opacity-10 blur-3xl"></div>
         <div className="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-accent rounded-full opacity-10 blur-3xl"></div>

        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="flex flex-col md:flex-row items-center gap-16">
            <div className="w-full md:w-1/2">
              <FadeInSection>
                <div className="relative">
                  <img 
                    src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                    alt="Engenheiro analisando projeto" 
                    className="rounded-lg shadow-2xl relative z-10"
                  />
                  <div className="absolute -bottom-6 -right-6 w-2/3 h-2/3 border-4 border-secondary rounded-lg z-0"></div>
                </div>
              </FadeInSection>
            </div>
            <div className="w-full md:w-1/2">
              <FadeInSection delay={200}>
                <h2 className="text-3xl font-bold mb-6">Excelência Técnica & Transparência</h2>
                <p className="text-slate-300 mb-8 text-lg leading-relaxed">
                  A Vilela Engenharia nasceu com o propósito de descomplicar obras. Entendemos que construir ou reformar é um investimento de vida. Por isso, tratamos cada detalhe técnico com rigor, garantindo que seu orçamento seja respeitado e sua segurança priorizada.
                </p>
                <div className="grid grid-cols-1 gap-4 mb-10">
                  <div className="flex items-start gap-4 p-4 bg-slate-800 rounded-lg border border-slate-700">
                    <CheckCircle className="text-secondary shrink-0" size={24} />
                    <div>
                      <h4 className="font-bold text-white">Conformidade NBR</h4>
                      <p className="text-sm text-slate-400">Todos os projetos seguem rigorosamente as normas técnicas vigentes.</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-4 p-4 bg-slate-800 rounded-lg border border-slate-700">
                    <CheckCircle className="text-secondary shrink-0" size={24} />
                    <div>
                      <h4 className="font-bold text-white">Gestão Transparente</h4>
                      <p className="text-sm text-slate-400">Relatórios fotográficos e prestação de contas clara durante a obra.</p>
                    </div>
                  </div>
                </div>
                <a 
                  href={whatsappLink}
                  className="inline-block bg-secondary text-slate-900 font-bold px-8 py-3 rounded hover:bg-yellow-400 transition-colors shadow-lg shadow-yellow-500/20"
                >
                  Falar com Engenheiro
                </a>
              </FadeInSection>
            </div>
          </div>
        </div>
      </section>

      {/* Testimonials */}
      <section className="py-20 bg-white">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <FadeInSection>
            <h2 className="text-2xl font-bold text-slate-900 mb-12">O que dizem nossos clientes</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div className="bg-slate-50 p-8 rounded-xl relative">
                <Quote className="absolute top-4 left-4 text-slate-200" size={40} />
                <p className="text-slate-600 mb-6 relative z-10 italic">
                  "Contratei a Vilela para o laudo de reforma do meu apartamento. Foram super ágeis e técnicos. O condomínio aprovou de primeira."
                </p>
                <div className="flex items-center justify-center gap-2 text-secondary">
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                </div>
                <p className="font-bold text-primary mt-2">Ricardo Silva</p>
                <p className="text-xs text-slate-500">Reforma Residencial</p>
              </div>
              <div className="bg-slate-50 p-8 rounded-xl relative">
                <Quote className="absolute top-4 left-4 text-slate-200" size={40} />
                <p className="text-slate-600 mb-6 relative z-10 italic">
                  "Excelente gerenciamento de obra. Cumpriram o prazo estipulado e me ajudaram a economizar na compra dos materiais."
                </p>
                <div className="flex items-center justify-center gap-2 text-secondary">
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                  <Star size={16} fill="currentColor" />
                </div>
                <p className="font-bold text-primary mt-2">Ana Paula Souza</p>
                <p className="text-xs text-slate-500">Construção Comercial</p>
              </div>
            </div>
          </FadeInSection>
        </div>
      </section>

      {/* Footer / Contact */}
      <footer id="contato" className="bg-slate-900 pt-16 border-t border-slate-800 text-slate-300">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div>
              <div className="flex items-center mb-6">
                <span className="text-2xl font-bold text-white tracking-tight">
                  Vilela<span className="text-secondary">.</span>Eng
                </span>
              </div>
              <p className="text-slate-400 mb-6 leading-relaxed">
                Engenharia séria para construir sonhos e estruturar o futuro. Atendemos com compromisso técnico e ético.
              </p>
              <div className="flex space-x-4">
                <a href="#" className="p-2 bg-slate-800 rounded-full hover:bg-secondary hover:text-slate-900 transition-all">
                  <Instagram size={20} />
                </a>
                <a href="#" className="p-2 bg-slate-800 rounded-full hover:bg-secondary hover:text-slate-900 transition-all">
                  <Linkedin size={20} />
                </a>
              </div>
            </div>
            
            <div>
              <h3 className="text-lg font-bold text-white mb-6">Contato Rápido</h3>
              <ul className="space-y-4">
                <li>
                  <a href={whatsappLink} className="flex items-center gap-3 text-slate-400 hover:text-white transition-colors group">
                    <div className="p-2 bg-slate-800 rounded group-hover:bg-secondary group-hover:text-slate-900 transition-colors">
                      <Phone size={18} /> 
                    </div>
                    (11) 99999-9999
                  </a>
                </li>
                <li>
                  <a href="mailto:contato@vilela.eng.br" className="flex items-center gap-3 text-slate-400 hover:text-white transition-colors group">
                     <div className="p-2 bg-slate-800 rounded group-hover:bg-secondary group-hover:text-slate-900 transition-colors">
                      <Mail size={18} /> 
                    </div>
                    contato@vilela.eng.br
                  </a>
                </li>
                <li className="flex items-start gap-3 text-slate-400 group">
                   <div className="p-2 bg-slate-800 rounded group-hover:bg-secondary group-hover:text-slate-900 transition-colors mt-1">
                    <MapPin size={18} /> 
                  </div>
                  <span>São Paulo - SP<br/>Atendemos toda região</span>
                </li>
              </ul>
            </div>

            <div>
              <h3 className="text-lg font-bold text-white mb-6">Links Úteis</h3>
              <ul className="space-y-2 text-slate-400 text-sm">
                <li><a href="#home" className="hover:text-secondary transition-colors">Início</a></li>
                <li><a href="#servicos" className="hover:text-secondary transition-colors">Serviços</a></li>
                <li><a href="#projetos" className="hover:text-secondary transition-colors">Obras Realizadas</a></li>
                <li><a href="#sobre" className="hover:text-secondary transition-colors">Sobre a Empresa</a></li>
                <li><a href="#" className="hover:text-secondary transition-colors">Política de Privacidade</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-slate-500 text-sm">
            <p>&copy; {new Date().getFullYear()} Vilela Engenharia. Todos os direitos reservados.</p>
            <p className="mt-2 md:mt-0">CNPJ: 00.000.000/0001-00</p>
          </div>
        </div>
      </footer>

      {/* WhatsApp Floating Button */}
      <a 
        href={whatsappLink}
        target="_blank"
        rel="noopener noreferrer"
        className="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all z-50 hover:scale-110 animate-bounce-slow ring-4 ring-green-500/30"
        aria-label="Falar no WhatsApp"
      >
        <Phone size={28} fill="currentColor" />
        <span className="absolute -top-1 -right-1 flex h-3 w-3">
          <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
          <span className="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        </span>
      </a>
    </div>
  );
};

// Cards de Serviço
const ServiceCard = ({ icon, title, description, delay = 0 }: { icon: React.ReactNode, title: string, description: string, delay?: number }) => (
  <FadeInSection delay={delay}>
    <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 group h-full">
      <div className="mb-6 bg-accent w-14 h-14 rounded-lg flex items-center justify-center shadow-lg shadow-accent/20 group-hover:scale-110 transition-transform duration-300">
        {icon}
      </div>
      <h3 className="text-xl font-bold text-slate-900 mb-3 group-hover:text-accent transition-colors">{title}</h3>
      <p className="text-slate-600 leading-relaxed text-sm">
        {description}
      </p>
    </div>
  </FadeInSection>
);

// Card de Projeto
const ProjectCard = ({ image, title, category, description }: { image: string, title: string, category: string, description: string }) => (
  <FadeInSection>
    <div className="group rounded-xl overflow-hidden bg-white shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer">
      <div className="relative h-64 overflow-hidden">
        <div className="absolute inset-0 bg-slate-900/20 group-hover:bg-slate-900/0 transition-all z-10"></div>
        <img src={image} alt={title} className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" />
        <div className="absolute top-4 left-4 z-20 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary uppercase tracking-wide">
          {category}
        </div>
      </div>
      <div className="p-6">
        <h3 className="text-xl font-bold text-slate-900 mb-2">{title}</h3>
        <p className="text-slate-600 text-sm mb-4 line-clamp-2">{description}</p>
        <div className="flex items-center text-accent font-semibold text-sm group-hover:gap-2 transition-all">
          Ver detalhes <ArrowRight size={16} className="ml-1" />
        </div>
      </div>
    </div>
  </FadeInSection>
);

const container = document.getElementById('root');
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}
