import MainLayout from '../../Layout/MainLayout';

export default function AjouterMembre({ users = [] }) {
    // Get CSRF token from meta tag
    const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : '';
    return (
        <MainLayout>
            <h1>Ajouter un membre</h1>
            <ul>
                {users.map((user) => (
                    <li key={user.id}>
                        {user.nom} {user.prenom}
                        <form
                            action={`/equipe/add/${user.id}`}
                            method="POST"
                            style={{ display: 'inline' }}
                            onSubmit={(e) => {
                                if (!confirm(`Voulez‑vous vraiment ajouter ${user.nom} ${user.prenom} ?`)) {
                                    e.preventDefault();
                                }
                            }}
                        >
                            <input type="hidden" name="_token" value={csrf} />
                            <button type="submit">Ajouter à l'équipe</button>
                        </form>
                    </li>
                ))}
            </ul>
        </MainLayout>
    );
}