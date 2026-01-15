import { Link } from "@inertiajs/react";

export default function ClubCard({
    name = "Club Aventure Alpine",
    nbMembers = "—",
    location = "",
    responsable = "",
    route = "#",
    image = "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop",
    pillColor = "bg-indigo-600",
    pillText = "",
}) {
    return (
        <Link href={route} className="block w-full h-full">
            <div className="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer h-full flex flex-col">
                <div className="relative h-48 overflow-hidden">
                    <img
                        src={image}
                        alt={name}
                        className="w-full h-full object-cover"
                    />
                    {pillText && (
                        <div className={`absolute top-3 right-3 ${pillColor} text-white text-xs font-semibold px-3 py-1 rounded-full`}>
                            {pillText}
                        </div>
                    )}
                </div>

                <div className="p-4">
                    <h3 className="text-lg font-bold text-gray-900 mb-2">{name}</h3>
                    {responsable && <h4 className="text-sm text-gray-700 mb-2">{responsable}</h4>}

                    <div className="grid grid-cols-2 gap-3 text-xs text-gray-500">
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/users.svg" alt="Members" className="w-4 h-4" />
                            <span>{nbMembers}</span>
                        </div>

                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/location.svg" alt="Location" className="w-4 h-4" />
                            <span className="truncate">{location}</span>
                        </div>
                    </div>
                </div>
            </div>
        </Link>
    );
}