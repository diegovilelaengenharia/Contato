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
                    <div key={p.id} className={`
                        p-4 rounded-xl border flex items-start gap-4 transition-all duration-300 group
                        ${isResolved
                            ? 'bg-gray-50/50 border-gray-100 opacity-60 hover:opacity-100'
                            : 'bg-white border-red-100 shadow-sm hover:shadow-md hover:border-red-200'
                        }
                    `}>
                        <div className={`mt-0.5 p-2 rounded-full ${isResolved ? 'bg-gray-100 text-gray-400' : 'bg-red-50 text-red-500'}`}>
                            {isResolved ? <CheckCircle size={16} /> : <AlertCircle size={16} />}
                        </div>

                        <div className="flex-1">
                            <h5 className={`text-sm font-bold ${isResolved ? 'text-gray-500 line-through decoration-gray-300' : 'text-gray-900 group-hover:text-red-700 transition-colors'}`}>
                                {p.titulo}
                            </h5>
                            <p className="text-xs text-gray-500 mt-1 leading-relaxed">{p.descricao}</p>

                            {!isResolved && (
                                <button className="mt-3 flex items-center gap-2 text-xs font-bold bg-white border border-red-200 text-red-600 px-4 py-2 rounded-lg hover:bg-red-50 transition-colors shadow-sm">
                                    <Paperclip size={14} />
                                    Anexar Arquivo
                                </button>
                            )}
                        </div>

                        <span className={`text-[10px] px-2.5 py-1 rounded-full uppercase tracking-wider font-bold border ${isResolved ? 'bg-gray-50 border-gray-100 text-gray-400' : 'bg-red-50 border-red-100 text-red-600'
                            }`}>
                            {p.status}
                        </span>
                    </div>
                )
            })}
        </div>
    )
}
