import MainLayout from '../../Layout/MainLayout';
import { useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function CreationEquipe({ users = [], raid_id = null, course_id = null, maxCapacity = null }) {
    const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : null;
    const [selected, setSelected] = useState([]);
    const { errors } = usePage().props;

    function toggleMember(id, isAvailable) {
        if (!isAvailable) {
            return; // Don't allow toggling unavailable users
        }
        
        if (selected.includes(id)) {
            setSelected(prev => prev.filter(p => p !== id));
        } else {
            if (maxCapacity && selected.length >= maxCapacity) {
                alert(`Vous ne pouvez pas ajouter plus de ${maxCapacity} membres à cette équipe.`);
                return;
            }
            setSelected(prev => [...prev, id]);
        }
    }

    const remainingSpots = maxCapacity ? maxCapacity - selected.length : null;

    return (
        <MainLayout>
            <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
                <h1 className="text-2xl font-bold mb-4">Création d'équipe</h1>

                <form action="/equipe/store" method="POST" encType="multipart/form-data">
                    <input type="hidden" name="_token" value={typeof window !== 'undefined' && window.Laravel ? window.Laravel.csrfToken : ''} />
                    <input type="hidden" name="raid_id" value={raid_id} />
                    <input type="hidden" name="course_id" value={course_id} />

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="md:col-span-2">
                            {errors.members && (
                                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                                    {errors.members}
                                </div>
                            )}
                            
                            <label className="block text-sm font-medium text-gray-700">Nom de l'équipe</label>
                            <input name="nom" id="nom" required className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 p-2" />

                            <label className="block text-sm font-medium text-gray-700 mt-4">Image de l'équipe (facultatif)</label>
                            <input type="file" name="image" id="image" accept="image/*" className="mt-1 block w-full text-sm text-gray-600" />

                            <div className="mt-6">
                                <button type="submit" className="px-6 py-2.5 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 transition-colors">Créer l'équipe</button>
                            </div>
                        </div>

                        <div className="md:col-span-1">
                            <div className="bg-gray-50 p-4 rounded-md h-full">
                                <h2 className="text-md font-semibold mb-3">Ajouter des membres</h2>
                                <p className="text-sm text-gray-600 mb-3">Sélectionnez les membres à ajouter à cette équipe.</p>

                                {maxCapacity && (
                                    <div className={`mb-3 p-2 rounded border ${remainingSpots === 0 ? 'bg-red-100 border-red-300' : remainingSpots <= 2 ? 'bg-yellow-100 border-yellow-300' : 'bg-blue-100 border-blue-300'}`}>
                                        <p className={`text-sm font-semibold ${remainingSpots === 0 ? 'text-red-800' : remainingSpots <= 2 ? 'text-yellow-800' : 'text-blue-800'}`}>
                                            Places restantes: {remainingSpots}/{maxCapacity}
                                        </p>
                                    </div>
                                )}

                                <div className="max-h-64 overflow-auto space-y-2">
                                    {users.length === 0 && (
                                        <div className="text-sm text-gray-500">Aucun utilisateur disponible.</div>
                                    )}

                                    {users.map((user) => {
                                        const isDisabled = !user.available || (!selected.includes(user.id) && maxCapacity && selected.length >= maxCapacity);
                                        
                                        return (
                                            <label 
                                                key={user.id} 
                                                className={`flex flex-col bg-white p-2 rounded border ${isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
                                            >
                                                <div className="flex items-center justify-between">
                                                    <div className="flex-1">
                                                        <div className={`text-sm font-medium ${!user.available ? 'text-gray-500' : ''}`}>
                                                            {user.nom} {user.prenom}
                                                        </div>
                                                    </div>
                                                    <input 
                                                        type="checkbox" 
                                                        name="members[]" 
                                                        value={user.id} 
                                                        checked={selected.includes(user.id)} 
                                                        onChange={() => toggleMember(user.id, user.available)} 
                                                        disabled={isDisabled}
                                                        className="h-4 w-4 ml-2" 
                                                    />
                                                </div>
                                                {!user.available && user.unavailableReason && (
                                                    <div className="text-xs text-red-600 italic mt-1">
                                                        {user.unavailableReason}
                                                    </div>
                                                )}
                                            </label>
                                        );
                                    })}
                                </div>

                                <div className="mt-3 text-sm text-gray-700">
                                    Membres sélectionnés: <span className="font-semibold">{selected.length}</span>
                                    {maxCapacity && <span className="text-gray-500"> / {maxCapacity}</span>}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}