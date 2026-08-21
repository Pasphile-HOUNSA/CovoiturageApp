export default function Footer() {
  return (
    <footer className="bg-[#1E293B] pb-24 pt-10 text-white sm:pb-10">
      <div className="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 px-6 sm:flex-row sm:items-center">
        <span className="text-lg font-bold">Mon site de covoiturage</span>
        <div className="flex gap-6 text-sm text-white/70">
          <a href="#rechercher" className="hover:text-white">Rechercher</a>
          <a href="#comment" className="hover:text-white">Comment ça marche</a>
          <a href="#conducteurs" className="hover:text-white">Conducteurs</a>
        </div>
        <span className="text-xs text-white/40">© 2026 — Cotonou</span>
      </div>
    </footer>
  );
}
