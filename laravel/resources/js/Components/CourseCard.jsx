import { Link } from "@inertiajs/react";

// Safe date parser and formatter
const parseDate = (s) => {
    if (!s) return null;
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return new Date(s + 'T00:00:00');
    const d = new Date(s);
    return isNaN(d.getTime()) ? null : d;
};

const formatDate = (s) => {
    const d = parseDate(s);
    return d ? d.toLocaleDateString('fr-FR') : (s ?? '');
};

const formatTime = (t) => {
    if (!t) return '';
    if (t.indexOf(':') >= 0) return t.split(':').slice(0, 2).join(':');
    return t;
};

export default function CourseCard({
    title = "Pas de titre de la course",
    image = "/Images/Card/card_image_placeholder.jpg",
    date = "03/15/2025",
    heure_debut = "10:00",
    route = "/course-detail/1/1",
    nb_equipes_inscrites = 0,
    nb_equipes_max = 0,
    nb_coureurs_max = 0,
    nb_coureurs_inscrits = 0
}) {


    return (
        <Link href={route} className="block w-full">


            <div className="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer max-w-sm mx-auto">
                {/* Image Section */}
                <div className="relative h-48 overflow-hidden">
                    <img
                        src={image}
                        alt={title}
                        className="w-full h-full object-cover"
                    />

                </div>

                {/* Content Section */}
                <div className="p-4">
                    {/* Title */}
                    <h3 className="text-lg font-bold text-gray-900 mb-2">
                        {title}
                    </h3>

                    {/* Info Grid */}
                    <div className="grid grid-cols-2 gap-3 text-xs text-gray-500">
                        {/* Date */}
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/calendar.svg" alt="Calendar" className="w-4 h-4" />
                            <span>{formatDate(date)}</span>
                        </div>

                        {/* Time */}
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/clock.svg" alt="Time" className="w-4 h-4 " />
                            <span className="truncate">{formatTime(heure_debut)}</span>
                        </div>

                        {/*capacity information*/}
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/users.svg" alt="Capacity" className="w-4 h-4" />
                            <span>{nb_equipes_inscrites} / {nb_equipes_max}</span>
                        </div>

                        {/*capacity information*/}
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/user.svg" alt="Capacity" className="w-4 h-4" />
                            <span>{nb_coureurs_inscrits} / {nb_coureurs_max}</span>
                        </div>
                    </div>
                </div>
            </div>
        </Link>
    );
}