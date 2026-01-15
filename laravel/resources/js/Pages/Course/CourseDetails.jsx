import MainLayout from "../../Layout/MainLayout";
import CourseCard from "../../Components/CourseCard";
import { useState } from "react";
import { Link } from '@inertiajs/react';
import TeamCard from "../../Components/TeamCard";
import ResultsTable from "../../Components/ResultsTable";

export default function CourseDetails({course_id, raid_id, course = null, raid = null, teams = [], userTeam = null, userTeamMembers = [], userTeamManager = null, isInTeam = false, isManager = false, results = []}) {

    function isRegistrationOpen() {
        if (!raid || !raid.date_debut_inscription || !raid.date_fin_inscription) return false;
        const now = new Date();
        const start = new Date(raid.date_debut_inscription);
        const end = new Date(raid.date_fin_inscription);
        return now >= start && now <= end;
    }

    return (
        <MainLayout>
            <div className="relative h-64 overflow-hidden rounded-2xl mb-2">
                <img
                    src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"
                    alt={course?.CRS_NOM || "Course"}
                    className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-black/40"></div>
                <div className="absolute inset-0 flex flex-col justify-center px-8">
                    <h1 className="text-4xl font-bold text-white mb-2">{course?.CRS_NOM || "Course"}</h1>
                    <p className="text-white/90 text-sm">{course?.CRS_TYPE || "Description de la course"}</p>
                </div>
            </div>

            {/* Event Details Section */}
            <div className="bg-gray-100 py-4 px-8 rounded-2xl mb-2">
                <h2 className="text-sm font-semibold mb-2">Détails de l'événement</h2>
                <div className="flex flex-wrap gap-6 text-sm text-gray-700">
                    <div className="flex items-center gap-2">
                        <img src="/SVGS/calendar.svg" alt="Calendar" className="w-4 h-4" />
                        <span><strong>Date :</strong> {course?.CRS_DATE_HEURE_DEPART ? new Date(course.CRS_DATE_HEURE_DEPART).toLocaleDateString('fr-FR') : '—'} → {course?.CRS_DATE_HEURE_FIN ? new Date(course.CRS_DATE_HEURE_FIN).toLocaleDateString('fr-FR') : '—'}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <img src="/SVGS/location.svg" alt="Location" className="w-4 h-4" />
                        <span><strong>Difficulté :</strong> {course?.CRS_DIFFICULTE || '—'}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong>Durée :</strong> {course?.CRS_DUREE ? `${course.CRS_DUREE}h` : '—'}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span><strong>Participants :</strong> {course?.CRS_MIN_PARTICIPANTS || '—'} - {course?.CRS_MAX_PARTICIPANTS || '—'}</span>
                    </div>
                </div>
            </div>

            <Link
                href={`/raid/${raid_id}`}
                className="inline-flex items-center px-4 py-1 bg-gray-200 text-sm font-medium rounded-full hover:bg-gray-300 transition-colors"
            >
                <span className="mr-2">←</span> Retour
            </Link>

            <div className="md:grid md:grid-cols-2 md:gap-8">

                <aside className="md:col-span-1 mt-4 md:mt-0 h-full">
                    <div className="bg-gray-50 p-4 rounded-lg shadow-sm h-full flex flex-col">
                        <h3 className="text-lg font-bold text-center text-gray-900 mb-4">Les équipes</h3>
                        <div className="space-y-3 flex-1">
                            {teams.length > 0 ? (
                                teams.map((team) => (
                                    <TeamCard
                                        key={team.EQU_ID}
                                        title={team.EQU_NOM}
                                        capacity={course?.CRS_MAX_PARTICIPANTS_EQUIPE}
                                        size={team.membres_count}
                                        route={`#`}
                                    />
                                ))
                            ) : (
                                <p className="text-center text-gray-500">Aucune équipe inscrite pour cette course.</p>
                            )}
                        </div>
                    </div>
                </aside>

                <aside className="md:col-span-1 mt-4 md:mt-0 h-full">
                    <div className="bg-gray-50 p-4 rounded-lg shadow-sm h-full flex flex-col justify-between">
                        {isInTeam ? (
                            <>
                                <div>
                                    <h3 className="text-lg font-bold text-center text-gray-900 mb-4">Mon équipe</h3>
                                    {userTeam && (
                                        <div className="mb-4">
                                            <p className="text-center text-sm font-semibold text-gray-700">{userTeam.EQU_NOM}</p>
                                        </div>
                                    )}
                                    {userTeamManager && (
                                        <div className="mb-3 p-2 bg-green-100 rounded border border-green-300">
                                            <div className="text-sm font-semibold text-green-900">Manager: {userTeamManager.nom} {userTeamManager.prenom}</div>
                                        </div>
                                    )}
                                    <div className="space-y-3">
                                        {userTeamMembers.length > 0 ? (
                                            userTeamMembers.map((member) => (
                                                <div key={member.id} className="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                                                    <div className="flex items-center gap-3">
                                                        <p className="text-sm font-medium text-gray-900">{member.nom} {member.prenom}</p>
                                                        {userTeam && member.id === userTeam.USE_ID && (
                                                            <span className="text-xs font-semibold text-white bg-green-600 px-2 py-1 rounded-full">Manager</span>
                                                        )}
                                                        {isManager && member.statut === 'en_attente' && (
                                                            <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded border border-yellow-300">
                                                                En attente de réponse
                                                            </span>
                                                        )}
                                                        {isManager && member.statut === 'accepte' && (
                                                            <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded border border-green-300">
                                                                Accepté
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            ))
                                        ) : (
                                            <p className="text-center text-gray-500">Aucun membre dans votre équipe.</p>
                                        )}
                                    </div>
                                </div>

                                {isManager && isRegistrationOpen() && (
                                    <div className="flex justify-center">
                                        <Link href={`/equipe/modify/${raid_id}/${course_id}/${userTeam.EQU_ID}`} className="inline-block mt-6 px-6 py-2.5 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 transition-colors duration-300">Modifier son équipe</Link>
                                    </div>
                                )}
                            </>
                        ) : (
                            <div className="flex flex-col items-center justify-center h-full">
                                <p className="text-gray-600 mb-4 text-center">Vous n'êtes pas encore inscrit à une équipe pour cette course.</p>
                                {isRegistrationOpen() ? (
                                    <Link href={`/equipe/create?raid_id=${raid_id}&course_id=${course_id}`} className="px-6 py-2.5 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 transition-colors duration-300">Créer une équipe</Link>
                                ) : (
                                    <p className="text-sm text-gray-500">Les inscriptions sont fermées.</p>
                                )}
                            </div>
                        )}
                    </div>
                </aside>
            </div>

            <ResultsTable results={results} />
        
        </MainLayout>
    );
}