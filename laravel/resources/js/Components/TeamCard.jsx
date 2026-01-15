import { Link } from "@inertiajs/react";


export default function TeamCard({ 
    title = "RAID Aventure Alpine",
    image = "/SVGS/users.svg",
    size = "1",
    capacity = "10",
    route = "#",
    isManager = false,
    onClick = null,
    selected = false
}) {

    const baseClasses = "bg-green-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer w-full";
    const selectedClasses = selected ? "ring-2 ring-green-500" : "";

    const content = (
        <div className={`${baseClasses} ${selectedClasses}`}>
            <div className="p-4 flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-bold text-green-900 mb-1 flex items-center gap-2">
                        <p>{title}</p>
                        {isManager && <span className="ml-2 text-xs font-semibold text-white bg-green-600 px-2 py-1 rounded-full flex items-center justify-center leading-none mt-0.5">( Manager )</span>}
                        {typeof paid !== 'undefined' && (
                            <span className={`ml-2 text-xs font-semibold px-2 py-1 rounded-full ${paid ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'}`}>
                                {paid ? 'Payé' : 'Non payé'}
                            </span>
                        )}
                    </h3>
                </div>

                <div className="flex items-center gap-3">
                    <img
                        src={image}
                        alt={title}
                        className="w-5 h-5 object-cover rounded-md"
                    />
                    <div className="text-right">
                        <div className="text-sm font-semibold text-green-900">{size}/{capacity}</div>
                    </div>
                </div>
            </div>
        </div>
    );

    // Render as a clickable button/div if onClick provided, otherwise use Link for navigation
    if (onClick) {
        return (
            <button type="button" onClick={onClick} className="block w-full text-left">
                {content}
            </button>
        );
    }

    return (
        <Link href={route} className="block w-full">
            {content}
        </Link>
    );
} 