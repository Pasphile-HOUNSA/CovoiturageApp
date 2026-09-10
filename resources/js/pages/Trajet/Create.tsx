import React from "react";
import TrajetForm from "@/components/TrajetForm";

const CreateTrajet: React.FC = () => {
  return (
    <div className="p-6">
      <h1 className="text-xl font-bold mb-4">Créer un trajet 🚗</h1>
      <TrajetForm />
    </div>
  );
};

export default CreateTrajet;
