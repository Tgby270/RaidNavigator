import MainLayout from '../../Layout/MainLayout';
import { useState } from 'react';
import axios from 'axios';

export default function ModificationEquipe({ equipe, users }) {
    equipe = equipe ?? {};
    users = users ?? [];
    
    // Get CSRF token - use a function to get fresh token
    const getCsrfToken = () => {
        // First try meta tag
        let token = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Fallback to cookie
        if (!token) {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'XSRF-TOKEN') {
                    token = decodeURIComponent(value);
                    break;
                }
            }
        }
        
        return token;
    };
    
    const csrf = getCsrfToken();
    console.log('CSRF Token:', csrf);
    
    // Configure axios defaults
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    
    const teamId = (equipe?.EQU_ID ?? equipe?.id) ?? '';
    const raidId = (equipe?.RAID_ID ?? equipe?.raid_id) ?? '';
    const crsId = (equipe?.CRS_ID ?? equipe?.crs_id) ?? '';
    const maxCapacity = equipe?.max_capacity ?? null;
    const [membersList, setMembersList] = useState(((equipe && equipe.membres) || []));
    const [selected, setSelected] = useState(membersList.map((m) => m.id));
    const [name, setName] = useState(equipe?.NOM_EQUIPE ?? '');
    
    const isTeamFull = maxCapacity && membersList.length >= maxCapacity;

    async function handleRemove(memberId) {
        if (!confirm('Voulez‑vous vraiment retirer ce membre ?')) return;
        if (!raidId || !crsId || !teamId) {
            alert('Identifiants d\'équipe manquants (RAID/CRS/EQU).');
            return;
        }
        
        if (!csrf) {
            alert('Token CSRF manquant. Veuillez rafraîchir la page.');
            return;
        }
        
        try {
            // send composite keys in body to the correct route
            const response = await axios.post(`/equipe/deleteMember/${memberId}`, {
                RAID_ID: raidId,
                CRS_ID: crsId,
                EQU_ID: teamId,
                _method: 'DELETE'
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-XSRF-TOKEN': csrf
                }
            });
            setSelected((prev) => prev.filter((p) => p !== memberId));
            setMembersList((prev) => prev.filter((m) => m.id !== memberId));
        } catch (err) {
            console.error('Error details:', err);
            console.error('Response:', err?.response);
            let msg = 'Erreur lors de la suppression du membre.';
            if (err?.response) {
                const data = err.response.data;
                if (data?.error) msg = data.error;
                else if (data?.errors) msg = Object.values(data.errors).flat().join('\n');
                else if (data?.message) msg = data.message;
            } else if (err?.message) {
                msg = err.message;
            }
            alert(msg);
        }
    }

    async function handleAdd(memberId) {
        if (!confirm('Ajouter ce membre à l\'équipe ?')) return;
        if (!raidId || !crsId || !teamId) {
            alert('Identifiants d\'équipe manquants (RAID/CRS/EQU).');
            return;
        }
        try {
            await axios.post(`/equipe/add/${memberId}`, {
                RAID_ID: raidId,
                CRS_ID: crsId,
                EQU_ID: teamId
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            });
            setSelected((prev) => (prev.includes(memberId) ? prev : [...prev, memberId]));
            // Also add the user to the local members list so the UI updates immediately
            const user = users.find((u) => u.id === memberId);
            if (user) setMembersList((prev) => [...prev, user]);
        } catch (err) {
            console.error(err);
            // Parse Laravel validation errors or generic error field
            let msg = 'Erreur lors de l\'ajout du membre.';
            if (err?.response) {
                const data = err.response.data;
                if (data?.error) msg = data.error;
                else if (data?.errors) {
                    // Flatten validation errors
                    msg = Object.values(data.errors).flat().join('\n');
                } else if (data?.message) msg = data.message;
            } else if (err?.message) {
                msg = err.message;
            }
            alert(msg);
        }
    }

    async function handleDeleteTeam() {
        if (!confirm('Voulez‑vous vraiment supprimer cette équipe ?')) return;
        if (!raidId || !crsId || !teamId) {
            alert('Identifiants d\'équipe manquants (RAID/CRS/EQU).');
            return;
        }
        
        if (!csrf) {
            alert('Token CSRF manquant. Veuillez rafraîchir la page.');
            return;
        }
        
        try {
            // call the destroy route with composite keys
            await axios.delete(`/equipe/${raidId}/${crsId}/${teamId}`, {
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-XSRF-TOKEN': csrf
                }
            });
            window.location.href = `/course-detail/${crsId}/${raidId}`;
        } catch (err) {
            try {
                // fallback POST with method override
                await axios.post(`/equipe/${raidId}/${crsId}/${teamId}`, { _method: 'DELETE' }, {
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-XSRF-TOKEN': csrf
                    }
                });
                window.location.href = `/course-detail/${crsId}/${raidId}`;
            } catch (e) {
                console.error('Error details:', e);
                console.error('Response:', e?.response);
                let msg = 'Impossible de supprimer l\'équipe.';
                if (e?.response) {
                    const data = e.response.data;
                    if (data?.error) msg = data.error;
                    else if (data?.message) msg = data.message;
                }
                alert(msg);
            }
        }
    }

    function handleFormSubmit(e) {
        if (!teamId) {
            e.preventDefault();
            alert('Identifiant d\'équipe manquant. Impossible d\'enregistrer.');
            return;
        }
    }

    return (
        <MainLayout>
            <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
                <h1 className="text-2xl font-bold mb-4">Modifier l'équipe</h1>

                <form onSubmit={handleFormSubmit} action={`/equipe/update/${teamId}`} method="POST" encType="multipart/form-data">
                    <input type="hidden" name="_token" value={csrf} />
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="equipe_id" value={teamId} />
                    <input type="hidden" name="RAID_ID" value={raidId} />
                    <input type="hidden" name="CRS_ID" value={crsId} />

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="md:col-span-2">
                            <label className="block text-sm font-medium text-gray-700">Nom de l'équipe </label>
                            <input name="nom" id="nom" required value={name} onChange={(e) => setName(e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 p-2" />

                            <label className="block text-sm font-medium text-gray-700 mt-4">Changer l'image de l'équipe (facultatif)</label>
                            {equipe.IMAGE_EQUIPE && (
                                <div className="mb-2">
                                    <img src={equipe.IMAGE_EQUIPE.startsWith('http') ? equipe.IMAGE_EQUIPE : `/storage/${equipe.IMAGE_EQUIPE}`} alt="Aperçu" className="w-32 h-32 object-cover rounded" />
                                </div>
                            )}
                            <input type="file" name="image" id="image" accept="image/*" className="mt-1 block w-full text-sm text-gray-600" />

                            <div className="mt-6 flex items-center gap-3">
                                <button type="submit" disabled={!teamId} className={`px-6 py-2.5 bg-green-600 text-white text-lg font-semibold rounded-lg transition-colors ${!teamId ? 'opacity-50 cursor-not-allowed hover:bg-green-600' : 'hover:bg-green-700'}`}>Enregistrer</button>
                                <button type="button" onClick={handleDeleteTeam} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Supprimer l'équipe</button>
                            </div>
                        </div>

                        <div className="md:col-span-1">
                            <div className="bg-gray-50 p-4 rounded-md h-full">
                                <h2 className="text-md font-semibold mb-3">Membres actuels</h2>
                                {equipe?.manager && (
                                    <div className="mb-3 p-2 bg-green-100 rounded border border-green-300">
                                        <div className="text-sm font-semibold text-green-900">Manager: {equipe.manager.nom} {equipe.manager.prenom}</div>
                                    </div>
                                )}
                                <div className="max-h-44 overflow-auto space-y-2 mb-3">
                                    {membersList.length === 0 && <div className="text-sm text-gray-500">Aucun membre.</div>}
                                    {membersList.map((member) => (
                                        <div key={member.id} className="flex items-center justify-between bg-white p-2 rounded border">
                                            <div className="flex items-center gap-2 flex-1">
                                                <div>
                                                    <div className="text-sm font-medium">{member.nom} {member.prenom}</div>
                                                </div>
                                                {member.statut === 'en_attente' && (
                                                    <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded border border-yellow-300">
                                                        En attente de réponse
                                                    </span>
                                                )}
                                                {member.statut === 'accepte' && (
                                                    <span className="inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded border border-green-300">
                                                        Accepté
                                                    </span>
                                                )}
                                            </div>
                                            <button type="button" onClick={() => handleRemove(member.id)} className="text-sm text-red-600 hover:underline">Retirer</button>
                                        </div>
                                    ))}
                                </div>

                                <h3 className="text-md font-semibold mb-2">Utilisateurs disponibles</h3>
                                {isTeamFull && (
                                    <div className="mb-2 p-2 bg-yellow-100 rounded border border-yellow-300">
                                        <p className="text-xs text-yellow-800">L'équipe est complète ({membersList.length}/{maxCapacity})</p>
                                    </div>
                                )}
                                <div className="max-h-40 overflow-auto space-y-2">
                                    {users.filter(u => !selected.includes(u.id)).map((user) => {
                                        const canAdd = user.available && !isTeamFull;
                                        
                                        return (
                                            <div key={user.id} className={`flex flex-col bg-white p-2 rounded border ${!user.available ? 'opacity-60' : ''}`}>
                                                <div className="flex items-center justify-between">
                                                    <div className="flex-1">
                                                        <div className={`text-sm font-medium ${!user.available ? 'text-gray-500' : ''}`}>
                                                            {user.nom} {user.prenom}
                                                        </div>
                                                    </div>
                                                    {canAdd && (
                                                        <button type="button" onClick={() => handleAdd(user.id)} className="text-sm text-green-600 hover:underline">Ajouter</button>
                                                    )}
                                                </div>
                                                {!user.available && user.unavailableReason && (
                                                    <div className="text-xs text-red-600 italic mt-1">
                                                        {user.unavailableReason}
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}