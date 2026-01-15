import MainLayout from '../../Layout/MainLayout';
import { Head, useForm } from '@inertiajs/react';

export default function ModificationCourse({ course, users, raid_id, course_id, priceUnder18: propPriceUnder18, priceOver18: propPriceOver18 }) {

    // Robust parsing for date/time that accepts 'YYYY-MM-DD HH:MM:SS' or ISO strings with 'T' 
    function parseDateTime(s) {
        if (!s) return { date: '', time: '' };
        let str = String(s);
        // Replace T with space for ISO strings
        str = str.replace('T', ' ');
        if (str.indexOf(' ') >= 0) {
            const parts = str.split(' ');
            const datePart = parts[0];
            let timePart = parts[1] || '';
            if (timePart.indexOf(':') >= 0) {
                const tp = timePart.split(':');
                timePart = `${tp[0].padStart(2, '0')}:${(tp[1] || '00').padStart(2, '0')}`;
            }
            return { date: datePart, time: timePart };
        }
        // Fallback to Date parsing
        const dt = new Date(str);
        if (isNaN(dt.getTime())) return { date: '', time: '' };
        const pad = (n) => String(n).padStart(2, '0');
        return { date: `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}`, time: `${pad(dt.getHours())}:${pad(dt.getMinutes())}` };
    }

    const { date: startDate, time: startTime } = parseDateTime(course?.CRS_DATE_HEURE_DEPART);
    const { date: endDate, time: endTime } = parseDateTime(course?.CRS_DATE_HEURE_FIN);

    const rawCrsType = String(course?.CRS_TYPE ?? '');
    const typeParts = rawCrsType.split('-');
    const initialType = String(typeParts[0] ?? '').trim();
    const initialGenre = String(typeParts[1] ?? '').trim().toLowerCase();

    const { data, setData, post, processing, errors, reset } = useForm({
            title: course?.CRS_NOM ?? '',
            type: initialType,
            genre: initialGenre,
            duration: course?.CRS_DUREE ?? '',
            dateB: startDate,
            hourB: startTime,
            dateE: endDate,
            hourE: endTime,
            participantsMin: course?.CRS_MIN_PARTICIPANTS ?? '',
            participantsMax: course?.CRS_MAX_PARTICIPANTS ?? '',
            teamMin: course?.CRS_NB_EQUIPE_MIN ?? '',
            teamMax: course?.CRS_NB_EQUIPE_MAX ?? '',
            participantNbByTeam: course?.CRS_MAX_PARTICIPANTS_EQUIPE ?? '',
            use_id: String(course?.USE_ID ?? ''),
            mealPrice: course?.CRS_PRIX_REPAS ?? '',
            priceUnder18: propPriceUnder18 ?? course?.CRS_PRIX_MOINS_18 ?? '',
            priceOver18: propPriceOver18 ?? course?.CRS_PRIX_PLUS_18 ?? '',
            discount: course?.CRS_REDUC_LICENCIE ?? '',
            difficulte: course?.CRS_DIFFICULTE ?? '',
            // Ensure raid/course ids are present in the form data
            raid_id: String(raid_id ?? ''),
            course_id: String(course_id ?? ''),
    });
 
    const submit = (e) => {
        e.preventDefault();
        const payload = { ...data };
        payload.type = `${payload.type}-${payload.genre}`;
        payload.raid_id = raid_id;
        payload.course_id = course_id;

        const dataToSend = {
            ...payload
        };
        delete dataToSend.genre;

        post('/courses/update', dataToSend);
    };
    
    return (
        <MainLayout>
            <Head title="Modifier cette Course" />

            <div className="p-8">
                {/* En-tête de la page */}
                <h2 className="text-4xl font-bold mb-2">Modifier cette course</h2>
                <h3 className="text-gray-400 text-lg font-semibold mb-8">
                    Modifier les donées souhaitées pour cette course
                </h3>

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
                    {/* hidden ids to ensure server receives them even if props are missing */}
                    <input type="hidden" name="raid_id" value={data.raid_id} />
                    <input type="hidden" name="course_id" value={data.course_id} />
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
                                    placeholder={course?.CRS_NOM ?? 'Nom de course'}
                                    className="border border-gray-100 py-2 px-4 w-full rounded bg-gray-100 outline-none focus:ring-2 focus:ring-gray-200" 
                                    value={data.title}
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
                                    <label htmlFor="duration" className="font-semibold block mb-1">Durée:</label>
                                    <input type="text" 
                                        id="duration" 
                                        name="duration" 
                                        required 
                                        placeholder={course?.CRS_DUREE ?? 'Durée de la course'}
                                        value={data.duration}
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
                                            <option key={user.USE_ID} value={String(user.USE_ID)}>{user.USE_NOM} {user.USE_PRENOM}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="flex gap-4">
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
                                Modifier la course
                            </button>
                            
                        </div>                                      
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
