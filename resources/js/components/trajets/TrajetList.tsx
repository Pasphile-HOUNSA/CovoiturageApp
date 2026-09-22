import { useEffect, useState } from "react";

export default function TrajetList() {
  const [trajets, setTrajets] = useState<any[]>([]);
  const [filters, setFilters] = useState({ lieuDepart: "", lieuArrivee: "", dateDepart: "" });

  useEffect(() => {
    fetchTrajets();
  }, []);

  const fetchTrajets = async () => {
    try {
      const response = await fetch("/trajets");
      if (response.ok) {
        const data = await response.json();
        setTrajets(data);
      } else {
        console.error("Erreur lors du chargement des trajets");
      }
    } catch (error) {
      console.error("Erreur réseau :", error);
    }
  };

  const searchTrajets = async () => {
    try {
      const query = new URLSearchParams(filters).toString();
      const response = await fetch(`/trajets/search?${query}`);
      if (response.ok) {
        const data = await response.json();
        setTrajets(data);
      } else {
        console.error("Erreur lors de la recherche");
      }
    } catch (error) {
      console.error("Erreur réseau :", error);
    }
  };

  return (
    <div>
      <h2>Liste des trajets disponibles</h2>
      <div className="space-x-2">
        <input
          placeholder="Lieu de départ"
          onChange={(e) => setFilters({ ...filters, lieuDepart: e.target.value })}
        />
        <input
          placeholder="Lieu d'arrivée"
          onChange={(e) => setFilters({ ...filters, lieuArrivee: e.target.value })}
        />
        <input
          type="date"
          onChange={(e) => setFilters({ ...filters, dateDepart: e.target.value })}
        />
        <button onClick={searchTrajets}>Rechercher</button>
      </div>

      <ul>
        {trajets.map((t: any) => (
          <li key={t.idTrajet}>
            {t.lieuDepart} → {t.lieuArrivee} ({t.dateDepart} à {t.heureDepart}) - {t.placesDispo} places
          </li>
        ))}
      </ul>
    </div>
  );
}
