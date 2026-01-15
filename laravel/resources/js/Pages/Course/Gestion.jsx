import MainLayout from "../../Layout/MainLayout";
import CourseCard from "../../Components/CourseCard";
import ResultsTable from "../../Components/ResultsTable";
import { Link } from '@inertiajs/react';

import { useState } from 'react';
import axios from 'axios';
import { usePage } from '@inertiajs/react';

export default function CourseDetails({course_id, raid_id, course = null, raid = null, teams = [], userTeam = null, userTeamMembers = [], userTeamManager = null, isInTeam = false, isManager = false, results = []}) {
    const [selectedTeam, setSelectedTeam] = useState(null);
    const [csvFile, setCsvFile] = useState(null);
    const [uploading, setUploading] = useState(false);
    const [uploadMessage, setUploadMessage] = useState('');

    function isRegistrationOpen() {
        if (!raid || !raid.date_debut_inscription || !raid.date_fin_inscription) return false;
        const now = new Date();
        const start = new Date(raid.date_debut_inscription);
        const end = new Date(raid.date_fin_inscription);
        return now >= start && now <= end;
    }
    const [selectedManager, setSelectedManager] = useState(null);
    const [selectedMembers, setSelectedMembers] = useState([]);
    const [loadingLeft, setLoadingLeft] = useState(false);
    const { auth } = usePage().props;
    const currentUser = auth?.user;
    const [teamsState, setTeamsState] = useState(teams);

    // Local white-style team item used only on the Gestion page so we don't modify shared `TeamCard`.
    function LeftTeamItem({ team, capacity, selected, onClick }) {
        return (
            <button onClick={onClick} className={`w-full text-left bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow p-3 ${selected ? 'ring-2 ring-green-500' : ''}`}>
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-md font-semibold text-gray-900">{team.EQU_NOM}</h3>
                    </div>
                    <div className="text-sm text-gray-700">{team.membres_count}/{capacity ?? '—'}</div>
                </div>
                <div className="mt-2 text-right">
                    <span className={`text-xs font-semibold px-2 py-1 rounded ${team.dossier_complet ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'}`}>
                        {team.dossier_complet ? 'Dossier complet' : 'Dossier incomplet'}
                    </span>
                </div>
            </button>
        );
    }

    async function handleSelectTeam(team) {
        // Temporarily set selection for immediate UI feedback; we'll replace with full data from API
        setSelectedTeam(team);
        setSelectedManager(null);
        setSelectedMembers([]);
        setLoadingLeft(true);
        try {
            const res = await fetch(`/equipe/${team.EQU_ID}/details`);
            if (!res.ok) {
                setSelectedManager(null);
                setSelectedMembers([]);
                setLoadingLeft(false);
                return;
            }
            const data = await res.json();
            // Use the detailed team returned by the API (includes USE_ID manager id)
            if (data.team) {
                setSelectedTeam(data.team);
            }
            setSelectedManager(data.manager);
            setSelectedMembers(data.members || []);
        } catch (err) {
            console.error(err);
        } finally {
            setLoadingLeft(false);
        }
    }

    const handleFileChange = (file) => {
        setCsvFile(file);
    };

    const handleCsvUpload = async (e) => {
        e.preventDefault();
        if (!csvFile) {
            setUploadMessage('Veuillez sélectionner un fichier CSV');
            return;
        }

        setUploading(true);
        const formData = new FormData();
        formData.append('csv_file', csvFile);
        formData.append('raid_id', raid_id);
        formData.append('course_id', course_id);

        try {
            const token = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : null;
            const response = await fetch('/results/import', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                }
            });

            const data = await response.json();
            console.log('Upload response:', data);
            
            if (response.ok) {
                setUploadMessage(`✓ ${data.message}`);
                setCsvFile(null);
                // Reload page to show new results
                setTimeout(() => window.location.reload(), 1000);
            } else {
                // Show more detailed error message if debug_info exists
                let errorMsg = data.message;
                if (data.debug_info) {
                    errorMsg += ` | CSV rows: ${data.debug_info.csv_rows_count} | Registered teams: ${JSON.stringify(data.debug_info.registered_teams)} | CSV teams: ${JSON.stringify(data.debug_info.csv_team_names)}`;
                }
                setUploadMessage(`✗ Erreur: ${errorMsg}`);
            }
        } catch (error) {
            setUploadMessage(`✗ Erreur lors de l'upload: ${error.message}`);
        } finally {
            setUploading(false);
        }
    }

    return (
        <MainLayout> 
            <div className="relative h-64 overflow-hidden rounded-2xl mb-2">
                <img 
                    src={course?.raid_image ? `/Images/Card/${course.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"}
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

            <div className="md:grid md:grid-cols-2 md:gap-8">

                <aside className="md:col-span-1 mt-4 md:mt-0 h-full">
                    <div className="bg-gray-50 p-4 rounded-lg shadow-sm h-full flex flex-col">
                        <h3 className="text-lg font-bold text-center text-gray-900 mb-4">Les équipes</h3>
                        <div className="space-y-3 flex-1">
                                {teamsState.length > 0 ? (
                                teamsState.map((team) => (
                                    <LeftTeamItem
                                        key={team.EQU_ID}
                                        team={team}
                                        capacity={course?.CRS_MAX_PARTICIPANTS_EQUIPE}
                                        selected={selectedTeam && selectedTeam.EQU_ID === team.EQU_ID}
                                        onClick={() => handleSelectTeam(team)}
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
                        {selectedTeam ? (
                            <div>
                                <h3 className="text-lg font-bold text-center text-gray-900 mb-4">{selectedTeam.EQU_NOM}</h3>

                                {typeof selectedTeam?.EQU_EST_PAYEE !== 'undefined' && (
                                    <div className="mb-2 text-center">
                                        <span className={`inline-flex items-center px-2 py-1 text-xs font-semibold rounded ${selectedTeam.EQU_EST_PAYEE ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'}`}>
                                            {selectedTeam.EQU_EST_PAYEE ? 'Payé' : 'Non payé'}
                                        </span>
                                    </div>
                                )}

                                {/* If team is not paid, allow marking as paid */}
                                {!selectedTeam?.EQU_EST_PAYEE && (
                                    <div className="mb-2 text-center">
                                        <button onClick={async () => {
                                            if (!confirm('Voulez‑vous marquer cette équipe comme payée ?')) return;
                                            try {
                                                const raid = selectedTeam.RAID_ID ?? selectedTeam.raid_id ?? '';
                                                const crs = selectedTeam.CRS_ID ?? selectedTeam.crs_id ?? '';
                                                const equ = selectedTeam.EQU_ID ?? selectedTeam.equ_id ?? '';
                                                const res = await axios.post(`/equipe/${raid}/${crs}/${equ}/markPaid`);
                                                const updated = res.data.team;
                                                setSelectedTeam(prev => ({ ...prev, ...updated }));
                                                setTeamsState(prev => prev.map(t => t.EQU_ID === updated.EQU_ID ? { ...t, EQU_EST_PAYEE: updated.EQU_EST_PAYEE, dossier_complet: (updated.dossier_complet ?? (updated.EQU_EST_PAYEE && t.membres_count > 0 && t.incomplete_membres_count == 0)) } : t));
                                            } catch (err) {
                                                console.error(err);
                                                alert('Erreur lors de la mise à jour du statut de paiement.');
                                            }
                                        }} className="inline-block mt-2 px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded">L'équipe a payée</button>
                                    </div>
                                )}

                                {selectedManager && (
                                    <div className="mb-3 p-2 bg-green-100 rounded border border-green-300">
                                        <div className="text-sm font-semibold text-green-900">Manager: {selectedManager.nom} {selectedManager.prenom}</div>
                                    </div>
                                )}

                                

                                <div className="space-y-3">
                                    {selectedMembers.length > 0 ? (
                                        selectedMembers.map((member) => {
                                            const dossierValide = (member.statut !== 'en_attente') && ((member.pps && member.pps.trim() !== '') || (member.licence && member.licence.trim() !== ''));
                                            return (
                                                <div key={member.id} className="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                                                    <div className="flex items-center gap-3">
                                                        <p className="text-sm font-medium text-gray-900">{member.nom} {member.prenom}</p>
                                                        {!dossierValide ? (
                                                            <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded border border-red-300">Dossier incomplet</span>
                                                        ) : (
                                                            <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded border border-green-300">Dossier valide</span>
                                                        )}
                                                    </div>

                                                    <div className="flex items-center gap-2">
                                                        <button type="button" onClick={async() => {
                                                            if (!confirm('Voulez‑vous vraiment supprimer ce membre ?')) return;
                                                            try {
                                                                const raid = selectedTeam.RAID_ID ?? selectedTeam.raid_id ?? '';
                                                                const crs = selectedTeam.CRS_ID ?? selectedTeam.crs_id ?? '';
                                                                const equ = selectedTeam.EQU_ID ?? selectedTeam.equ_id ?? '';
                                                                await axios.post(`/equipe/deleteMember/${member.id}`, { RAID_ID: raid, CRS_ID: crs, EQU_ID: equ, _method: 'DELETE' });
                                                                setSelectedMembers(prev => prev.filter(m => m.id !== member.id));
                                                                // full refresh to update team counts/dossier flags
                                                                window.location.reload();
                                                            } catch (err) {
                                                                console.error(err);
                                                                alert('Erreur lors de la suppression du membre.');
                                                            }
                                                        }} className="text-red-600 font-bold text-sm">Supprimer</button>
                                                    </div>
                                                </div>
                                            );
                                        })
                                    ) : (
                                        <p className="text-center text-gray-500">Aucun membre dans cette équipe.</p>
                                    )}
                                </div>

                                {/* Team delete button centered below members */}
                                <div className="mt-4 w-full flex justify-center">
                                    <button onClick={async() => {
                                        if (!confirm('Voulez‑vous vraiment supprimer cette équipe ? Cette action est irréversible.')) return;
                                        try {
                                            const raid = selectedTeam.RAID_ID ?? selectedTeam.raid_id ?? '';
                                            const crs = selectedTeam.CRS_ID ?? selectedTeam.crs_id ?? '';
                                            const equ = selectedTeam.EQU_ID ?? selectedTeam.equ_id ?? '';
                                            await axios.post(`/equipe/${raid}/${crs}/${equ}`, { _method: 'DELETE' });
                                            window.location.reload();
                                        } catch (err) {
                                            console.error(err);
                                            alert('Erreur lors de la suppression de l\'équipe.');
                                        }
                                    }} className="text-red-600 font-bold">Supprimer l'équipe</button>
                                </div>

                            
                            </div>
                        ) : (
                            false ? (
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
                                                userTeamMembers.map((member) => {
                                                    const dossierValide = (member.statut !== 'en_attente') && ((member.pps && member.pps.trim() !== '') || (member.licence && member.licence.trim() !== ''));
                                                    return (
                                                        <div key={member.id} className="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                                                            <div className="flex items-center gap-3">
                                                                <p className="text-sm font-medium text-gray-900">{member.nom} {member.prenom}</p>
                                                                {userTeam && member.id === userTeam.USE_ID && (
                                                                    <span className="text-xs font-semibold text-white bg-green-600 px-2 py-1 rounded-full">Manager</span>
                                                                )}
                                                                {!dossierValide ? (
                                                                    <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded border border-red-300">Dossier incomplet</span>
                                                                ) : (
                                                                    <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded border border-green-300">Dossier valide</span>
                                                                )}
                                                            </div>
                                                        </div>
                                                    );
                                                })
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
                                    <p className="text-gray-600 mb-4 text-center">Choisissez une équipe</p>
                                </div>
                            )
                        )}
                    </div>
                </aside>
            </div>

            {/* CSV Import Section */}
            <div className="mt-8 mb-8 bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                <h3 className="text-lg font-bold text-blue-900 mb-4">Importer les résultats (CSV)</h3>
                <form onSubmit={handleCsvUpload} className="flex flex-col gap-4">
                    <div className="flex items-center gap-4">
                        <input
                            type="file"
                            accept=".csv"
                            onChange={(e) => handleFileChange(e.target.files?.[0] || null)}
                            className="block flex-1 text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100"
                            disabled={uploading}
                        />
                        <button
                            type="submit"
                            disabled={uploading || !csvFile}
                            className="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
                        >
                            {uploading ? 'Upload...' : 'Upload'}
                        </button>
                    </div>

                    {uploadMessage && (
                        <p className={`text-sm font-medium ${uploadMessage.startsWith('✓') ? 'text-green-700' : 'text-red-700'}`}>
                            {uploadMessage}
                        </p>
                    )}
                    <p className="text-xs text-gray-600">Format CSV attendu : CLT, PUCE, EQUIPE, CATEGORIE, TEMPS (en secondes), POINTS (optionnel). La prévisualisation ci-dessous se mettra à jour avec votre fichier.</p>
                </form>
            </div>

            {/* Results Table */}
            <div className="mt-8">
                {console.log(results)}
                <ResultsTable results={results ?? []} />
            </div>

        
        </MainLayout>
    );
}