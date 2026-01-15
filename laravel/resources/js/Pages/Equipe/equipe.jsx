import MainLayout from '../../Layout/MainLayout';
import axios from 'axios';
import { useState } from 'react';

export default function Equipe({ equipe }) {
    const membres = Array.isArray(equipe) ? equipe : (equipe?.membres || equipe?.members || []);
    const team = Array.isArray(equipe) ? {} : (equipe || {});
    const membresCount = membres.length;
    const maxMembers = team.crs_max ?? '—';

    // Get CSRF token from meta tag
    const getCsrfToken = () => {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    };

    const handleDelete = async (membreId) => {
        if (!confirm('Voulez‑vous vraiment supprimer ce membre ?')) return;
        const raid = team.RAID_ID ?? team.raid_id ?? '';
        const crs = team.CRS_ID ?? team.crs_id ?? '';
        const equ = team.EQU_ID ?? team.equ_id ?? '';
        const csrf = getCsrfToken();
        try {
            await axios.post(`/equipe/deleteMember/${membreId}`, {
                RAID_ID: raid,
                CRS_ID: crs,
                EQU_ID: equ,
                _method: 'DELETE'
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            });
            window.location.reload();
        } catch (err) {
            console.error(err);
            alert('Erreur lors de la suppression du membre.');
        }
    };

    return (
        <MainLayout>
        <h1>Nom de l'équipe</h1>
            <img src="/chemin/vers/image.jpg" alt="Image de l'équipe" />

            <p>Raid : {team.raid_id}</p>
            <p>Course : {team.crs_id}</p>

            {team.equ_est_payee ? (
                <p>Vous avez payé</p>
            ) : (
                <p>Le montant actuel à payer est de {team.equ_montant} euros</p>
            )}
            <p>Les membres de l'équipe sont ({membresCount} sur {maxMembers}):</p>
            <ul>
                {membres.map((membre) => (
                    <li key={membre.id ?? membre.nom}>
                        {membre.nom}
                        <button
                            type="button"
                            onClick={() => handleDelete(membre.id)}
                        >
                            Supprimer
                        </button>
                    </li>
                ))}
            </ul>

            {membresCount < (team.crs_max ?? 0) ? (
                <a href="/equipe/add"><button type="button">Ajouter un membre</button></a>
            ) : (
                <p>L'équipe est complète !</p>
            )}
        </MainLayout>
    );
}