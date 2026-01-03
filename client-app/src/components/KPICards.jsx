import React from 'react';
import { DollarSign, TrendingUp, Clock, AlertCircle } from 'lucide-react';

export default function KPICards({ kpis }) {
    if (!kpis) return null;

    const cards = [
        {
            label: 'Investimento Total',
            value: kpis.total_pago,
            icon: <DollarSign size={22} />,
            color: 'text-emerald-500',
            bg: 'bg-emerald-50 dark:bg-emerald-900/20',
            border: 'border-emerald-100 dark:border-emerald-800/30'
        },
        {
            label: 'A Receber (Futuro)',
            value: kpis.total_pendente,
            icon: <Clock size={22} />,
            color: 'text-blue-500',
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            border: 'border-blue-100 dark:border-blue-800/30'
        },
        {
            label: 'Em Atraso',
            value: kpis.total_atrasado,
            icon: <AlertCircle size={22} />,
            color: 'text-red-500',
            bg: 'bg-red-50 dark:bg-red-900/20',
            border: 'border-red-100 dark:border-red-800/30',
            alert: true
        }
    ];

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {cards.map((card, idx) => (
                <div
                    key={idx}
                    className={`
            relative overflow-hidden rounded-2xl p-5 border transition-all duration-300
            bg-white dark:bg-gray-800/50 backdrop-blur-md
            ${card.border}
            hover:shadow-lg hover:-translate-y-1
          `}
                >
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">
                                {card.label}
                            </p>
                            <h3 className={`text-2xl font-bold ${paramsTextColor(card, kpis)}`}>
                                {formatCurrency(card.value)}
                            </h3>
                        </div>
                        <div className={`p-3 rounded-xl ${card.bg} ${card.color}`}>
                            {card.icon}
                        </div>
                    </div>

                    {/* Decorative Circle */}
                    <div className={`absolute -bottom-4 -right-4 w-24 h-24 rounded-full opacity-5 ${card.color.replace('text-', 'bg-')}`}></div>
                </div>
            ))}
        </div>
    );
}

function paramsTextColor(card, kpis) {
    if (card.alert && card.value > 0) return 'text-red-600 dark:text-red-400';
    return 'text-gray-800 dark:text-gray-100';
}
