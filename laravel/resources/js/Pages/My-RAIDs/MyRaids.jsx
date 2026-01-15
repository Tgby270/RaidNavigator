import MainLayout from '../../Layout/MainLayout';
import RAIDCard from '../../Components/RAIDCard';
import { Link, usePage } from '@inertiajs/react';

export default function MyRaids({ raids }) {
    const items = raids?.data ?? [];
    const links = raids?.links ?? []; // Pagination links if available   
    const currentPage = usePage().url;

    // Calculate statistics from raids
    const stats = {
        //NUmber of raids the user participated in
        participated: items.length,
        
        // raids where raid_date_fin < today
        completed: items.filter(raid => {
            const endDate = new Date(raid.raid_date_fin);
            return endDate < new Date();
        }).length,
        
        // average_rank = would need to come from backend (join EQUIPE -> RESULTATS)
        averageRank: raids?.average_rank ?? 0 // to do
    };

    return (
        <MainLayout>
            {/*top button to switch from al the raid to the one you participated in*/}

            {console.log("Raids data:", raids, "items:", items)}

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 mb-8">
                <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <div>
                        <p className="text-gray-600 text-sm font-medium">Nombre de RAID participé</p>
                        <p className="text-4xl font-bold text-gray-900 mt-2">{stats.participated}</p>
                    </div>
                </div>

                <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <div>
                        <p className="text-gray-600 text-sm font-medium">Nombres de RAID finis</p>
                        <p className="text-4xl font-bold text-gray-900 mt-2">{stats.completed}</p>
                    </div>
                </div>

                <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <div>
                        <p className="text-gray-600 text-sm font-medium">Rang moyen</p>
                        <p className="text-4xl font-bold text-gray-900 mt-2">{stats.averageRank}<span className="text-lg">°</span></p>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 items-stretch justify-items-center">
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
                            route={`/my-raids/${raid.RAID_ID}`}
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

            
        </MainLayout>
    );
}