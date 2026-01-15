import React, { useState } from 'react';
import MainLayout from "@/Layout/MainLayout";

import { Head, usePage, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

const UserIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>);
const KeyIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>);
const ShieldCheckIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" /></svg>);
const UserGroupIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6"><path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 5.472m0 0a9.09 9.09 0 0 0-3.279.479 3 3 0 0 0 4.681 2.72 8.9 8.9 0 0 0 3.656.603 8.9 8.9 0 0 0 3.656-.603m-9.117 0A9.094 9.094 0 0 1 3 18.72m-1.383-2.618a3 3 0 0 1 4.244-4.244m3.873-1.025A6.062 6.062 0 0 1 12 10.5c1.282 0 2.47.402 3.457 1.082" /></svg>);


export default function UserAccount() {

    const { auth, flash, user: userProp } = usePage().props;
    const user = userProp ?? auth.user;

    const [activeTab, setActiveTab] = useState('profil');

    const { data, setData, post, patch, processing, errors, recentlySuccessful } = useForm({
        name: user.USE_NOM || '',
        prenom: user.USE_PRENOM || '',
        email: user.USE_MAIL || '',
        type: user.USE_NUM_LICENCIE ? 'licencie' : 'adherent',
        numero_licence: user.USE_NUM_LICENCIE || '',
        pps: user.USE_NUM_PPS || '',
    });
    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const deleteForm = useForm({
        password: '',
    });
    const submitProfile = (e) => {
        e.preventDefault();
        patch('/profile');
    };

    const submitPassword = (e) => {
        e.preventDefault();

        passwordForm.patch('/profile/password', {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    };

    const deleteAccount = (e) => {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')) {
            deleteForm.delete('/profile');
        }
    };


    return (
        <MainLayout>
            <Head title="Mon Compte" />
            <div className="py-4">
                <h1 className="text-3xl font-semibold mb-3">Paramètres</h1>
                <h2 className="text-gray-600 font-medium">Gérer votre profil et les paramètres de votre compte</h2>
                <hr className="my-4 border-gray-300" />

                {flash?.success && (
                    <div className="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 flex items-center gap-3 text-green-700 shadow-sm animate-fade-in-down">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path fillRule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clipRule="evenodd" />
                        </svg>
                        <span className="font-medium">{flash.success}</span>
                    </div>
                )}

                <div className="gap-7 mt-8 flex flex-row flex-wrap bg-gray-100 p-6 rounded-xl">
                    <aside className="lg:col-span-3 mb-6 lg:mb-0">
                        <nav className="space-y-1 bg-white rounded-2xl shadow-sm p-2 sticky top-6">
                            <button
                                onClick={() => setActiveTab('profil')}
                                className={`w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ${activeTab === 'profil'
                                    ? 'bg-black text-white shadow-md'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                                    }`}
                            >
                                <UserIcon />
                                Profil & Licence
                            </button>
                            <button
                                onClick={() => setActiveTab('password')}
                                className={`w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ${activeTab === 'password'
                                    ? 'bg-black text-white shadow-md'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                                    }`}
                            >
                                <KeyIcon />
                                Sécurité
                            </button>
                        </nav>
                    </aside>

                    <div className="bg-white shadow-sm ring-1 ring-gray-900/5 w-150 sm:rounded-2xl p-6 md:p-8">
                        {activeTab === 'profil' && (
                            <>
                                <h2 className="text-xl font-semibold mb-2">Information du profil</h2>
                                <h2 className="text-gray-600 font-medium mb-4">Mettez à jour votre nom et votre adresse e-mail</h2>

                                <form onSubmit={submitProfile} className="space-y-6">
                                    <label htmlFor="prenom" className="block mb-2 text-sm font-medium">Prénom</label>
                                    <input
                                        type="text"
                                        id="prenom"
                                        value={data.prenom}
                                        onChange={(e) => setData('prenom', e.target.value)}
                                        className="border border-gray-300 py-1 px-3 bg-white rounded-lg w-full"
                                    />
                                    {errors.prenom && <div className="text-red-500 text-sm">{errors.prenom}</div>}
                                    <label htmlFor="name" className="block mb-2 text-sm font-medium">Nom</label>
                                    <input
                                        type="text"
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                        className="border border-gray-300 py-1 px-3 bg-white rounded-lg w-full mb-4"
                                    />
                                    {errors.name && <div className="text-red-500 text-sm mb-2">{errors.name}</div>}

                                    <label htmlFor="email" className="block mb-2 text-sm font-medium">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                        className="border border-gray-300 py-1 px-3 bg-white rounded-lg w-full mb-2"
                                    />
                                    {errors.email && <div className="text-red-500 text-sm mb-2">{errors.email}</div>}
                                    <div className="pt-4">
                                        <label className="text-sm font-medium leading-6 text-gray-900 mb-4 block">Statut Sportif</label>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                            <label className={`relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all duration-200 ${data.type === 'licencie' ? 'border-black ring-1 ring-black bg-gray-50' : 'border-gray-200 hover:border-gray-300'}`}>
                                                <input type="radio" name="type" value="licencie" checked={data.type === 'licencie'} onChange={(e) => setData('type', e.target.value)} className="sr-only" />
                                                <div className="flex w-full items-center justify-between">
                                                    <div className="flex items-center">
                                                        <div className="text-sm">
                                                            <div className={`font-semibold ${data.type === 'licencie' ? 'text-black' : 'text-gray-900'}`}>
                                                                <div className="flex items-center gap-2">
                                                                    <ShieldCheckIcon /> Licencié
                                                                </div>
                                                            </div>
                                                            <p className="text-gray-500 mt-1">J'ai une licence active</p>
                                                        </div>
                                                    </div>
                                                    {data.type === 'licencie' && <div className="h-4 w-4 rounded-full border-4 border-black"></div>}
                                                </div>
                                            </label>


                                            <label className={`relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all duration-200 ${data.type === 'adherent' ? 'border-black ring-1 ring-black bg-gray-50' : 'border-gray-200 hover:border-gray-300'}`}>
                                                <input type="radio" name="type" value="adherent" checked={data.type === 'adherent'} onChange={(e) => setData('type', e.target.value)} className="sr-only" />
                                                <div className="flex w-full items-center justify-between">
                                                    <div className="flex items-center">
                                                        <div className="text-sm">
                                                            <div className={`font-semibold ${data.type === 'adherent' ? 'text-black' : 'text-gray-900'}`}>
                                                                <div className="flex items-center gap-2">
                                                                    <UserGroupIcon /> Adhérent
                                                                </div>
                                                            </div>
                                                            <p className="text-gray-500 mt-1">Non-licencié / PPS</p>
                                                        </div>
                                                    </div>
                                                    {data.type === 'adherent' && <div className="h-4 w-4 rounded-full border-4 border-black"></div>}
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {data.type === 'licencie' && (
                                        <div className="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                            <label htmlFor="numero_licence" className="block mb-2 text-sm font-bold text-blue-800">
                                                Numéro de Licence
                                            </label>
                                            <input
                                                type="text"
                                                id="numero_licence"
                                                value={data.numero_licence}
                                                onChange={(e) => setData('numero_licence', e.target.value)}
                                                className="border border-blue-300 py-1 px-3 rounded-lg w-full"
                                                placeholder="Ex: 1234567A"
                                            />
                                        </div>
                                    )}

                                    {data.type === 'adherent' && (
                                        <div className="mb-6 bg-green-50 p-4 rounded-lg border border-green-100">
                                            <label htmlFor="pps" className="block mb-2 text-sm font-bold text-green-800">
                                                Code PPS (Parcours Prévention Santé)
                                            </label>
                                            <input
                                                type="text"
                                                id="pps"
                                                value={data.pps}
                                                onChange={(e) => setData('pps', e.target.value)}
                                                className="border border-green-300 py-1 px-3 rounded-lg w-full"
                                                placeholder="Entrez votre code PPS"
                                            />
                                        </div>
                                    )}

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="  bg-black text-white mb-9 py-2 px-4 rounded-lg font-semibold hover:bg-gray-800 transition disabled:opacity-50">
                                        Enregistrer les modifications
                                    </button>
                                    {recentlySuccessful && (
                                        <div className="text-green-600 font-bold mt-2">✅ Sauvegardé avec succès !</div>
                                    )}
                                </form>
                                 <div className="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-2xl p-6 md:p-8">
                                    <form onSubmit={deleteAccount}>
                                    <h2 className="font-medium mb-2 text-red-600">Zone de danger</h2>
                                    <p className="font-medium text-gray-600 mb-4">Supprimer votre compte et toutes ses ressources</p>

                                    <label htmlFor="password" className="block mb-2 text-sm font-medium">Mot de passe</label>
                                    <input
                                        type="password"
                                        id="password"
                                        value={deleteForm.data.password}
                                        onChange={(e) => deleteForm.setData('password', e.target.value)}
                                        required
                                        className="border border-gray-300 py-1 px-3 rounded-lg w-full mb-4"
                                    />
                                    {deleteForm.errors.password && <div className="text-red-500 text-sm mb-2">{deleteForm.errors.password}</div>}

                                    <button
                                        type="submit"
                                        disabled={deleteForm.processing}
                                        className="bg-red-600 text-white mb-9 py-2 px-4 rounded-lg font-semibold hover:bg-red-800 transition">
                                        Supprimer le compte
                                    </button>
                                </form>
                                 
                                 </div>
                                

                            </>
                        )}


                        {activeTab === 'password' && (
                            <>
                                <h2 className="text-xl font-semibold mb-2">Modifier le mot de passe</h2>
                                <h2 className="text-gray-600 font-medium mb-4">Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.</h2>

                                <form onSubmit={submitPassword}>
                                    <label htmlFor="current_password" className="block mb-2 text-sm font-medium">Mot de passe actuel</label>
                                    <input
                                        type="password"
                                        id="current_password"
                                        value={passwordForm.data.current_password}
                                        onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                        required
                                        placeholder="Entrez votre mot de passe actuel, au moins 6 caractères"
                                        className="border border-gray-300 py-1 bg-white px-3 rounded-lg w-full mb-4"
                                    />
                                    {passwordForm.errors.current_password && <div className="text-red-500 text-sm mb-2">{passwordForm.errors.current_password}</div>}

                                    <label htmlFor="password" className="block mb-2 text-sm font-medium">Nouveau mot de passe</label>
                                    <input
                                        type="password"
                                        id="password"
                                        value={passwordForm.data.password}
                                        onChange={(e) => passwordForm.setData('password', e.target.value)}
                                        required
                                        placeholder="Entrez votre nouveau mot de passe, au moins 6 caractères"
                                        className="border border-gray-300 py-1 bg-white px-3 rounded-lg w-full mb-4"
                                    />
                                    {passwordForm.errors.password && <div className="text-red-500 text-sm mb-2">{passwordForm.errors.password}</div>}

                                    <label htmlFor="password_confirmation" className="block mb-2 text-sm font-medium">Confirmer le nouveau mot de passe</label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        value={passwordForm.data.password_confirmation}
                                        onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                        required
                                        placeholder="Confirmez votre nouveau mot de passe"
                                        className="border border-gray-300 py-1 bg-white px-3 rounded-lg w-full mb-6"
                                    />
                                    {passwordForm.errors.password_confirmation && <div className="text-red-500 text-sm mb-2">{passwordForm.errors.password_confirmation}</div>}

                                    <button
                                        type="submit"
                                        disabled={passwordForm.processing}
                                        className="bg-black text-white mb-9 py-2 px-4 rounded-lg font-semibold hover:bg-gray-800 transition disabled:opacity-50">
                                        Mettre à jour le mot de passe
                                    </button>
                                </form>
                            </>
                        )}


                    </div>
                </div>
            </div>
        </MainLayout>
    );
}