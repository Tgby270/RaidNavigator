import { useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post('/login');
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
                    Bon retour sur RAID Navigator !
                </h2>
            </div>

            {/* Right Side - Login Form */}
            <div className="flex-1 bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center p-8">
                <div className="w-full max-w-md">
                    <div className="bg-white rounded-lg shadow-lg p-8">
                        <h3 className="text-2xl font-bold text-gray-800 mb-2">Bon retour !</h3>
                        <p className="text-gray-500 text-sm mb-6">Veuillez entrer votre mail et mot de passe</p>

                        {status && (
                            <div className="mb-4 text-sm font-medium text-green-600">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit}>
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
                                    autoFocus
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
                                    autoComplete="current-password"
                                    placeholder="Enter your password"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none"
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                {errors.password && (
                                    <div className="mt-2 text-sm text-red-600">{errors.password}</div>
                                )}
                            </div>

                            <div className="mt-4 mb-2 block">
                                <label className="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-800"
                                    />
                                    <span className="ms-2 text-sm text-gray-600">
                                        Se souvenir de moi
                                    </span>
                                </label>
                            </div>

                            <div className="text-right mb-6">
                                {canResetPassword && (
                                    <a href={route('password.request')} className="text-sm text-gray-500 hover:text-gray-700">
                                        Mot de passe oublié ?
                                    </a>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-gray-900 text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors disabled:opacity-50"
                            >
                                Se connecter
                            </button>
                        </form>

                        <p className="text-center text-sm text-gray-600 mt-6">
                            Vous n'avez pas de compte ? <a href="/register" className="text-gray-900 font-medium hover:underline">Inscrivez-vous</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    )
}