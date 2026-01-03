import { useState, useEffect } from 'react';
import { Upload, FileText, AlertCircle, CheckCircle, ChevronDown, ChevronUp, Info, ShieldAlert } from 'lucide-react';

export default function UploadArea({ clientId }) {
  const [types, setTypes] = useState({});
  const [selectedProcess, setSelectedProcess] = useState('');
  const [requirements, setRequirements] = useState(null);
  const [loading, setLoading] = useState(false);
  const [showConditionals, setShowConditionals] = useState(false);

  // 1. Fetch Process Types on Mount
  useEffect(() => {
    fetch('/area-cliente/api/get_requisitos.php')
      .then(res => res.json())
      .then(data => {
        if (data.tipos) setTypes(data.tipos);
      })
      .catch(err => console.error("Erro ao carregar tipos:", err));
  }, []);

  // 2. Fetch Requirements when Process changes
  useEffect(() => {
    if (!selectedProcess) {
      setRequirements(null);
      return;
    }

    setLoading(true);
    // Encode the process name properly for the URL
    fetch(`/area-cliente/api/get_requisitos.php?processo=${encodeURIComponent(selectedProcess)}`)
      .then(res => res.json())
      .then(data => {
        setRequirements(data);
        setLoading(false);
      })
      .catch(err => {
        console.error("Erro ao carregar requisitos:", err);
        setLoading(false);
      });
  }, [selectedProcess]);

  const handleUpload = (docName, file) => {
    // Placeholder for actual upload logic
    alert(`Implementar upload de "${docName}" para o processo "${selectedProcess}"`);
    console.log("File:", file);
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-500">
      
      {/* HEADER / SELECTION */}
      <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
          <FileText className="text-vilela-primary" />
          Protocolo Digital
        </h2>
        
        <div className="space-y-2">
          <label className="text-sm font-bold text-gray-600 block">Selecione o Tipo de Processo</label>
          <div className="relative">
            <select 
              value={selectedProcess}
              onChange={(e) => setSelectedProcess(e.target.value)}
              className="w-full p-3 pl-4 pr-10 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:border-vilela-primary focus:ring-1 focus:ring-vilela-primary bg-white text-gray-700 font-medium transition-all cursor-pointer"
            >
              <option value="">-- Selecione para ver a lista de documentos --</option>
              {Object.keys(types).map(group => (
                <optgroup key={group} label={group} className="font-bold text-gray-800">
                  {types[group].map(proc => (
                    <option key={proc} value={proc} className="text-gray-600 font-normal">
                      {proc}
                    </option>
                  ))}
                </optgroup>
              ))}
            </select>
            <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" size={18} />
          </div>
        </div>
      </div>

      {/* LOADING STATE */}
      {loading && (
        <div className="text-center py-12 text-gray-400">
          <div className="animate-spin w-8 h-8 border-2 border-vilela-primary border-t-transparent rounded-full mx-auto mb-2"></div>
          Carregando requisitos...
        </div>
      )}

      {/* REQUIREMENTS LIST */}
      {requirements && !loading && (
        <div className="grid gap-6">
          
          {/* MANDATORY SECTION */}
          <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
              <ShieldAlert className="text-red-600" size={20} />
              <div>
                <h3 className="text-sm font-bold text-red-900 uppercase tracking-wide">Documentação Obrigatória</h3>
                <p className="text-xs text-red-700 opacity-80">Necessário para iniciar a análise</p>
              </div>
            </div>
            
            <div className="divide-y divide-gray-50">
              {requirements.obrigatorios.map((req, idx) => (
                <UploadSlot key={idx} requirement={req} onUpload={handleUpload} isMandatory={true} />
              ))}
              {requirements.obrigatorios.length === 0 && (
                <div className="p-6 text-center text-gray-400 text-sm">Nenhum documento obrigatório listado.</div>
              )}
            </div>
          </div>

          {/* CONDITIONAL SECTION */}
          <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <button 
              onClick={() => setShowConditionals(!showConditionals)}
              className="w-full bg-blue-50 px-6 py-4 border-b border-blue-100 flex items-center justify-between hover:bg-blue-100 transition-colors"
            >
              <div className="flex items-center gap-3">
                <Info className="text-blue-600" size={20} />
                <div className="text-left">
                  <h3 className="text-sm font-bold text-blue-900 uppercase tracking-wide">Documentação Complementar / Condicionais</h3>
                  <p className="text-xs text-blue-700 opacity-80">Anexar apenas se o caso exigir (Ex: CNPJ para Pessoa Jurídica)</p>
                </div>
              </div>
              {showConditionals ? <ChevronUp className="text-blue-500" /> : <ChevronDown className="text-blue-500" />}
            </button>
            
            {showConditionals && (
              <div className="divide-y divide-gray-50 animate-in slide-in-from-top-2 duration-300">
                {requirements.condicionais.map((req, idx) => (
                  <UploadSlot key={idx} requirement={req} onUpload={handleUpload} isMandatory={false} />
                ))}
                {requirements.condicionais.length === 0 && (
                  <div className="p-6 text-center text-gray-400 text-sm">Nenhum documento condicional listado.</div>
                )}
              </div>
            )}
          </div>

        </div>
      )}
    </div>
  );
}

function UploadSlot({ requirement, onUpload, isMandatory }) {
  return (
    <div className="p-4 md:p-5 hover:bg-gray-50 transition-colors group">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        {/* INFO */}
        <div className="flex-1">
          <div className="flex items-start gap-2">
            <h4 className="font-bold text-gray-800 text-sm flex items-center gap-1.5">
              {requirement.nome}
              {isMandatory && <span className="text-red-500 text-lg leading-none" title="Obrigatório">*</span>}
            </h4>
            {requirement.formato && (
              <span className="text-[10px] font-mono bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200">
                {requirement.formato}
              </span>
            )}
          </div>
          
          {/* HELP TEXT / OBS */}
          {requirement.nome.includes('Imobiliária') && (
             <p className="text-xs text-gray-500 mt-1 flex items-center gap-1">
               <Info size={12} className="text-vilela-primary" />
               <span className="italic">Se caso 02, anexar Contrato de Compra e Venda + Matrícula</span>
             </p>
          )}
          {requirement.obs && <p className="text-xs text-gray-500 mt-1">{requirement.obs}</p>}
        </div>

        {/* ACTION */}
        <div className="shrink-0">
          <label className="cursor-pointer flex items-center gap-2 bg-white border border-dashed border-gray-300 hover:border-vilela-primary hover:text-vilela-primary text-gray-500 px-4 py-2 rounded-lg transition-all text-xs font-bold uppercase tracking-wide group-hover:shadow-sm">
            <Upload size={14} />
            <span>Selecionar Arquivo</span>
            <input type="file" className="hidden" onChange={(e) => onUpload(requirement.nome, e.target.files[0])} />
          </label>
        </div>

      </div>
    </div>
  );
}
