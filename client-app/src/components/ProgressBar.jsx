import { CheckCircle, Circle } from 'lucide-react';

const FASES = [
    "Protocolo e Autuação",
    "Análise Documental",
    "Vistoria Técnica In Loco",
    "Emissão de Laudos/Peças",
    "Tramitação e Aprovação",
    "Entrega Final/Habite-se"
];

export default function ProgressBar({ currentPhase }) {
    const currentPhaseIndex = FASES.indexOf(currentPhase);
    const progressPercent = Math.round(((currentPhaseIndex + 1) / FASES.length) * 100);

    return (
        <div className="w-full">
            <div className="flex justify-between items-end mb-6">
                <div>
                    <h4 className="text-sm font-bold text-vilela-subtle uppercase tracking-wider mb-1">Status Atual</h4>
                    <p className="text-2xl font-bold text-vilela-primary">{currentPhase}</p>
                </div>
                <div className="text-right">
                    <span className="text-3xl font-bold text-vilela-primary">{progressPercent}%</span>
                    <span className="text-xs text-gray-400 block font-medium">CONCLUÍDO</span>
                </div>
            </div>

            {/* Bar */}
            <div className="h-2.5 w-full bg-vilela-bg rounded-full overflow-hidden mb-8 shadow-inner">
                <div
                    className="h-full bg-vilela-primary shadow-lg shadow-vilela-primary/40 transition-all duration-1000 ease-out relative"
                    style={{ width: `${progressPercent}%` }}
                >
                    <div className="absolute top-0 left-0 w-full h-full bg-white/20 animate-pulse"></div>
                </div>
            </div>

            {/* Steps (Desktop) */}
            <div className="hidden md:flex justify-between relative px-2">
                {/* Connecting Line */}
                <div className="absolute top-3.5 left-0 w-full h-0.5 bg-vilela-border -z-10" />

                {FASES.map((fase, index) => {
                    const isCompleted = index <= currentPhaseIndex;
                    const isCurrent = index === currentPhaseIndex;

                    return (
                        <div key={index} className="flex flex-col items-center group w-1/6">
                            <div
                                className={`w-7 h-7 rounded-full flex items-center justify-center border-2 transition-all duration-300
                  ${isCompleted ? 'bg-vilela-primary border-vilela-primary shadow-md shadow-vilela-primary/20' : 'bg-white border-gray-300'}
                  ${isCurrent ? 'ring-4 ring-vilela-light scale-110' : ''}
                `}
                            >
                                {isCompleted ? (
                                    <CheckCircle size={14} className="text-white" strokeWidth={3} />
                                ) : (
                                    <Circle size={8} className="text-gray-300 fill-gray-100" />
                                )}
                            </div>
                            <span className={`mt-3 text-[10px] text-center font-bold px-1 transition-colors uppercase tracking-tight
                ${isCurrent ? 'text-vilela-primary scale-105' : 'text-gray-400'}
              `}>
                                {fase}
                            </span>
                        </div>
                    )
                })}
            </div>

            {/* Mobile Steps (Compact) */}
            <div className="md:hidden space-y-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <h5 className="text-xs font-bold text-gray-400 uppercase">Histórico de Etapas</h5>
                {FASES.map((fase, index) => {
                    if (index > currentPhaseIndex) return null; // Show only up to current
                    return (
                        <div key={index} className="flex items-center gap-3">
                            <CheckCircle size={14} className="text-vilela-primary" />
                            <span className={`text-xs font-bold ${index === currentPhaseIndex ? 'text-vilela-primary' : 'text-gray-600'}`}>
                                {fase}
                            </span>
                        </div>
                    )
                })}
            </div>
        </div>
    )
}
