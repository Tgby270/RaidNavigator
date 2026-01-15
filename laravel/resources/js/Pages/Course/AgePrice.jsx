import { useState } from "react";

export default function AgePriceTiersForm({ eventId }) {

  const [tiers, setTiers] = useState([
    { age_min: 0, age_max: 12, price: 5 },
  ]);

  const addRow = () =>
    setTiers([...tiers, { age_min: "", age_max: "", price: "" }]);

  const removeRow = (index) =>
    setTiers(tiers.filter((_, i) => i !== index));

  const updateField = (index, field, value) => {
    const updated = [...tiers];
    updated[index][field] = value;
    setTiers(updated);
  };

  /*
  const save = async () => {
    await fetch("/api/age-tiers", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        event_id: eventId,
        tiers,
      }),
    });
    alert("Tarifs enregistrés");
  };
  */

  return (
    <div>
      <h3>Tarifs par tranches d'âge</h3>

      <table>
        <thead>
          <tr>
            <th>Âge min</th>
            <th>Âge max</th>
            <th>Prix (€)</th>
            <th></th>
          </tr>
        </thead>

        <tbody>
          {tiers.map((tier, i) => (
            <tr key={i}>
              <td>
                <input
                  type="number"
                  value={tier.age_min}
                  onChange={e => updateField(i, "age_min", e.target.value)}
                />
              </td>
              <td>
                <input
                  type="number"
                  value={tier.age_max}
                  onChange={e => updateField(i, "age_max", e.target.value)}
                />
              </td>
              <td>
                <input
                  type="number"
                  step="0.01"
                  value={tier.price}
                  onChange={e => updateField(i, "price", e.target.value)}
                />
              </td>
              <td>
                <button onClick={() => removeRow(i)}>Supprimer</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <button onClick={addRow}>Ajouter une tranche</button>
      {'<button onClick={save}>Enregistrer</button>'}
    </div>
  );
}
