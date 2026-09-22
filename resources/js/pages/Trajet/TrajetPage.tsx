import TrajetForm from "@/components/trajets/TrajetForm";
import TrajetList from "@/components/trajets/TrajetList";

export default function TrajetPage() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">Module Trajet</h1>
      <TrajetForm />
      <hr className="my-6" />
      <TrajetList />
    </div>
  );
}
