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
            <div className="p-4 bg-green-50 rounded-xl border border-green-100">
                <div className="flex items-center gap-2 mb-2">
                    <div className="p-1.5 bg-green-200 rounded-md text-green-700">
                        <TrendingUp size={16} />
                    </div>
                    <span className="text-sm font-medium text-green-800">Pago</span>
                </div>
                <p className="text-xl font-bold text-green-900">{formatMoney(stats.pago)}</p>
            </div>

            <div className="p-4 bg-orange-50 rounded-xl border border-orange-100">
                <div className="flex items-center gap-2 mb-2">
                    <div className="p-1.5 bg-orange-200 rounded-md text-orange-700">
                        <DollarSign size={16} />
                    </div>
                    <span className="text-sm font-medium text-orange-800">A Pagar</span>
                </div>
                <p className="text-xl font-bold text-orange-900">{formatMoney(stats.pendente)}</p>
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
