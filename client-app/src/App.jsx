import { useState, useEffect } from 'react'
import { useAuth } from './contexts/AuthContext'
import { useTheme } from './contexts/ThemeContext'
import './App.css' // Import Standard CSS

import {
  LayoutDashboard,
  LayoutList,
  FolderOpen,
  User,
  Bell,
  Search,
  Menu,
  LogOut,
  FileText,
  DollarSign,
  AlertTriangle,
  HelpCircle,
  ChevronRight,
  Settings,
  Home,
  HardDrive,
  Briefcase,
  Phone,
  Mail,
  MapPin,
  Maximize2,
  FileDigit,
  Sun,
  Moon
} from 'lucide-react';

import Card from './components/Card';
import ProgressBar from './components/ProgressBar';
import Timeline from './components/Timeline';
import FinanceWidget from './components/FinanceWidget';
import PendencyWidget from './components/PendencyWidget';
import UploadArea from './components/UploadArea';
import KPICards from './components/KPICards';

// --- REAL DATA FETCHING ---
function App() {
  const { user, loading: authLoading } = useAuth();
  const { isDarkMode, toggleTheme } = useTheme();
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);
  const [activeTab, setActiveTab] = useState('inicial');

  const [clientData, setClientData] = useState(null);
  const [dataLoading, setDataLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Basic viewport fix for mobile
    document.body.style.paddingBottom = window.innerWidth < 768 ? '80px' : '0px';
    return () => { document.body.style.paddingBottom = '0px'; };
  }, []);

  // Fetch Client Data
  useEffect(() => {
    if (user) {
      if (window.DATA) { // Dev Mode Mock
        console.log("DEV MODE: Mock Data Loaded");
        setClientData(window.DATA);
        setDataLoading(false);
        return;
      }

      fetch('/area-cliente/api/get_client_data.php', { credentials: 'include' })
        .then(res => {
          if (!res.ok) throw new Error('Falha ao carregar dados');
          return res.json();
        })
        .then(data => {
          setClientData(data);
          setDataLoading(false);
        })
        .catch(err => {
          console.error(err);
          setError(err.message);
          setDataLoading(false);
        });
    } else if (!authLoading && !user) {
      setDataLoading(false);
    }
  }, [user, authLoading]);

  // States
  if (authLoading || (user && dataLoading)) return <div className="flex-center" style={{ height: '100vh', color: 'var(--color-primary)' }}>Carregando...</div>;

  if (!user) {
    return (
      <div className="flex-center flex-col" style={{ height: '100vh', padding: 20 }}>
        <h2>Acesso Restrito</h2>
        <p>Faça login para continuar.</p>
        <a href="/area-cliente/" className="nav-item active" style={{ display: 'inline-flex', width: 'auto', marginTop: 20 }}>Login</a>
      </div>
    )
  }

  const DATA = clientData || {};
  const processDetails = DATA.processDetails || {};
  const timeline = DATA.timeline || [];
  const financeiro = DATA.financeiro || [];
  const financeiroKPIs = DATA.financeiro_kpis || null;
  const pendencias = DATA.pendencias || [];
  const engineer = DATA.engineer || {};

  return (
    <div className="app-container">

      {/* --- DESKTOP SIDEBAR --- */}
      <aside className={`sidebar ${!isSidebarOpen ? 'closed' : ''}`}>
        <div className="sidebar-header">
          <img src="/logo.png" alt="Vilela" className="logo-img" />
        </div>

        {/* User Snippet */}
        <div className="sidebar-profile">
          <div className="user-snippet">
            {user?.foto ? <img src={DATA.user_photo || user.foto} className="user-avatar-small" /> :
              <div className="user-avatar-small flex-center" style={{ background: '#eee', color: '#999' }}>{user?.name?.[0]}</div>}

            {isSidebarOpen && <div className="user-meta">
              <h3>{user?.name?.split(' ')[0]}</h3>
              <span>Cliente Vilela</span>
            </div>}
          </div>
        </div>

        {/* Navigation */}
        <nav className="nav-list">
          <NavItem icon={<Home size={20} />} label="Início" active={activeTab === 'inicial'} onClick={() => setActiveTab('inicial')} expanded={isSidebarOpen} />
          <NavItem icon={<LayoutList size={20} />} label="Timeline" active={activeTab === 'timeline'} onClick={() => setActiveTab('timeline')} expanded={isSidebarOpen} />
          <NavItem icon={<AlertTriangle size={20} />} label="Pendências" active={activeTab === 'pendencias'} onClick={() => setActiveTab('pendencias')} expanded={isSidebarOpen} badge={pendencias.length} />
          <div style={{ height: 1, background: 'var(--ios-separator)', margin: '12px 0' }}></div>
          <NavItem icon={<DollarSign size={20} />} label="Financeiro" active={activeTab === 'financeiro'} onClick={() => setActiveTab('financeiro')} expanded={isSidebarOpen} />
          <NavItem icon={<HardDrive size={20} />} label="Documentos" active={activeTab === 'documentos'} onClick={() => setActiveTab('documentos')} expanded={isSidebarOpen} />
        </nav>
      </aside>

      {/* --- MAIN CONTENT --- */}
      <div className="main-content">

        {/* Desktop Header */}
        <header className="desktop-header">
          <div className="breadcrumb">
            {activeTab === 'inicial' ? 'Visão Geral' : activeTab.charAt(0).toUpperCase() + activeTab.slice(1)}
          </div>
          {/* Engineer Info or Actions */}
          <div style={{ display: 'flex', gap: 16 }}>
            <button onClick={toggleTheme} style={{ background: 'none', border: 'none', cursor: 'pointer' }}>
              {isDarkMode ? <Sun size={20} /> : <Moon size={20} />}
            </button>
          </div>
        </header>

        {/* Scrollable Area */}
        <main className="content-scrollable animate-in">

          {/* === VIEW: HERO (INICIAL) === */}
          {activeTab === 'inicial' && (
            <>
              <div className="hero-card">
                <div className="hero-avatar-container">
                  {DATA.user_photo ?
                    <img src={DATA.user_photo} className="hero-avatar" /> :
                    <div className="hero-avatar flex-center" style={{ background: '#eee', fontSize: 32 }}>{user?.name?.[0]}</div>
                  }
                </div>
                <div className="hero-info">
                  <h1>{user?.name}</h1>
                  <div className="info-pills">
                    <div className="info-pill"><User size={14} /> {DATA.client_info?.cpf || 'CPF --'}</div>
                    <div className="info-pill"><MapPin size={14} /> {processDetails.address || 'Sem endereço'}</div>
                  </div>
                </div>
              </div>
              {/* Additional Content Here */}
            </>
          )}

          {/* === VIEW: TIMELINE === */}
          {activeTab === 'timeline' && (
            <div className="ios-section">
              <h2>Acompanhamento</h2>
              <Timeline movements={timeline} />
            </div>
          )}

          {/* === VIEW: PENDENCIAS === */}
          {activeTab === 'pendencias' && (
            <div className="ios-section">
              <h2>Pendências do Processo</h2>
              <PendencyWidget pendencias={pendencias} />
            </div>
          )}

          {/* === VIEW: FINANCEIRO === */}
          {activeTab === 'financeiro' && (
            <div className="ios-section">
              <KPICards kpis={financeiroKPIs} />
              <div style={{ marginTop: 24 }}>
                <FinanceWidget financeiro={financeiro} />
              </div>
            </div>
          )}

          {/* === VIEW: DOCUMENTOS === */}
          {activeTab === 'documentos' && (
            <div className="hero-card" style={{ height: '100%' }}>
              {DATA.driveLink ?
                <iframe src={DATA.driveLink} style={{ width: '100%', height: '500px', border: 'none' }}></iframe> :
                <p>Drive não conectado.</p>
              }
            </div>
          )}

        </main>

        {/* --- MOBILE DOCK --- */}
        <nav className="mobile-dock">
          <DockItem icon={<Home size={24} />} active={activeTab === 'inicial'} onClick={() => setActiveTab('inicial')} />
          <DockItem icon={<LayoutList size={24} />} active={activeTab === 'timeline'} onClick={() => setActiveTab('timeline')} />
          <DockItem icon={<AlertTriangle size={24} />} active={activeTab === 'pendencias'} onClick={() => setActiveTab('pendencias')} badge={pendencias.length} />
          <DockItem icon={<DollarSign size={24} />} active={activeTab === 'financeiro'} onClick={() => setActiveTab('financeiro')} />
          <DockItem icon={<HardDrive size={24} />} active={activeTab === 'documentos'} onClick={() => setActiveTab('documentos')} />
        </nav>
      </div>
    </div>
  )
}

// --- SUBCOMPONENTS ---

function NavItem({ icon, label, active, onClick, expanded, badge }) {
  return (
    <button className={`nav-item ${active ? 'active' : ''}`} onClick={onClick} title={label}>
      {icon}
      {expanded && <span>{label}</span>}
      {expanded && badge > 0 && <span className="nav-badge">{badge}</span>}
    </button>
  )
}

function DockItem({ icon, active, onClick, badge }) {
  return (
    <button className={`dock-item ${active ? 'active' : ''}`} onClick={onClick}>
      {icon}
      {badge > 0 && <span style={{ position: 'absolute', top: 12, right: 12, width: 8, height: 8, background: 'red', borderRadius: '50%' }}></span>}
    </button>
  )
}

export default App

