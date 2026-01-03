import { AlertCircle, CheckCircle, Paperclip } from 'lucide-react';

export default function PendencyWidget({ pendencias }) {
    if (!pendencias || pendencias.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-8 text-center bg-gray-50 rounded-lg">
                <CheckCircle size={32} className="text-green-500 mb-2" />
                <p className="text-gray-500 font-medium">Tudo em dia!</p>
                <p className="text-xs text-gray-400">Nenhuma pendência solicitada.</p>
            </div>
        );
    }

    return (
        <div className="space-y-3">
            {pendencias.map((p) => {
                const isResolved = p.status === 'resolvido' || p.status === 'anexado';

                return (
                    <div key={p.id} className={`p-4 rounded-lg border flex items-start gap-3 transition-colors ${isResolved ? 'bg-gray-50 border-gray-100 opacity-75' : 'bg-red-50 border-red-100'
                        }`}>
                        <div className={`mt-0.5 ${isResolved ? 'text-green-500' : 'text-red-500'}`}>
                            {isResolved ? <CheckCircle size={20} /> : <AlertCircle size={20} />}
                        </div>

                        <div className="flex-1">
                            <h5 className={`text-sm font-bold ${isResolved ? 'text-gray-700 decoration-slate-400' : 'text-red-900'}`}>
                                {p.titulo}
                            </h5>
                            <p className="text-xs text-gray-600 mt-1">{p.descricao}</p>

                            {!isResolved && (
                                <button className="mt-3 flex items-center gap-1.5 text-xs font-semibold bg-white border border-red-200 text-red-600 px-3 py-1.5 rounded-md hover:bg-red-50">
                                    <Paperclip size={12} />
                                    Anexar Arquivo
                                </button>
                            )}
                        </div>

                        <span className={`text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider font-bold ${isResolved ? 'bg-green-100 text-green-700' : 'bg-red-200 text-red-800'
                            }`}>
                            {p.status}
                        </span>
                    </div>
                )
            })}
        </div>
    )
}
