import MainLayout from "../../Layout/MainLayout";
import { useEffect, useState } from "react";
import { Link } from '@inertiajs/react';
import RAIDCard from "../../Components/RAIDCard";
import CourseCard from "../../Components/CourseCard";
import ClubCard from "../../Components/Dashboard/ClubCard";
import SmallCard from '@/Components/SmallCard';

export default function Dashboard({
    nb_raids,
    raids_responsible,
    club_responsible,
    site_responsible,
    courses_responsible = [],
    clubs = [],
    numberRaid = 0,
    numberRaidYear = 0,
    number_of_user = 0,
    raidsClub = [],
    club_id = null,
    club_name = null,
}) {
    const [activePanel, setActivePanel] = useState('responsible'); // 'users' | 'club' | 'gererSite' | 'stats' | null

    // Debug: inspect data
    if (typeof window !== 'undefined') {
        // eslint-disable-next-line no-console
        console.log('Dashboard props:', {
            raids_responsible: raids_responsible?.length || 0,
            courses_responsible: courses_responsible?.length || 0,
            club_id,
            club_name,
            club_responsible
        });
    }

    const toggle = (panel) => {
        setActivePanel(prev => (prev === panel ? prev : panel));
    };

    // const { auth } = usePage().props;
    // const user = auth?.user;

    return (
        <MainLayout>
            <div className="flex min-h-screen">
                <aside className="w-64  text-white p-6 border-r border-gray-700 ">
                    <h1 className="text-2xl font-bold mb-6 text-gray-700">Dashboard</h1>

                    <nav className="space-y-2">
                        {/* Show the raids and races that the user is responsible for */}
                        <button onClick={() => toggle('responsible')} className="block w-full text-left text-gray-700 px-4 py-2 rounded-xl border-2 border-gray-700 hover:bg-gray-100 hover:-translate-y-0.5 transition-all">
                            Vos responsabilités
                        </button>
                        
                        {site_responsible && (
                            <>
                                <button onClick={() => toggle('gererSite')} className="block w-full text-left text-gray-700 px-4 py-2 rounded-xl border-2 border-gray-700 hover:bg-gray-100 hover:-translate-y-0.5 transition-all">
                                    Gérer le site
                                </button>
                                
                                {/* Stats of the club */}
                                <button onClick={() => toggle('stats')} className="block w-full text-left text-gray-700 px-4 py-2 rounded-xl border-2 border-gray-700 hover:bg-gray-100 hover:-translate-y-0.5 transition-all">
                                    Statistiques
                                </button>
                            </>
                        )}

                        {club_responsible && (
                            <>
                                <button onClick={() => toggle('club')} className="block w-full text-left text-gray-700 px-4 py-2 rounded-xl border-2 border-gray-700 hover:bg-gray-100 hover:-translate-y-0.5 transition-all">
                                    Mon club
                                </button>
                                
                                {/* Stats of the club */}
                                <button onClick={() => toggle('stats')} className="block w-full text-left text-gray-700 px-4 py-2 rounded-xl border-2 border-gray-700 hover:bg-gray-100 hover:-translate-y-0.5 transition-all">
                                    Statistique du Club
                                </button>
                            </>
                        )}
                    </nav>
                </aside>

                <main className="flex-1 p-6">
                    <div>
                        {activePanel === 'responsible' && (
                            <div className="contentUsers">
                                <h1 className="text-2xl font-bold mb-4 text-gray-700">Vos responsabilités</h1>
                                {/* Show every RAID that the user is responsible for */}
                                <div className=" rounded-lg p-4 text-gray-700">
                                    {/*title*/}
                                    <h2 className="font-semibold text-lg mb-4">RAIDs vous étant attribués :</h2>

                                    {/*list of raids*/}
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 items-stretch justify-items-center">
                                        {raids_responsible.length === 0 ? (
                                            <p className="text-gray-500">Il n'y a aucun raid dont vous êtes responsable.</p>
                                        ) : (
                                            raids_responsible.map((raid, idx) => (
                                                <div key={raid?.RAID_ID ?? `raid-${idx}`} className="w-full max-h-64 relative group">
                                                    <RAIDCard
                                                        title={raid.raid_nom}
                                                        image={raid.raid_image ? `/Images/Card/${raid.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"}
                                                        date={new Date(raid.raid_date_debut).toLocaleDateString('fr-FR') + " - " + new Date(raid.raid_date_fin).toLocaleDateString('fr-FR')}
                                                        location={raid.raid_lieu}
                                                        route={`/raid/manage/${raid.RAID_ID}`}
                                                    />
                                                </div>
                                            ))
                                        )}
                                    </div>
                                </div>
                                {/* Show every race that the user is responsible for */}
                                <div className=" rounded-lg p-4 text-gray-700">
                                    <h2 className="font-semibold text-lg mb-4">Courses vous étant attribuées :</h2>

                                    {/*list of courses*/}
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 items-stretch justify-items-center">
                                        {courses_responsible.length === 0 ? (
                                            <p className="text-gray-500">Il n'y a aucune course dont vous êtes responsable.</p>
                                        ) : (
                                            courses_responsible.map((course, index) => {
                                                const date_debut = course.CRS_DATE_HEURE_DEPART.split(" ")[0];
                                                const heure_debut = course.CRS_DATE_HEURE_DEPART.split(" ")[1];
                                                const mapKey = (course && course.RAID_ID && course.CRS_ID) ? `${course.RAID_ID}-${course.CRS_ID}` : `course-${index}`;
                                                return (
                                                    <div key={mapKey} className="w-full max-h-64 relative group">
                                                        
                                                        <CourseCard
                                                            title={course.CRS_NOM}
                                                            image={`/Images/Card/${course.raid_image}`}
                                                            date={date_debut}
                                                            heure_debut={heure_debut}
                                                            location={course.raid_lieu}
                                                            distance={course.CRS_DISTANCE}
                                                            nb_actual_team={course.nb_actual_team}
                                                            capacity={course.CRS_MAX_PARTICIPANTS}
                                                            route={`/course-detail/${course.CRS_ID}/${course.RAID_ID}/files`}
                                                        />
                                                        {/* Edit button overlay */}
                                                        <Link 
                                                            href={`/course/${course.RAID_ID}/${course.CRS_ID}/edit`}
                                                            className="absolute top-2 left-2 bg-white hover:bg-gray-100 text-gray-700 p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 ease-in-out z-10"
                                                            title="Modifier la course"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </Link>
                                                    </div>
                                                );
                                            })
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {activePanel === 'club' && (
                            <div>
                                <div>
                                    <p className="text-gray-700 font-semibold text-lg">{club_name || 'Non défini'}</p>
                                    <SmallCard title="Nombre de raids organisé par le club:" body={numberRaid || 0} />
                                    <SmallCard title="Nombre de raids organisé par le club sur l'année en cours:" body={numberRaidYear || 0} />
                                    <SmallCard title="Nombre d'utilisateurs du club:" body={number_of_user || 0} />


                                    <Link className="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg" href={club_id ? `/CreateRaid?club_id=${club_id}` : '/CreateRaid'}>
                                            Créer Raid
                                    </Link>
                                </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 items-stretch justify-items-center">
                                        {raidsClub.length == 0 ? (
                                            <p className="text-gray-500">Il n'y a aucun raid dont vous êtes responsable.</p>
                                        ) : (
                                            raidsClub.map((raid, idx) => (
                                                <div key={raid?.RAID_ID ?? `raidsClub-${idx}`} className="w-full max-h-64 relative group">
                                                    <RAIDCard
                                                        title={raid.raid_nom}
                                                        image={raid.raid_image ? `/Images/Card/${raid.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"}
                                                        date={new Date(raid.raid_date_debut).toLocaleDateString('fr-FR') + " - " + new Date(raid.raid_date_fin).toLocaleDateString('fr-FR')}
                                                        location={raid.raid_lieu}
                                                        route={`/raid/manage/${raid.RAID_ID}`}
                                                    />
                                                </div>
                                            ))
                                        )}
                                    </div>
    </div>
                        )}

                        {activePanel === 'gererSite' && (
                            <div className="contentRaids">
                                <div className="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {clubs.length === 0 ? (
                                        <p className="text-gray-500">Aucun club trouvé.</p>
                                    ) : (
                                        clubs.map((club, idx) => (
                                            <ClubCard
                                                key={club?.CLU_ID ?? `club-${idx}`}
                                                name={club.CLU_NOM}
                                                nbMembers={club.nb_members ?? '—'}
                                                location={`${club.CLU_ADRESSE} — ${club.CLU_VILLE} ${club.CLU_CODE_POSTAL}`}
                                                responsable={club.USE_ID ? `Responsable #${club.USE_ID}` : ''}
                                                route={`/club/${club.CLU_ID}/edit`}
                                            />
                                        ))
                                    )}
                                </div>

                                <div className="mt-6">
                                    <Link href="/CreateClub" className="bg-green-500 text-white px-4 py-2 rounded-lg inline-block">Créer un club</Link>
                                </div>
                            </div>
                        )}

                        {activePanel === 'stats' && (
                            <div className="contentStats text-gray-700 bg-gray-200 rounded-lg p-4">
                                Voici le contenu affiché quand <strong>Stats</strong> est actif.
                            </div>
                        )}
                    </div>
                </main>
            </div>
        </MainLayout>
    );
}