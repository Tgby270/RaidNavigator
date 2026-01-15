import React from 'react';

import MainLayout from "@/Layout/MainLayout";
import { Head, useForm } from '@inertiajs/react';

export default function CreateRaid({ users = [], club_id = null, club = null }) {

    const { data, setData, post, processing, errors, reset } = useForm({
                title: '',
                dateInscriptionBegin: '',
                dateInscriptionEnd: '',
                dateBegin: '',
                dateEnd: '',
                contact: '',
                website: '',
                location: '',
                city: '',
                use_id: '',
                numberOfRaces: '',
                image: null,
                club_id: club_id || ''
        });

        console.log('Props CreateRaid:', { users, club_id, club, formClubId: data.club_id });

        const submit = (e) => {
            e.preventDefault();

            // Validate city field is filled
            if (!data.city) {
                console.error('La ville est requise.');
                return;
            }

            // Use city as location
            const location = data.city;

            // Ensure club_id is set from prop if present
            if ((!data.club_id || data.club_id === '') && club_id) {
                setData('club_id', club_id);
            }

            // Set location into the form data so it's visible in state
            setData('location', location);

            console.log('Données avant soumission (prêtes à envoyer):', { ...data, location, club_id: club_id || data.club_id });

            // Post the form and make sure the visit data contains the updated location and club_id (preserves file uploads)
            post('/raid/create', {
                onBefore: (visit) => {
                    visit.data.location = location;
                    visit.data.club_id = data.club_id || club_id;
                }
            });
        };

    return (
        <MainLayout>

            <h2 className="text-4xl font-bold mb-4">Créer un nouveau Raid</h2>
            
            {Object.keys(errors).length > 0 && (
                <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <h3 className="font-bold">Erreurs de validation:</h3>
                    <ul className="list-disc ml-5">
                        {Object.entries(errors).map(([field, message]) => (
                            <li key={field}>{field}: {Array.isArray(message) ? message[0] : message}</li>
                        ))}
                    </ul>
                </div>
            )}
            
            <form onSubmit={submit}>
                <div className="flex flex-row gap-70 items-start mb-6">
                    <Head title="Créer un nouveau Raid" />

                    <div className=" flex flex-col ">
                        <h2 className="text-gray-400 text-lg font-semibold mb-4 "> Remplissez ces details pour créer un nouveau raid</h2>
                        <div>
                            <label htmlFor="title" className="font-semibold block mb-1">Nom du Raid:</label>

                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                required 
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                className="border border-gray-100 py-1 w-full rounded bg-gray-100" />
                        </div>

                        <div className="mb-4">
                            <label htmlFor="dateInscriptionBegin" className="font-semibold block mb-1 flex items-center gap-2">
                                Date d'inscription*
                               
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5 text-gray-500">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                </svg>
                            </label>

                            <div className="flex flex-row items-center gap-2">
                                <input
                                    type="date"
                                    id="dateInscriptionBegin"
                                    name="dateInscriptionBegin"
                                    required
                                    value={data.dateInscriptionBegin}
                                    onChange={(e) => setData('dateInscriptionBegin', e.target.value)}
                                    className="border border-gray-100 py-2 w-full rounded bg-gray-100"
                                />
                                <span className="font-bold text-gray-400">-</span>
                                <input
                                    type="date"
                                    id="dateInscriptionEnd"
                                    name="dateInscriptionEnd"
                                    required
                                    value={data.dateInscriptionEnd}
                                    onChange={(e) => setData('dateInscriptionEnd', e.target.value)}
                                    className="border border-gray-100 py-2 w-full rounded bg-gray-100"
                                />
                            </div>
                        </div>

                      
                        <div className="mb-4">
                            <label htmlFor="dateBegin" className="font-semibold block mb-1 flex items-center gap-2">
                                Date*
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5 text-gray-500">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                </svg>
                            </label>

                            <div className="flex flex-row items-center gap-2">
                                <input
                                    type="date"
                                    id="dateBegin"
                                    name="dateBegin"
                                    required
                                    value={data.dateBegin}
                                    onChange={(e) => setData('dateBegin', e.target.value)}
                                    className="border border-gray-100 py-2 w-full rounded bg-gray-100"
                                />
                                <span className="font-bold text-gray-400">-</span>
                                <input
                                    type="date"
                                    id="dateEnd"
                                    name="dateEnd"
                                    required
                                    value={data.dateEnd}
                                    onChange={(e) => setData('dateEnd', e.target.value)}
                                    className="border border-gray-100 py-2 w-full rounded bg-gray-100"
                                />
                            </div>
                        </div>
                        <div>
                            <div className="mb-4">
                                <label htmlFor="city" className="font-semibold">Ville*</label>
                                <br />
                                <input 
                                    type="text" 
                                    id="city" 
                                    name="city" 
                                    required 
                                    value={data.city}
                                    onChange={(e) => setData('city', e.target.value)}
                                    className="border border-gray-100 py-1 w-full rounded bg-gray-100" />
                            </div>
                        </div>

                        <div className="mb-4">
                            <label htmlFor="contact" className="font-semibold">Moyen de contact (email ou téléphone):</label>
                            <br />
                            <input 
                                type="text" 
                                id="contact" 
                                name="contact" 
                                required 
                                value={data.contact}
                                onChange={(e) => setData('contact', e.target.value)}
                                className="border border-gray-100 py-1 w-full rounded bg-gray-100" />
                        </div>
                        <div className="mb-4">
                            <label htmlFor="use_id" className="font-semibold">Responsable*</label>
                            <br />
                            <select 
                                name="use_id" 
                                id="use_id" 
                                required 
                                value={data.use_id}
                                onChange={(e) => setData('use_id', e.target.value)}
                                className="border border-gray-100 py-1 w-full rounded bg-gray-100">
                                <option value="" disabled>Sélectionner un responsable</option>
                                {users.map((user) => (
                                    <option key={user.USE_ID} value={user.USE_ID}>{user.USE_NOM} {user.USE_PRENOM}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label htmlFor="numberOfRaces" className="font-semibold block mb-1">Nombre de courses prévues:</label>
                            <input 
                                type="number" 
                                id="numberOfRaces" 
                                name="numberOfRaces" 
                                required 
                                value={data.numberOfRaces}
                                onChange={(e) => setData('numberOfRaces', e.target.value)}
                                className="border border-gray-100 py-1 w-full rounded bg-gray-100" />
                        </div>

                        <div className="mb-4">
                            <label htmlFor="website" className="font-semibold">URL du site Web du Raid (facultatif):</label>
                            <br />
                            <input 
                                type="url" 
                                id="website" 
                                name="website" 
                                value={data.website}
                                onChange={(e) => setData('website', e.target.value)}
                                className="border border-gray-100 py-1 w-full rounded bg-gray-100" />
                        </div>
                    </div>

                    <div className="flex flex-col gap-4 items-center">

                        <div className="mb-4">
                            <label htmlFor="image" className="font-semibold block mb-2">Image (facultative):</label>

                            <div className="file-upload w-64 h-64 border-2 border- border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden">

                                <label htmlFor="image" className="cursor-pointer w-full h-full flex items-center justify-center">

                                    {data.image ? (
                                        <img
                                            src={URL.createObjectURL(data.image)}
                                            alt="Prévisualisation"
                                            className="w-full h-full object-cover rounded-lg"
                                        />
                                    ) : (
                                        <div className="flex flex-col items-center justify-center p-4 text-center">
                                            <div className="mb-2">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    strokeWidth={1.5}
                                                    stroke="currentColor"
                                                    className="w-10 h-10 mx-auto text-gray-400"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                                                    />
                                                </svg>
                                            </div>
                                            <span className="text-sm font-medium text-gray-600">Choisir une image</span>
                                        </div>
                                    )}

                                </label>

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                    className="hidden"
                                    onChange={(e) => setData('image', e.target.files[0])}
                                />
                            </div>
                        </div>

                        <button
                            type="submit" 
                            disabled={processing}
                            className={`py-2 px-10 border font-semibold text-white rounded-lg flex items-center transition duration-300 shadow-md ${processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-black hover:bg-gray-800'}`}>
                            {processing ? 'Création en cours...' : 'Créer le RAID'}
                        </button>
                    </div>

                </div>


            </form>

        </MainLayout>
    );
}