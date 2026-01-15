import { Head, Link } from '@inertiajs/react';
import compassIcon from '/public/SVGS/compass.svg';
import mountainIcon from '/public/SVGS/mountain.svg';
import trophyIcon from '/public/SVGS/trophy.svg';
import usersIcon from '/public/SVGS/users.svg';
import RAIDCard from '../Components/RAIDCard';
import Popup from '../Components/Popup';
import LoginForm from './Auth/Login_Form';
import RegisterForm from './Auth/Register_Form';
import { useState, useEffect, use } from 'react';
import { usePage } from '@inertiajs/react';

export default function Welcome({ threeNextRaids, clubs = [] }) {
    const props = usePage().props;
    const [overlayLog, setOverlayLog] = useState(props.flash?.error === 'Vous devez vous connecter pour accéder à cette page.');
    const [overlayReg, setOverlayReg] = useState(false);
    const items = Array.isArray(threeNextRaids) ? threeNextRaids : threeNextRaids?.data ?? [];

    useEffect(() => {
        if (props.flash?.error === 'Vous devez vous connecter pour accéder à cette page.') {
            setOverlayLog(true);
        }
    }, [props.flash?.error]);

    useEffect(() => {
        console.log('Flash data:', props.flash);
        if (props.flash?.status === 'Registration successful! Please log in.') {
            setOverlayReg(false);
            setOverlayLog(true);
        }
    }, [props.flash?.status]);

    return (
        <>
            <Head title="RAID Navigator" />
            <div className="min-h-screen bg-gray-50">

                {/* Front image with title and login/register buttons*/}
                <div className="relative min-h-[80vh] flex items-center justify-center overflow-hidden">
                    <div className="absolute inset-0">
                        <img
                            src="https://images.unsplash.com/photo-1551632811-561732d1e306?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1920"
                            alt="Mountain Adventure"
                            className="w-full h-full object-cover"
                        />
                        <div className="absolute inset-0 bg-black/50"></div>

                    </div>
                    {/*How It Works Section*/}
                    <div className="relative min-h-screen flex flex-col justify-center ">
                        <div className="w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-7 pt-0.5 bg-green-500 mt-[10px]">
                            <img src={mountainIcon} alt="Mountain" className="w-12 h-12 invert" />
                        </div>
                        <h1 className="text-6xl md:text-7xl font-semibold mb-6 tracking-tight text-center text-white">
                            RAID Navigator
                        </h1>
                        <h2 className="text-4xl font-bold text-center text-white mb-10 drop-shadow-lg">
                            Comment ça marche ?
                        </h2>
                        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-[3rem] shadow-2xl py-10 px-4 md:px-8 max-w-7xl mx-auto">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-white">
                                <div className="text-center">
                                    <div className="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                                        1
                                    </div>
                                    <h3 className="text-xl font-bold mb-3">Parcourir les RAIDs Disponibles</h3>
                                    <p className="text-gray-200">
                                        Explorez les prochaines courses d'orientation dans différents endroits, niveaux de difficulté et distances.
                                    </p>
                                </div>

                                <div className="text-center">
                                    <div className="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                                        2
                                    </div>
                                    <h3 className="text-xl font-bold mb-3">S'inscrire aux Événements</h3>
                                    <p className="text-gray-200">
                                        Inscrivez-vous aux RAIDs qui correspondent à votre niveau. Consultez les informations et les limites de participants.
                                    </p>
                                </div>

                                <div className="text-center">
                                    <div className="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                                        3
                                    </div>
                                    <h3 className="text-xl font-bold mb-3">Participer et Naviguer</h3>
                                    <p className="text-gray-200">
                                        Utilisez vos compétences de navigation pour compléter tous les points de contrôle dans l'ordre.
                                    </p>
                                </div>

                                <div className="text-center">
                                    <div className="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                                        4
                                    </div>
                                    <h3 className="text-xl font-bold mb-3">Voir vos Résultats</h3>
                                    <p className="text-gray-200">
                                        Après la course, consultez vos classements, votre temps et des statistiques détaillées pour vous améliorer.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="flex flex-col sm:flex-row justify-center gap-4 mt-10 mb-20">
                            <button
                                onClick={() => setOverlayReg(true)}
                                className="px-8 py-2.5 text-white rounded-xl font-medium text-base cursor-pointer bg-green-500 hover:bg-green-400 transition-colors">
                                Commencer
                            </button>
                            <button
                                onClick={() => setOverlayLog(true)}
                                className="px-8 py-2.5 border-2 border-white text-white rounded-xl font-medium text-base bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                                Se connecter
                            </button>
                        </div>
                    </div>
                </div>
                <div className="py-10 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-4xl font-semibold text-center text-gray-900 mb-4">
                            Prochains RAIDs
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-10 px-4 max-w-7xl mx-auto justify-items-center">
                            {items.length === 0 ? (
                                <p className="text-gray-500">Aucun raid à afficher.</p>
                            ) : (
                                items.map((raid) => (
                                    <RAIDCard
                                        key={raid.RAID_ID}
                                        title={raid.raid_nom}
                                        image={raid.raid_image ? `/Images/Card/${raid.raid_image}` : "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2071&auto=format&fit=crop"}
                                        date={raid.raid_date_debut}
                                        location={raid.raid_lieu}
                                        route={`/raid/${raid.RAID_ID}`}
                                        age_min={raid.age_min}
                                        age_max={raid.age_max}
                                        nb_courses={raid.nb_courses}
                                    />
                                ))
                            )}
                        </div>
                        <div className="flex justify-center">
                            <Link className="mt-4 px-20 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors" href="/test" >Voir plus</Link>
                        </div>
                    </div>
                </div>

                {/* What is a RAID Section */}
                <div className="py-10 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-4xl font-semibold text-center text-gray-900 mb-4">
                            Qu'est-ce qu'un RAID ?
                        </h2>
                        <p className="text-xl text-gray-500 text-center mb-12 max-w-4xl mx-auto leading-relaxed">
                            Le RAID (Raid Aventure International Discovery) est une course d'orientation multidisciplinaire qui combine course de trail, navigation et endurance. Les participants naviguent à travers des points de contrôle en utilisant des cartes et des boussoles, testant à la fois leur condition physique et leur acuité mentale.
                        </p>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 ">
                            <div className="bg-white rounded-xl p-6 border border-gray-200 hover:drop-shadow-xl">
                                <div className="w-14 h-14 bg-green-200 rounded-xl flex items-center justify-center mb-5">
                                    <img src={compassIcon} alt="Navigation" className="w-7 h-7" />
                                </div>
                                <h3 className="text-base font-medium text-gray-900 mb-2">Compétences de Navigation</h3>
                                <p className="text-gray-500 text-sm leading-relaxed">
                                    Maîtrisez l'art de l'orientation en utilisant des cartes, une boussole et l'analyse du terrain pour trouver les itinéraires optimaux.
                                </p>
                            </div>

                            <div className="bg-white rounded-xl p-6 border border-gray-200 hover:drop-shadow-xl">
                                <div className="w-14 h-14 bg-green-200 rounded-xl flex items-center justify-center mb-5">
                                    <img src={mountainIcon} alt="Terrain" className="w-7 h-7" />
                                </div>
                                <h3 className="text-base font-medium text-gray-900 mb-2">Terrains Variés</h3>
                                <p className="text-gray-500 text-sm leading-relaxed">
                                    Défiez-vous à travers montagnes, forêts, déserts et sentiers côtiers dans la France entière.
                                </p>
                            </div>

                            <div className="bg-white rounded-xl p-6 border border-gray-200 hover:drop-shadow-xl">
                                <div className="w-14 h-14 bg-green-200 rounded-xl flex items-center justify-center mb-5">
                                    <img src={trophyIcon} alt="Progress" className="w-7 h-7" />
                                </div>
                                <h3 className="text-base font-medium text-gray-900 mb-2">Suivre vos Progrès</h3>
                                <p className="text-gray-500 text-sm leading-relaxed">
                                    Surveillez vos performances, consultez les classements et célébrez vos réussites avec des statistiques détaillées.
                                </p>
                            </div>

                            <div className="bg-white rounded-xl p-6 border border-gray-200 hover:drop-shadow-xl">
                                <div className="w-14 h-14 bg-green-200 rounded-xl flex items-center justify-center mb-5">
                                    <img src={usersIcon} alt="Community" className="w-7 h-7" />
                                </div>
                                <h3 className="text-base font-medium text-gray-900 mb-2">Rejoindre la Communauté</h3>
                                <p className="text-gray-500 text-sm leading-relaxed">
                                    Connectez-vous avec d'autres aventuriers et participez à des événements organisés partout en France.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Platform Features Section */}
                <div className="py-24 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-4xl font-semibold text-center text-gray-900 mb-4">
                            Fonctionnalités de la Plateforme
                        </h2>
                        <p className="text-center text-gray-500 mb-16 text-xl">
                            Tout ce dont vous avez besoin pour gérer vos aventures d'orientation
                        </p>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="bg-gray-50 rounded-xl p-8 border border-green-200 flex items-start gap-4">
                                <div className="flex-shrink-0">
                                    <div className="w-8 h-8 rounded-full border-2 border-green-500 flex items-center justify-center">
                                        <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">Informations Détaillées sur les Courses</h3>
                                    <p className="text-gray-500 text-sm leading-relaxed">
                                        Accédez aux détails complets de chaque RAID incluant la difficulté, l'emplacement, les dates, ... .
                                    </p>
                                </div>
                            </div>

                            <div className="bg-gray-50 rounded-xl p-8 border border-green-200 flex items-start gap-4">
                                <div className="flex-shrink-0">
                                    <div className="w-8 h-8 rounded-full border-2 border-green-500 flex items-center justify-center">
                                        <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">Tableau de Bord Personnel</h3>
                                    <p className="text-gray-500 text-sm leading-relaxed">
                                        Suivez toutes vos participations, consultez les statistiques et surveillez vos progrès au fil du temps.
                                    </p>
                                </div>
                            </div>

                            <div className="bg-gray-50 rounded-xl p-8 border border-green-200 flex items-start gap-4">
                                <div className="flex-shrink-0">
                                    <div className="w-8 h-8 rounded-full border-2 border-green-500 flex items-center justify-center">
                                        <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">Analyses de Performance</h3>
                                    <p className="text-gray-500 text-sm leading-relaxed">
                                        Obtenez des informations détaillées incluant le temps de course, l'allure, le classement et les progrès aux points de contrôle.
                                    </p>
                                </div>
                            </div>

                            <div className="bg-gray-50 rounded-xl p-8 border border-green-200 flex items-start gap-4">
                                <div className="flex-shrink-0">
                                    <div className="w-8 h-8 rounded-full border-2 border-green-500 flex items-center justify-center">
                                        <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">Inscription Facile</h3>
                                    <p className="text-gray-500 text-sm leading-relaxed">
                                        Processus d'inscription simple en un clic avec suivi de disponibilité en temps réel.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* CTA Section */}
                <div className="py-24 bg-gray-50">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <h2 className="text-5xl font-semibold text-gray-900 mb-6">
                            Prêt pour Votre Prochaine Aventure ?
                        </h2>
                        <p className="text-xl text-gray-600 mb-10">
                            Rejoignez des milliers de passionnés d'orientation et commencez votre parcours RAID aujourd'hui.
                        </p>
                        <div className="flex flex-col sm:flex-row justify-center gap-6">
                            <button
                                onClick={() => setOverlayReg(true)}
                                className="px-8 py-2.5 text-white rounded-xl font-medium text-base cursor-pointer bg-green-500 hover:bg-green-400 transition-colors">
                                Commencer
                            </button>
                            <button
                                onClick={() => setOverlayLog(true)}
                                className="px-8 py-2.5 border-2 border-slate-200 text-slate-900 rounded-xl font-medium text-base bg-white hover:bg-slate-900/20 transition-colors cursor-pointer">
                                Se connecter
                            </button>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="text-slate-400 py-12 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <hr className="border-slate-300 mb-6" />
                        <div className="flex items-center justify-center space-x-3 mb-2">
                            <img src={mountainIcon} alt="Mountain" className="w-8 h-8 bg-green-300 p-2 rounded-xl" />
                            <span className="text-xl font-semi-bold text-slate-600">RAID Navigator</span>
                        </div>
                        <p className="text-lg mb-1">
                            Votre porte d'entrée vers les courses d'orientation multidisciplinaires
                        </p>
                        <p>
                            © 2026 RAID Navigator. Tous droits réservés.
                        </p>
                    </div>
                </footer>
            </div>

            {overlayReg && (
                <Popup setOverlay={setOverlayReg}>
                    <RegisterForm onSwitchToLogin={() => {
                        setOverlayReg(false);
                        setOverlayLog(true);
                    }}
                        clubs={clubs} />
                </Popup>
            )}

            {overlayLog && (
                <Popup setOverlay={setOverlayLog}>
                    <LoginForm onSwitchToRegister={() => {
                        setOverlayLog(false);
                        setOverlayReg(true);
                    }} />
                </Popup>
            )}
        </>
    );
}