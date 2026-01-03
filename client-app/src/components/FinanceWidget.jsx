import { TrendingUp, TrendingDown, DollarSign } from 'lucide-react';

export default function FinanceWidget({ financeiro }) {
    const stats = {
        total: 0,
        pago: 0,
        pendente: 0
    };

    if (financeiro) {
        financeiro.forEach(f => {
            const val = parseFloat(f.valor);
            stats.total += val;
            if (f.status === 'pago') stats.pago += val;
            else stats.pendente += val;
        });
    }

    const formatMoney = (val) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);

    return (
        <div className="grid grid-cols-2 gap-4">
            <div className="p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div className="flex items-center gap-3 mb-3">
                    <div className="p-2 bg-vilela-primary/10 rounded-lg text-vilela-primary group-hover:scale-110 transition-transform">
                        <TrendingUp size={18} />
                    </div>
                    <span className="text-xs font-bold uppercase tracking-wider text-gray-400">Pago</span>
                </div>
                <p className="text-2xl font-bold text-gray-900">{formatMoney(stats.pago)}</p>
            </div>

            <div className="p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div className="flex items-center gap-3 mb-3">
                    <div className="p-2 bg-orange-50 rounded-lg text-orange-500 group-hover:scale-110 transition-transform">
                        <DollarSign size={18} />
                    </div>
                    <span className="text-xs font-bold uppercase tracking-wider text-gray-400">A Pagar</span>
                </div>
                <p className="text-2xl font-bold text-gray-900">{formatMoney(stats.pendente)}</p>
            </div>

            {/* Optional: List next pending item */}
            {financeiro && financeiro.find(f => f.status !== 'pago') && (
                <div className="col-span-2 mt-2 pt-3 border-t border-gray-100">
                    <p className="text-xs text-gray-500 mb-1">Próximo Vencimento:</p>
                    {(() => {
                        const next = financeiro.find(f => f.status !== 'pago');
                        return (
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-gray-700">{next.descricao}</span>
                                <span className="font-bold text-gray-800">{new Date(next.data_vencimento).toLocaleDateString()}</span>
                            </div>
                        )
                    })()}
                </div>
            )}
        </div>
    )
}
