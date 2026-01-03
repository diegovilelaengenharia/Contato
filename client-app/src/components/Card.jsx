export default function Card({ title, children, className = "" }) {
    return (
        <div className={`bg-white rounded-2xl shadow-sm border border-vilela-border overflow-hidden ${className}`}>
            {title && (
                <div className="px-6 py-4 border-b border-vilela-border bg-gray-50/30">
                    <h3 className="font-bold text-gray-800 text-sm tracking-wide uppercase">{title}</h3>
                </div>
            )}
            <div className="p-6">
                {children}
            </div>
        </div>
    )
}
