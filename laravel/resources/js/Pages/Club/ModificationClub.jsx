import React from 'react';
import MainLayout from "@/Layout/MainLayout";
import { Head, Link, useForm } from '@inertiajs/react';

export default function ModificationClub({ users = [], club = null }) {
    console.log('ModificationClub component loaded', { users, club });
    const isEdit = !!club;
    const { data, setData, post, processing, errors } = useForm({
        CLU_NOM: club?.CLU_NOM || '',
        CLU_ADRESSE: club?.CLU_ADRESSE || '',
        CLU_CODE_POSTAL: club?.CLU_CODE_POSTAL || '',
        CLU_VILLE: club?.CLU_VILLE || '',
        CLU_CONTACT: club?.CLU_CONTACT || '',
        USE_ID: club?.USE_ID || ''
    });

    const submit = (e) => {
        e.preventDefault();
        console.log('Form submitted', { data, isEdit });
        if (isEdit) {
            post(`/club/${club.CLU_ID}/update`);
        } else {
            post('/club/create');
        }
    };

    return (
        <MainLayout>
            <Head title={isEdit ? "Modifier le club" : "Créer un nouveau Club"} />

            <div className="max-w-4xl mx-auto p-6">
                <Link 
                    href="/" // Pour l'instant le bouton redirige vers la page d'accueil, normalement ca redirige vers la page admin-dashboard
                    className="inline-flex items-center px-4 py-1 bg-gray-200 text-sm font-medium rounded-full hover:bg-gray-300 transition-colors mb-8"
                >
                    <span className="mr-2">←</span> Retour
                </Link>
                <h2 className="text-3xl font-bold text-center mb-8 tracking-tight">{isEdit ? 'Modification du club' : 'Création d\u2019un club'}</h2>
                
                <form onSubmit={submit} className="flex flex-col items-center">
                    <div className="bg-gray-200 w-full max-w-2xl rounded-[40px] p-10 shadow-sm">                        
                        <div className="flex items-center mb-6">
                            <label htmlFor="CLU_NOM" className="text-xl font-bold w-32">Nom :</label>
                            <input 
                                type="text" 
                                id="CLU_NOM" 
                                name="CLU_NOM"
                                value={data.CLU_NOM}
                                onChange={(e) => setData('CLU_NOM', e.target.value)}
                                className="flex-1 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner"
                                required
                            />
                            {errors.CLU_NOM && <div className="text-red-500 mt-2">{errors.CLU_NOM}</div>}
                        </div>
                        <div className="flex items-center mb-6">
                            <label htmlFor="CLU_ADRESSE" className="text-xl font-bold w-32">Adresse :</label>
                            <input 
                                type="text" 
                                id="CLU_ADRESSE" 
                                name="CLU_ADRESSE"
                                value={data.CLU_ADRESSE}
                                onChange={(e) => setData('CLU_ADRESSE', e.target.value)}
                                className="flex-1 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner"
                                required
                            />
                            {errors.CLU_ADRESSE && <div className="text-red-500 mt-2">{errors.CLU_ADRESSE}</div>}
                        </div>
                        <div className="flex gap-4 mb-6 ml-32">
                            <input 
                                type="text" 
                                name="CLU_CODE_POSTAL"
                                value={data.CLU_CODE_POSTAL}
                                onChange={(e) => setData('CLU_CODE_POSTAL', e.target.value)}
                                placeholder="Code Postal"
                                className="w-1/3 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner"
                                required
                            />
                            <input 
                                type="text" 
                                name="CLU_VILLE"
                                value={data.CLU_VILLE}
                                onChange={(e) => setData('CLU_VILLE', e.target.value)}
                                placeholder="Ville"
                                className="flex-1 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner"
                                required
                            />
                        </div>
                        <div className="flex items-center mb-6">
                            <label htmlFor="CLU_CONTACT" className="text-xl font-bold w-40 flex-shrink-0">Numero :</label>
                            <input 
                                type="text" 
                                id="CLU_CONTACT"
                                name="CLU_CONTACT"
                                value={data.CLU_CONTACT}
                                onChange={(e) => setData('CLU_CONTACT', e.target.value)}
                                placeholder="Téléphone" 
                                className="w-1/2 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner" 
                                required 
                            />
                        </div>
                        <div className="flex items-center">
                            <label htmlFor="USE_ID" className="text-xl font-bold w-40 flex-shrink-0">Responsable :</label>
                            <select 
                                id="USE_ID"
                                name="USE_ID"
                                value={data.USE_ID}
                                onChange={(e) => setData('USE_ID', e.target.value)}
                                className="w-1/2 bg-white border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-emerald-400 outline-none shadow-inner" 
                                required
                            >
                                <option value="" disabled>Sélectionner un responsable</option>
                                {users && users.length > 0 ? (
                                    users.map((user) => (
                                        <option key={user.USE_ID} value={user.USE_ID}>
                                            {user.USE_NOM} {user.USE_PRENOM}
                                        </option>
                                    ))
                                ) : (
                                    <option value="" disabled>Aucun utilisateur disponible</option>
                                )}
                            </select>
                        </div>
                    </div>
                    <button 
                        type="submit"
                        disabled={processing}
                        className="mt-8 bg-[#2ecc71] text-white font-bold py-3 px-16 rounded-full text-lg shadow-lg hover:bg-[#27ae60] hover:scale-105 active:scale-95 transition-all uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {processing ? (isEdit ? 'Enregistrement...' : 'Création...') : (isEdit ? 'Enregistrer' : 'Créer')}
                    </button>
                </form>
            </div>
        </MainLayout>
    );
}
