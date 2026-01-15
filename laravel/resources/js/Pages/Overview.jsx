import MainLayout from '../Layout/MainLayout';
import RAIDCard from '../Components/RAIDCard';
import { Link } from '@inertiajs/react';

export default function Overview({ raids }) {
    const items = Array.isArray(raids) ? raids : raids?.data ?? [];
    const links = raids?.links ?? []; // Pagination links if available

    console.log('RAIDs:', items);
    return (
        <MainLayout>
            <div className="p-4 sm:p-6 lg:p-8">
                <Link
                    href="/"
                    className="inline-flex items-center px-4 py-1 bg-gray-200 text-sm font-medium rounded-full hover:bg-gray-300 transition-colors mb-4 sm:mb-6"
                >
                    <span className="mr-2">←</span> Retour
                </Link>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch justify-items-center">
                    {items.length === 0 ? (
                        <p className="text-gray-500 items-center">Aucun raid à afficher.</p>
                    ) : (
                        items.map((raid) => (
                            console.log(raid.age_min + ' - ' + raid.age_max),
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
                <div className="flex flex-wrap gap-2 mt-4 justify-center">
                    {links.map((link, i) => (
                        <Link
                            key={i}
                            href={link.url || '#'}
                            className={`px-3 py-1 rounded ${link.active ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'} ${!link.url ? 'opacity-50 cursor-default' : ''}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                            preserveScroll
                        />
                    ))}
                </div>
            </div>
        </MainLayout>
    );
}
