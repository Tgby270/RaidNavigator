import MainLayout from '../../Layout/MainLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function CreateCourse({users, raid_id = null, raid = null}){

    const { data, setData, post, processing, errors, reset } = useForm({
            title: '',
            type: '',
            genre: '',
            duration: '',
            dateB: '',
            hourB: '',
            dateE: '',
            hourE: '',
            participantsMin: '',
            participantsMax: '',
            participantNbByTeam: '',
            teamMin: '',
            teamMax: '',
            use_id: '',
            mealPrice: '',
            priceUnder18: '',
            priceOver18: '',
            discount: '',
            difficulte: '',
    });
 
    const submit = (e) => {
        e.preventDefault();
        data.type = `${data.type}-${data.genre}`;
        
        const dataToSend = {
            ...data,
            type: data.type
        };
        delete dataToSend.genre;
        
        // Utiliser le raid_id dans l'URL de soumission
        const submitUrl = raid_id ? `/courses/${raid_id}/create` : '/courses/create';
        post(submitUrl, dataToSend);
    };
    
    return (
        <MainLayout>
            <Head title="Créer une nouvelle Course" />

            <div className="p-8">
                {/* Bouton retour */}
                {raid_id && (
                    <Link 
                        href={`/raid/${raid_id}`}
                        className="inline-flex items-center px-4 py-2 bg-gray-200 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors mb-4"
                    >
                        <span className="mr-2">←</span> Retour au raid
                    </Link>
                )}

                {/* En-tête de la page */}
                <h2 className="text-4xl font-bold mb-2">
                    Créer une nouvelle Course
                    {raid && <span className="text-2xl text-gray-600"> pour {raid.raid_nom}</span>}
                </h2>
                <h3 className="text-gray-400 text-lg font-semibold mb-8">
                    Remplissez ces détails pour créer une nouvelle course
                </h3>

                {/* Informations sur les dates du raid */}
                {raid && (
                    <div className="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <div className="flex items-center">
                            <svg className="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                            </svg>
                            <p className="text-sm font-medium text-blue-800">
                                La course doit se dérouler entre le <strong>{new Date(raid.raid_date_debut).toLocaleDateString('fr-FR')}</strong> et le <strong>{new Date(raid.raid_date_fin).toLocaleDateString('fr-FR')}</strong>
                            </p>
                        </div>
                    </div>
                )}

                {/* Affichage des erreurs */}
                {Object.keys(errors).length > 0 && (
                    <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong className="font-bold">Erreurs:</strong>
                        <ul className="list-disc list-inside">
                            {Object.keys(errors).map((key) => (
                                <li key={key}>{errors[key]}</li>
                            ))}
                        </ul>
                    </div>
                )}

                <form onSubmit={submit}>
                    <div className="flex flex-col lg:flex-row gap-20 items-start">
                        <div className="flex flex-col space-y-6 flex-1">
                            <div>
                                <label htmlFor="title" className="font-semibold block mb-1">Nom de la Course:</label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    required 
                                    autoFocus
                                    placeholder='Nom de course'
                                    className="border border-gray-100 py-2 px-4 w-full rounded bg-gray-100 outline-none focus:ring-2 focus:ring-gray-200" 
                                    onChange={(e) => setData('title', e.target.value)}
                                    />
                            </div>
                            <div className="flex gap-4">
                                <div className="flex-1">
                                    <label className="font-semibold block mb-2">Type de course:</label>
                                    <div className="flex gap-4">
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="type" 
                                                value="loisirs"
                                                required 
                                                checked={data.type === 'loisirs'}
                                                onChange={(e) => setData('type', e.target.value)}
                                                className="w-4 h-4 text-green-600 cursor-pointer" />
                                            <span>Loisirs</span>
                                        </label>
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="type" 
                                                value="compétitif"
                                                required 
                                                checked={data.type === 'compétitif'}
                                                onChange={(e) => setData('type', e.target.value)}
                                                className="w-4 h-4 text-green-600 cursor-pointer" />
                                            <span>Compétitif</span>
                                        </label>
                                    </div>
                                </div>
                                <div className="flex-1">
                                    <label className="font-semibold block mb-2">Genre:</label>
                                    <div className="flex gap-4">
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="genre" 
                                                value="mixte"
                                                required 
                                                checked={data.genre === 'mixte'}
                                                onChange={(e) => setData('genre', e.target.value)}
                                                className="w-4 h-4 text-green-600 cursor-pointer" />
                                            <span>Mixte</span>
                                        </label>
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="genre" 
                                                value="homme"
                                                required 
                                                checked={data.genre === 'homme'}
                                                onChange={(e) => setData('genre', e.target.value)}
                                                className="w-4 h-4 text-green-600 cursor-pointer" />
                                            <span>Homme</span>
                                        </label>
                                        <label className="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="genre" 
                                                value="femme"
                                                required 
                                                checked={data.genre === 'femme'}
                                                onChange={(e) => setData('genre', e.target.value)}
                                                className="w-4 h-4 text-green-600 cursor-pointer" />
                                            <span>Femme</span>
                                        </label>
                                    </div>
                                </div>
                                <div className="flex-1">
                                    <label htmlFor="duration" className="font-semibold block mb-1">Durée (nombre en minutes):</label>
                                    <input type="text" 
                                        id="duration" 
                                        name="duration" 
                                        required 
                                        placeholder='Durée de la course'
                                        onChange={(e) => setData('duration', e.target.value)}
                                        className="border border-gray-100 py-2 px-4 w-full rounded bg-gray-100" />
                                </div>
                            </div>

                            <div>
                                <label className="font-semibold block mb-1 flex items-center gap-2">
                                    Date et heure du départ*
                                </label>
                                <div className="flex flex-row items-center gap-2">
                                    <input 
                                        type="date" 
                                        id="dateB" 
                                        name="dateB" 
                                        required
                                        value={data.dateB}
                                        onChange={(e) => setData('dateB', e.target.value)}
                                        min={raid ? new Date(raid.raid_date_debut).toISOString().split('T')[0] : undefined}
                                        max={raid ? new Date(raid.raid_date_fin).toISOString().split('T')[0] : undefined}
                                        className="border border-gray-100 py-2 px-3 flex-1 rounded bg-gray-100" />
                                    <span className="font-bold text-gray-400">-</span>
                                    <input 
                                        type="time" 
                                        id="hourB" 
                                        name="hourB" 
                                        required 
                                        value={data.hourB}
                                        onChange={(e) => setData('hourB', e.target.value)}
                                        className="border border-gray-100 py-2 px-3 w-32 rounded bg-gray-100" />
                                </div>
                            </div>

                            <div>
                                <label className="font-semibold block mb-1">Date et heure de fin*</label>
                                <div className="flex flex-row items-center gap-2">
                                    <input 
                                        type="date" 
                                        id="dateE" 
                                        name="dateE" 
                                        required 
                                        value={data.dateE}
                                        onChange={(e) => setData('dateE', e.target.value)}
                                        min={raid ? new Date(raid.raid_date_debut).toISOString().split('T')[0] : undefined}
                                        max={raid ? new Date(raid.raid_date_fin).toISOString().split('T')[0] : undefined}
                                        className="border border-gray-100 py-2 px-3 flex-1 rounded bg-gray-100" />
                                    <span className="font-bold text-gray-400">-</span>
                                    <input 
                                        type="time" 
                                        id="hourE" 
                                        name="hourE" 
                                        required 
                                        value={data.hourE}
                                        onChange={(e) => setData('hourE', e.target.value)}
                                        className="border border-gray-100 py-2 px-3 w-32 rounded bg-gray-100" />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="font-semibold block mb-1">Participants (Min / Max):</label>
                                    <div className="flex gap-2">
                                        <input 
                                            type="number" 
                                            id="participantsMin" 
                                            name="participantsMin" 
                                            placeholder="Min" 
                                            value={data.participantsMin}
                                            onChange={(e) => setData('participantsMin', e.target.value)}
                                            className="w-full border border-gray-100 py-2 px-3 rounded bg-gray-100" />
                                        <input 
                                            type="number" 
                                            id="participantsMax" 
                                            name="participantsMax" 
                                            placeholder="Max" 
                                            value={data.participantsMax}
                                            onChange={(e) => setData('participantsMax', e.target.value)}
                                            className="w-full border border-gray-100 py-2 px-3 rounded bg-gray-100" />
                                    </div>
                                </div>
                                <div className="flex gap-4">
                                    <div className="flex-1">
                                        <label htmlFor="teamMin" className="font-semibold block mb-1 text-sm">Nombre d'équipes minimum:</label>
                                        <input 
                                            type="number" 
                                            id="teamMin" 
                                            name="teamMin" 
                                            value={data.teamMin}
                                            onChange={(e) => setData('teamMin', e.target.value)}
                                            className="w-full border border-gray-100 py-2 px-3 rounded bg-gray-100" 
                                            required />
                                    </div>
                                    <div className="flex-1">
                                        <label htmlFor="teamMax" className="font-semibold block mb-1 text-sm">Nombre d'équipes maximum:</label>
                                        <input 
                                            type="number" 
                                            id="teamMax" 
                                            name="teamMax" 
                                            value={data.teamMax}
                                            onChange={(e) => setData('teamMax', e.target.value)}
                                            className="w-full border border-gray-100 py-2 px-3 rounded bg-gray-100" 
                                            required/>
                                    </div>
                                    <div className="flex-1"> 
                                        <label htmlFor="participantNbByTeam" className="font-semibold block mb-1 text-sm">Nb participants/équipe:</label>
                                        <input 
                                            type="number" 
                                            id="participantNbByTeam" 
                                            name="participantNbByTeam" 
                                            required
                                            value={data.participantNbByTeam}
                                            onChange={(e) => setData('participantNbByTeam', e.target.value)}
                                            className="border border-gray-100 py-2 px-3 w-full rounded bg-gray-100" />
                                    </div>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label htmlFor="manager" className="font-semibold block mb-1">Responsable*</label>
                                    <select 
                                        name="use_id" 
                                        id="use_id" 
                                        /*required*/
                                        value={data.use_id}
                                        onChange={(e) => setData('use_id', e.target.value)}
                                        className="border border-gray-100 py-2 px-3 w-full rounded bg-gray-100">
                                        <option value="" disabled>Choisir un responsable</option>
                                        {users.map((user) => (
                                            <option key={user.USE_ID} value={user.USE_ID}>{user.USE_NOM} {user.USE_PRENOM}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="flex gap-4">
                                    <div className="flex-1">
                                        <label htmlFor="priceUnder18" className="font-semibold block mb-1 text-sm">Prix {'<'} à 18 ans (€):</label>
                                        <input 
                                            type="number" 
                                            id="priceUnder18" 
                                            name="priceUnder18" 
                                            step="0.01" 
                                            required
                                            value={data.priceUnder18}
                                            onChange={(e) => setData('priceUnder18', e.target.value)}
                                            className="border border-gray-100 py-2 px-3 w-full rounded bg-gray-100" />
                                    </div>
                                    <div className="flex-1">
                                        <label htmlFor="priceOver18" className="font-semibold block mb-1 text-sm">Prix {'>'} à 18 ans (€):</label>
                                        <input 
                                            type="number" 
                                            id="priceOver18" 
                                            name="priceOver18" 
                                            step="0.01" 
                                            required
                                            value={data.priceOver18}
                                            onChange={(e) => setData('priceOver18', e.target.value)}
                                            className="border border-gray-100 py-2 px-3 w-full rounded bg-gray-100" />
                                    </div>
                                    <div className="flex-1">
                                        <label htmlFor="mealPrice" className="font-semibold block mb-1 text-sm">Prix repas (€):</label>
                                        <input 
                                            type="number" 
                                            id="mealPrice" 
                                            name="mealPrice" 
                                            step="0.01" 
                                            required
                                            value={data.mealPrice}
                                            onChange={(e) => setData('mealPrice', e.target.value)}
                                            className="border border-gray-100 py-2 px-3 w-full rounded bg-gray-100" />
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label htmlFor="discount" className="font-semibold block mb-1">Réduction licenciés (facultatif):</label>
                                <input 
                                    type="text" 
                                    id="discount" 
                                    name="discount" 
                                    value={data.discount}
                                    onChange={(e) => setData('discount', e.target.value)}
                                    className="border border-gray-100 py-2 px-4 w-full rounded bg-gray-100" />
                            </div>
                            <div>
                                <label htmlFor="difficulte" className="font-semibold block mb-1">Difficulté:</label>
                                <input 
                                    type="text" 
                                    id="difficulte" 
                                    name="difficulte" 
                                    value={data.difficulte}
                                    onChange={(e) => setData('difficulte', e.target.value)}
                                    className="border border-gray-100 py-2 px-4 w-full rounded bg-gray-100" />
                            </div>

                            <button
                                type="submit" 
                                className="w-full py-3 px-10 bg-green-500 font-semibold text-white rounded-lg transition duration-300 shadow-lg hover:bg-green-800 active:scale-95"
                            >
                                Créer la Course
                            </button>
                            
                        </div>                                      
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
