import { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function Register({ clubs = [] }) {
    const [role, setRole] = useState('');
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        license: '',
        club_id: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post('/register');
    };

    return (
        <div className="min-h-screen flex">
            {/* Left Side - Purple/Blue gradient */}
            <div className="flex-1 bg-gradient-to-br from-indigo-100 to-blue-100 flex flex-col items-center justify-center p-8">
                <div className="bg-black text-white p-6 rounded-2xl mb-6">
                    <svg
                        className="w-12 h-12"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#ffffff"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                    >
                        <path d="M8 3l4 8 5-5 5 15H2L8 3z" />
                    </svg>
                </div>
                <h2 className="text-2xl font-semibold text-gray-800 text-center">
                    Rejoignez RAID Navigator !
                </h2>
            </div>

            {/* Right Side - Login Form */}
            <div className="flex-1 bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center p-8">
                <div className="w-full max-w-md">
                    <div className="bg-white rounded-lg shadow-lg p-8">
                        <h3 className="text-2xl font-bold text-gray-800 mb-2">Creez votre compte</h3>
                        <p className="text-gray-500 text-sm mb-6">Rejoignez la communauté RAID Navigator</p>

                        <form onSubmit={submit}>
                            <div className="mb-4">
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                                    Nom Complet
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value={data.name}
                                    required
                                    autoFocus
                                    placeholder="John Doe"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                    onChange={(e) => setData('name', e.target.value)}
                                />
                                {errors.name && (
                                    <div className="mt-2 text-sm text-red-600">{errors.name}</div>
                                )}
                            </div>

                            <div className="mb-4">
                                <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    required
                                    placeholder="mail@example.com"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                    onChange={(e) => setData('email', e.target.value)}
                                />
                                {errors.email && (
                                    <div className="mt-2 text-sm text-red-600">{errors.email}</div>
                                )}
                            </div>

                            <div className="mb-2">
                                <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                                    Mot de passe
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    required
                                    autoComplete="new-password"
                                    placeholder="Entrez votre mot de passe, Au moins 6 caractères"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                {errors.password && (
                                    <div className="mt-2 text-sm text-red-600">{errors.password}</div>
                                )}
                            </div>

                            <div className="mb-2">
                                <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmez le mot de passe
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    required
                                    autoComplete="new-password"
                                    placeholder="Confirmez votre mot de passe"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                />
                                {errors.password_confirmation && (
                                    <div className="mt-2 text-sm text-red-600">{errors.password_confirmation}</div>
                                )}
                            </div>

                            <div className="mb-2">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Etes vous détenteur d'un licence*
                                </label>

                                <input
                                    id="1"
                                    type="button"
                                    value="oui"
                                    className="w-32 px-4 py-2 border border-gray-300 rounded-lg     focus:bg-gray-200 outline-none mr-4 hover:cursor-pointer"
                                    onClick={() => setRole('Liscencié')}
                                />

                                <input
                                    id="2"
                                    type="button"
                                    value="non"
                                    className="w-32 px-4 py-2 border border-gray-300 rounded-lg     focus:bg-gray-200 outline-none mr-4 hover:cursor-pointer"
                                    onClick={() => setRole('Non Liscencié')}
                                />
                                {role === 'Liscencié' ? (
                                    <>
                                        <label htmlFor="license" className="block text-sm font-medium text-gray-700 mt-4">
                                            Numéro de licence
                                        </label>
                                        <input
                                            id="license"
                                            type="text"
                                            name="license"
                                            value={data.license}
                                            required
                                            placeholder="Entrez votre numéro de licence"
                                            className="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                            onChange={(e) => setData('license', e.target.value)}
                                        />
                                        {errors.license && (
                                            <div className="mt-2 text-sm text-red-600">{errors.license}</div>
                                        )}
                                    </>) : null}

                            </div>

                            <div className="mb-4">
                                <label htmlFor="club" className="block text-sm font-medium text-gray-700 mb-2">
                                    Club
                                </label>
                                <select
                                    id="club"
                                    name="club_id"
                                    value={data.club_id}
                                    onChange={(e) => setData('club_id', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                >
                                    <option value="">Sélectionnez un club</option>
                                    {clubs.map((club) => (
                                        <option key={club.CLU_ID} value={club.CLU_ID}>
                                            {club.CLU_NOM}
                                        </option>
                                    ))}
                                </select>
                                {errors.club_id && (
                                    <div className="mt-2 text-sm text-red-600">{errors.club_id}</div>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-gray-900 text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors disabled:opacity-50"
                            >
                                S'inscrire
                            </button>
                        </form>

                        <p className="text-center text-sm text-gray-600 mt-6">
                            Vous avez déjà un compte ? <a href="/login" className="text-gray-900 font-medium hover:underline">Connectez-vous</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    )
}