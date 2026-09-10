import { useState } from "react";
import axios from "axios";

export default function TrajetForm() {
  const [formData, setFormData] = useState({
    idCar: "",
    lieuDepart: "",
    lieuArrivee: "",
    dateDepart: "",
    heureDepart: "",
    price: "",
    placesDispo: "",
    statut: ""
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await axios.post("/api/trajets", formData);
      alert("Trajet créé avec succès !");
    } catch (error) {
      alert("Erreur lors de la création du trajet");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-3">
      <input name="idCar" placeholder="ID voiture" onChange={handleChange} />
      <input name="lieuDepart" placeholder="Lieu de départ" onChange={handleChange} />
      <input name="lieuArrivee" placeholder="Lieu d’arrivée" onChange={handleChange} />
      <input type="date" name="dateDepart" onChange={handleChange} />
      <input type="time" name="heureDepart" onChange={handleChange} />
      <input name="price" placeholder="Prix" onChange={handleChange} />
      <input name="placesDispo" placeholder="Places disponibles" onChange={handleChange} />
      <input name="statut" placeholder="Statut" onChange={handleChange} />
      <button type="submit">Créer Trajet</button>
    </form>
  );
}
