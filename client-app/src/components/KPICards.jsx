import React from 'react';
import { DollarSign, TrendingUp, Clock, AlertCircle } from 'lucide-react';

export default function KPICards({ kpis }) {
    if (!kpis) return null;

    const cards = [
        {
            label: 'Investimento Total',
            value: kpis.total_pago,
            icon: <DollarSign size={24} strokeWidth={2} />,
            color: 'text-vilela-primary',
            bg: 'bg-vilela-primary/10',
            border: 'border-vilela-primary/20', // Subtle tint
            shadow: 'shadow-sm hover:shadow-md'
        },
        {
            label: 'A Receber (Futuro)',
            value: kpis.total_pendente,
            icon: <Clock size={24} strokeWidth={2} />,
            color: 'text-blue-600',
            bg: 'bg-blue-50',
            border: 'border-gray-100',
            shadow: 'shadow-sm hover:shadow-md'
        },
        {
            label: 'Em Atraso',
            value: kpis.total_atrasado,
            icon: <AlertCircle size={24} strokeWidth={2} />,
            color: 'text-red-600',
            bg: 'bg-red-50',
            border: 'border-red-100', // Warning border
            shadow: 'shadow-sm hover:shadow-md',
            alert: true
        }
    ];

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {cards.map((card, idx) => (
                <div
                    key={idx}
                    className={`
            relative overflow-hidden rounded-xl p-6 border transition-all duration-300 bg-white
            ${card.border} ${card.shadow}
            group
          `}
                >
                    <div className="flex items-center justify-between mb-4">
                        <div className={`p-3 rounded-xl ${card.bg} ${card.color} transition-colors`}>
                            {card.icon}
                        </div>
                        {card.alert && card.value > 0 && (
                            <span className="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                        )}
                    </div>

                    <div>
                        <p className="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">
                            {card.label}
                        </p>
                        <h3 className={`text-2xl font-bold tracking-tight ${card.alert && card.value > 0 ? 'text-red-600' : 'text-gray-900'}`}>
                            {formatCurrency(card.value)}
                        </h3>
                    </div>

                    {/* Subtle Hover Decor */}
                    <div className={`absolute -right-6 -bottom-6 w-24 h-24 rounded-full opacity-0 group-hover:opacity-5 transition-opacity duration-500 ${card.color.replace('text-', 'bg-')}`}></div>
                </div>
            ))}
        </div>
    );
}
