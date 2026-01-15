import Logo from "../Components/Logo.jsx";
import { useState, useRef, useEffect } from "react";
import LoginForm from "../Pages/Auth/Login_Form.jsx";
import RegisterForm from "../Pages/Auth/Register_Form.jsx";
import Popup from "../Components/Popup.jsx";
import { Link, usePage } from "@inertiajs/react";

export default function MainLayout({ children }) {
    const [overlayLog, setOverlayLog] = useState(false);
    const [overlayReg, setOverlayReg] = useState(false);
    const [showUserMenu, setShowUserMenu] = useState(false);
    const userButtonRef = useRef(null);
    const wrapperRef = useRef(null);
    const [menuWidth, setMenuWidth] = useState(null);

    useEffect(() => {
        function measure() {
            if (userButtonRef.current) {
                setMenuWidth(userButtonRef.current.getBoundingClientRect().width);
            }
        }

        if (showUserMenu) {
            measure();
            window.addEventListener('resize', measure);
        }

        return () => {
            window.removeEventListener('resize', measure);
        };
    }, [showUserMenu]);

    // Close menu when clicking outside
    useEffect(() => {
        function handleOutsideClick(e) {
            if (showUserMenu && wrapperRef.current && !wrapperRef.current.contains(e.target)) {
                setShowUserMenu(false);
            }
        }

        document.addEventListener('mousedown', handleOutsideClick);
        return () => document.removeEventListener('mousedown', handleOutsideClick);
    }, [showUserMenu]);
    const page = usePage();
    const { auth, raids, myRaidsCount } = page.props;
    const user = auth?.user;
    const currentPage = page.url;
    const raidsCount =
        typeof myRaidsCount === "number"
            ? myRaidsCount
            : raids?.data?.length ?? 0;

    return (
        <div className="main-layout w-full h-full flex flex-col px-4 sm:px-8 md:px-16 lg:px-32 xl:px-52">

            {/* Navbar */}
            <nav className="flex flex-row items-center justify-between pt-3 sm:pt-5 gap-2 sm:gap-4">
                {/* left side of navbar */}
                <div className="flex flex-row items-center">
                    {/* Logo Component */}
                    <Logo />

                    {/* Title and subtitle */}
                    <div className="flex flex-col ml-2 sm:ml-3">
                        {/* Title */}

                        <p className="ml-1 sm:ml-1.5 text-sm sm:text-base md:text-lg font-semibold">RAID Navigator</p>
                        {/* Text under title */}
                        <p className="ml-1 sm:ml-1.5 text-xs sm:text-sm text-gray-400 hidden sm:block">Course d'orientation Multi-Discipline</p>
                    </div>
                </div>

                {/* Right side of navbar */}
                <div className="flex flex-row items-center gap-2 sm:gap-4">
                    {user ? (
                        <>
                            {/*User info section */}
                            <div ref={wrapperRef} className="relative">
                                <button ref={userButtonRef} onClick={() => setShowUserMenu(!showUserMenu)} className="flex items-center gap-2 hover:cursor-pointer hover:border p-1.5 sm:p-2 rounded-lg hover:bg-blue-100 hover:text-blue-400 transition duration-300">
                                    <div className="flex flex-row items-center">
                                        {/* Profile picture */}
                                        <img src="/SVGS/user.svg" alt="User Profile" className="h-6 w-6 sm:h-8 sm:w-8" style={{ filter: 'brightness(0) saturate(100%) invert(46%) sepia(3%) saturate(199%) hue-rotate(201deg) brightness(93%) contrast(87%)' }} />

                                        {/*User info */}
                                        <div className="flex flex-col ml-2 sm:ml-3 hidden sm:block">
                                            {/*name*/}
                                            <p className="ml-1.5 text-sm md:text-md font-semibold">{user.name}</p>
                                            {/*mail*/}
                                            <p className="ml-1.5 text-xs sm:text-sm text-gray-400">{user.email}</p>
                                        </div>
                                    </div>
                                </button>

                                {showUserMenu && (
                                    <div className="absolute right-0 mt-2 bg-white border rounded-lg shadow-lg z-50" style={{ width: menuWidth ? `${menuWidth}px` : undefined }}>
                                        <Link href="/dashboard" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</Link>
                                        <Link href="/UserAccount" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon compte</Link>
                                    </div>
                                )}
                            </div>
                            
                            {/* Logout section */}
                            <Link href="/logout" className="flex items-center gap-2 hover:cursor-pointer hover:border p-1.5 sm:p-2 rounded-lg hover:bg-red-100 hover:text-red-400 transition duration-300">
                                {/*logout svg */}
                                <img src="/SVGS/logout.svg" alt="Logout Icon" className="h-5 w-5 sm:h-6 sm:w-6" />
                                {/*logout button */}
                                <p className="text-xs sm:text-sm font-semibold hidden sm:block">Déconnexion</p>
                            </Link>
                        </>
                    ) : (
                        <>
                            <button onClick={() => setOverlayLog(true)} className="px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100 rounded-lg transition">
                                Connexion
                            </button>
                            <button onClick={() => setOverlayReg(true)} className="px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition">
                                S'inscrire
                            </button>
                        </>
                    )}
                </div>
            </nav>

            {/* Navigation Links for RAIDs */}
            <div className="flex justify-center mt-6 gap-0 bg-gray-300 rounded-full p-1 w-fit mx-auto">
                <Link href="/test" className={`flex items-center gap-2 px-6 py-2 rounded-full font-semibold transition-all duration-300 ${currentPage === '/test' ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-600 hover:text-gray-700'}`}>
                    <img src="/SVGS/calendar.svg" className="h-5 w-5" />
                    RAIDs disponibles
                </Link>
                <Link href="/my-raids" className={`flex items-center gap-2 px-6 py-2 rounded-full font-semibold transition-all duration-300 ${currentPage === '/my-raids' ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-600 hover:text-gray-700'}`}>
                    <img src="/SVGS/users.svg" className="h-5 w-5" />
                    Mes RAIDs ({raidsCount})
                </Link>
            </div>

            {/*content section */}
            <div className="flex-grow mt-6 mb-6">
                {children}
            </div>
            {/* footer section */}

            <hr className="my-4 border-gray-300" />

            <footer className="py-4">
                <p className="text-center text-xs text-gray-500">© 2024 RAID Navigator. All rights reserved.</p>
            </footer>

            {overlayReg && (
                <Popup setOverlay={setOverlayReg}>
                    <RegisterForm onSwitchToLogin={() => {
                        setOverlayReg(false);
                        setOverlayLog(true);
                    }} />
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
        </div>
    );
}