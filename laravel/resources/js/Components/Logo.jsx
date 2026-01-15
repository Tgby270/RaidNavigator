import { Link } from '@inertiajs/react';

export default function Logo(){
    return (
        <>
            {/* Logo Container */}
            <Link href="/" className="inline-block">
                <div className="rounded-2xl bg-[#010213] h-16 w-16 flex items-center justify-center">
                    {/* Mountain Logo  (brightness-0 invert --> brightness-0 makes it black, invert then makes it white)*/}
                    <img src="/SVGS/mountain.svg" alt="Mountain Logo" className="h-8 w-8 brightness-0 invert" />
                </div>
            </Link>
        </>
    );
}
