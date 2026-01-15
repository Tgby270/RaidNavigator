import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

export default function ResultsTable({ results = [] }) {
    const formatTime = (seconds) => {
        if (!seconds && seconds !== 0) return '—';
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    };

    const handleExportPDF = () => {
        const doc = new jsPDF();
        doc.text("Résultats de la course", 14, 16);
        const head = [["CLT", "EQUIPE", "CATEGORIE", "TEMPS", "PTS"]];
        const body = results.map((result, index) => [
            index + 1,
            result.EQU_NOM,
            result.CATEGORIE || 'Mixte',
            formatTime(result.RES_TEMPS),
            result.RES_POINTS ?? 0
        ]);
        autoTable(doc, { head, body, startY: 22 });
        doc.save("resultats_course.pdf");
    };

    return (
        <div className="mt-8 mb-8">
            <div className="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div className="bg-gradient-to-r from-green-600 to-green-700 px-6 py-2 flex items-center justify-between">
                    <h2 className="text-2xl font-bold text-white">Résultats de la course</h2>
                    {results && results.length > 0 && (
                        <button
                            onClick={handleExportPDF}
                            className="ml-4 px-4 py-1.5 bg-white text-green-700 font-semibold rounded shadow hover:bg-green-50 border border-green-200"
                        >
                            Exporter en PDF
                        </button>
                    )}
                </div>
                {results && results.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="bg-gray-100 border-b border-gray-200">
                                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">CLT</th>
                                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">EQUIPE</th>
                                    <th className="px-6 py-4 text-center text-sm font-semibold text-gray-700">CATEGORIE</th>
                                    <th className="px-6 py-4 text-center text-sm font-semibold text-gray-700">TEMPS</th>
                                    <th className="px-6 py-4 text-center text-sm font-semibold text-gray-700">PTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                {results.map((result, index) => (
                                    <tr 
                                        key={result.EQU_ID} 
                                        className={`border-b border-gray-100 hover:bg-gray-50 transition-colors ${
                                            index === 0 ? 'bg-yellow-50' : 
                                            index === 1 ? 'bg-gray-50' : 
                                            index === 2 ? 'bg-orange-50' : ''
                                        }`}
                                    >
                                        <td className="px-6 py-4">
                                            <span className="font-semibold text-gray-900">{index + 1}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="font-medium text-gray-900">{result.EQU_NOM}</span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className="text-gray-700">{result.CATEGORIE || 'Mixte'}</span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className="text-gray-700">{formatTime(result.RES_TEMPS)}</span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                                {(result.RES_POINTS ?? 0)}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="px-6 py-12 text-center">
                        <svg className="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p className="text-gray-500 text-lg">Aucun résultat disponible pour cette course</p>
                        <p className="text-gray-400 text-sm mt-2">Les résultats seront affichés une fois la course terminée</p>
                    </div>
                )}
            </div>
        </div>
    );
}
