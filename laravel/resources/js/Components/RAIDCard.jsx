import { Link } from "@inertiajs/react";

// safe parse and format for dates (handles YYYY-MM-DD and ISO strings)
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

/*Helper function to see if the RAID is in a long time or not to adjust for the pill color and text value*/
function getPillValue(date) {
    //get current date
    const currentDate = new Date();
    //creates a date from the date of the RAID in a string format
    const RAIDDate = parseDate(date);

    //get the diffference in milliseconds between the two dates
    let timeDiff = RAIDDate - currentDate;

    //convert milliseconds to days
    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

    if (daysDiff < 0) {
        return { color: 'bg-gray-600', text: "Passé" }; // RAID is in the past
    }

    if (daysDiff === 0) {
        return { color: 'bg-red-600', text: "Aujourd'hui" }; // RAID is today
    }

    //if the time difference is more than 30 days away,  blue color, else  red color
    const color = daysDiff > 30 ? 'bg-blue-600' : 'bg-red-600';
    const text = daysDiff > 30 ? '+ D\'un mois' : 'Bientot !';

    return { color, text };
};

export default function RAIDCard({
    title = "RAID Aventure Alpine",
    image = "/Images/Card/card_image_placeholder.jpg",
    date = "03/15/2026",
    location = "Forest Name, Allemagne",
    route = "#",
    age_min = "NA",
    age_max = "NA",
    nb_courses = 0
}) {

    //get pill value
    const { color, text } = getPillValue(date);

    return (
        <Link href={route} className="block w-full h-full">



            <div className="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer h-full flex flex-col">
                {/* Image Section */}
                <div className="relative h-48 overflow-hidden">
                    <img
                        src={image}
                        alt={title}
                        className="w-full h-full object-cover"
                    />
                    {/*time pill*/}
                    <div className={`absolute top-3 right-3 ${color} text-white text-xs font-semibold px-3 py-1 rounded-full`}>
                        {text}
                    </div>

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

                        {/* Location */}
                        <div className="flex items-center gap-1.5">
                            <img src="/SVGS/location.svg" alt="Location" className="w-4 h-4" />
                            <span className="truncate">{location}</span>
                        </div>


                        {nb_courses !== null && nb_courses !== 0 ? (
                            <>
                                {/* Age Range */}
                                <div className="flex items-center gap-1.5">
                                    {age_min === null || age_max === null ? (
                                        <span className="truncate">
                                            Âge non spécifié
                                        </span>
                                    ) : (
                                        <span className="truncate">
                                            {`De ${age_min} à ${age_max} ans`}
                                        </span>
                                    )}
                                </div>

                                {/* Number of Courses */}
                                <div className="flex items-center gap-1.5">
                                    <span>&nbsp;{nb_courses} course{nb_courses > 1 ? 's' : ''}</span>
                                </div>
                            </>
                        ) : (
                            <div className="flex items-center gap-1.5">
                                <span>aucunes courses organisés</span>
                            </div>
                        )}

                    </div>
                </div>
            </div>
        </Link>
    );
}