import { useEffect, useState } from "react";
import axios from "axios";

export default function TrajetList() {
  const [trajets, setTrajets] = useState([]);
  const [filters, setFilters] = useState({ lieuDepart: "", lieuArrivee: "", dateDepart: "" });

  useEffect(() => {
    fetchTrajets();
  }, []);

  const fetchTrajets = async () => {
    const res = await axios.get("/api/trajets");
    setTrajets(res.data);
  };

  const searchTrajets = async () => {
    const res = await axios.get("/api/trajets/search", { params: filters });
    setTrajets(res.data);
  };

  return (
    <div>
      <h2>Liste des trajets disponibles</h2>
      <div className="space-x-2">
        <input placeholder="Lieu départ" onChange={(e) => setFilters({ ...filters, lieuDepart: e.target.value })} />
        <input placeholder="Lieu arrivée" onChange={(e) => setFilters({ ...filters, lieuArrivee: e.target.value })} />
        <input type="date" onChange={(e) => setFilters({ ...filters, dateDepart: e.target.value })} />
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
