export default function Popup({ children, setOverlay, showClose = true }) {
    const handleBackdropClick = (e) => {
        if (e.target === e.currentTarget) {
            setOverlay(false);
        }
    };

    return (
        <div
            onMouseDown={handleBackdropClick}
            className="fixed top-0 left-0 h-screen w-full grid place-items-center p-10 bg-black/40"
        >
            <div
                className="popup-scroll relative w-full max-w-[500px] max-h-[90vh] overflow-y-auto bg-white rounded-lg my-5"
            >
                {showClose && (
                    <button
                        type="button"
                        aria-label="Close popup"
                        className="absolute right-3 top-3 rounded-full p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100"
                        onClick={() => setOverlay(false)}
                    >
                        ❌
                    </button>
                )}
                {children}
            </div>
        </div>
    );
}