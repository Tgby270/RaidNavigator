import MainLayout from "../../Layout/MainLayout";
import CourseCard from "../../Components/CourseCard";
import { useState } from "react";
import { Link } from "@inertiajs/react";

//fetch all data of a specific RAID from the database


export default function RaidDetail({ raid, courses }) {
    const [searchQuery, setSearchQuery] = useState("");

    const formatDate = (s) => {
        if (!s) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
            return new Date(s + 'T00:00:00').toLocaleDateString('fr-FR');
        }
        const d = new Date(s);
        if (isNaN(d.getTime())) return s;
        return d.toLocaleDateString('fr-FR');
    };


    console.log("RaidDetail props:", { raid });
    console.log("Courses props:", { courses });

    let img = raid.raid_image ? `/Images/Card/${raid.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop";

    if (!raid) {
        return (
            <MainLayout>
                <div className="flex justify-center items-center h-64">
                    <p className="text-gray-500">Il n'y a pas de raid à afficher.</p>
                </div>
            </MainLayout>
        );
    }

    return (
        <MainLayout>

            <div className="relative h-64 overflow-hidden rounded-2xl mb-2">
                <img
                    src={raid.raid_image ? `/Images/Card/${raid.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"}
                    alt={raid.raid_nom}
                    className="w-full h-full object-cover"
                    onError={(e) => {
                        e.target.src = "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop";
                    }}
                />
                <div className="absolute inset-0 bg-black/40"></div>
                <div className="absolute inset-0 flex flex-col justify-center px-8">
                    <h1 className="text-4xl font-bold text-white mb-2">{raid.raid_nom}</h1>
                    <h2 className="text-white/90 text-3xl font-semibold text-sm">{raid.raid_lieu}</h2>
                </div>
            </div>

            {/* Event Details Section */}
            <div className="bg-gray-100 py-4 px-8 rounded-2xl mb-2">
                <h2 className="text-sm font-semibold mb-2">DÉTAILS DE L'ÉVÉNEMENT</h2>
                <div className="flex flex-wrap gap-6 text-sm text-gray-700">
                    <div className="flex items-center gap-2">
                        <img src="/SVGS/calendar.svg" alt="Calendar" className="w-4 h-4" />
                        <span><strong>Date :</strong>{formatDate(raid.raid_date_debut)}  →  {formatDate(raid.raid_date_fin)}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <img src="/SVGS/location.svg" alt="Location" className="w-4 h-4" />
                        <span><strong>Localisation :</strong> {raid.raid_lieu}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span><strong>Contact :</strong> {raid.raid_contact}</span>
                    </div>

                    <div className="flex items-center gap-2">
                        <img src="/SVGS/calendar.svg" alt="calendar" className="w-4 h-4" />
                        <span><strong>Dates d'inscription</strong> {formatDate(raid.date_debut_inscription)}  →  {formatDate(raid.date_fin_inscription)}</span>
                    </div>
                </div>
            </div>

            <Link
                href="/test"
                className="inline-flex items-center px-4 py-1 bg-gray-200 text-sm font-medium rounded-full hover:bg-gray-300 transition-colors"
            >
                <span className="mr-2">←</span> Retour
            </Link>

            {/* Course Cards Section */}
            <div className="py-8 px-8 bg-white">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl">
                    {courses && courses.map((course, index) => {
                        let date_debut = course.CRS_DATE_HEURE_DEPART.split(" ")[0];
                        let heure_debut = course.CRS_DATE_HEURE_DEPART.split(" ")[1];
                        return (
                            <CourseCard
                                key={`${course.RAID_ID}-${course.CRS_ID}` || index}
                                title={course.CRS_NOM}
                                image={img}
                                date={date_debut}
                                heure_debut={heure_debut}
                                location={raid.raid_lieu}
                                distance={course.CRS_DISTANCE}
                                nb_coureurs_inscrits={course.nb_members}
                                nb_coureurs_max={course.CRS_MAX_PARTICIPANTS}
                                nb_equipes_inscrites={course.nb_equipes}
                                nb_equipes_max={course.CRS_NB_EQUIPE_MAX}
                                route={`/course-detail/${course.CRS_ID}/${raid.RAID_ID}`}
                            />
                        );
                    })}
                </div>
            </div>
        </MainLayout>
    );
}
