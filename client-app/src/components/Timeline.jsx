import { Calendar, FileText, CheckCircle2, MessageSquare, UploadCloud, ChevronRight } from 'lucide-react';

export default function Timeline({ movements }) {
    if (!movements || movements.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center p-8 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <Calendar className="text-gray-400" size={20} />
                </div>
                <p className="text-gray-500 font-medium">Nenhuma movimentação registrada.</p>
                <p className="text-xs text-gray-400">O histórico aparecerá aqui.</p>
            </div>
        );
    }

    return (
        <div className="relative pl-4 md:pl-8 py-2">
            {/* Vertical Line */}
            <div className="absolute left-[27px] md:left-[43px] top-4 bottom-4 w-px bg-gradient-to-b from-gray-200 via-gray-200 to-transparent" />

            <div className="space-y-8">
                {movements.map((mov, index) => {
                    // ADMIN COLOR LOGIC PARITY
                    // Default/Opinion/Message -> Gray/Neutral
                    let Icon = MessageSquare;
                    let iconColor = "text-gray-500";
                    let iconBg = "bg-gray-50 border-gray-200";
                    let cardBorder = "border-l-4 border-l-gray-300";

                    // Fase -> Admin uses Blue for 'fase_inicio' or standard phases
                    if (mov.status_tipo === 'fase' || mov.status_tipo === 'fase_inicio') {
                        Icon = CheckCircle2;
                        iconColor = "text-blue-600";
                        iconBg = "bg-blue-50 border-blue-200";
                        cardBorder = "border-l-4 border-l-blue-500";
                    }

                    // Documento -> Admin uses Green for docs
                    if (mov.status_tipo === 'documento') {
                        Icon = FileText;
                        iconColor = "text-green-600";
                        iconBg = "bg-green-50 border-green-200";
                        cardBorder = "border-l-4 border-l-green-500";
                    }

                    // Upload -> Purple (Modern addition)
                    if (mov.status_tipo === 'upload') {
                        Icon = UploadCloud;
                        iconColor = "text-purple-600";
                        iconBg = "bg-purple-50 border-purple-200";
                        cardBorder = "border-l-4 border-l-purple-500";
                    }

                    // Modern Date Formatting
                    const dateObj = new Date(mov.data_movimento);
                    const day = dateObj.getDate();
                    const month = dateObj.toLocaleString('pt-BR', { month: 'short' }).toUpperCase();
                    const year = dateObj.getFullYear();

                    return (
                        <div key={mov.id || index} className="relative z-10">

                            <div className="flex items-start gap-4 md:gap-6 group">
                                {/* Date Bubble (Mobile/Compact) */}
                                <div className="flex flex-col items-center shrink-0 w-[50px] md:w-[60px] pt-1">
                                    <div className={`w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center border-4 border-white shadow-sm transition-transform group-hover:scale-110 ${iconBg} ${iconColor}`}>
                                        <Icon size={18} strokeWidth={2.5} />
                                    </div>
                                    <div className="mt-2 text-center">
                                        <span className="block text-xs font-bold text-gray-500">{day} {month}</span>
                                        <span className="block text-[10px] text-gray-300 transition-colors group-hover:text-vilela-primary">{year}</span>
                                    </div>
                                </div>

                                {/* Content Card */}
                                <div className={`flex-1 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow ${cardBorder}`}>
                                    <div className="flex justify-between items-start mb-2">
                                        <h5 className="font-bold text-gray-800 text-sm md:text-base">{mov.titulo_fase}</h5>
                                        {/* Badge for Type */}
                                        <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded-full ${iconBg.replace('border-', '')} ${iconColor}`}>
                                            {mov.status_tipo === 'fase' ? 'Andamento' : mov.status_tipo}
                                        </span>
                                    </div>
                                    <p className="text-sm text-gray-600 leading-relaxed font-medium">
                                        {mov.descricao}
                                    </p>
                                </div>
                            </div>
                        </div>
                    )
                })}
            </div>
        </div>
    )
}
