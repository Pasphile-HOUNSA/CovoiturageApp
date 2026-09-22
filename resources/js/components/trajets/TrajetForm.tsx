import { useState } from "react";

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
      const response = await fetch("/trajets", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify(formData)
      });

      if (response.ok) {
        alert("Trajet créé avec succès !");
      } else {
        alert("Erreur lors de la création du trajet");
      }
    } catch (error) {
      alert("Erreur réseau ou serveur");
      console.error(error);
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
