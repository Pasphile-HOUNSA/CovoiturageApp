import { CarIcon } from "lucide-react";

export default function Header() {
  return (
    <header className="bg-linear-to-r from-[#6D9EEB] to-[#245FDB] shadow-md">
      <div className="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="/" className="flex items-center gap-3">
          <span className="flex h-9 w-9 items-center justify-center rounded-full bg-[#EF4444] text-white shadow-sm">
            <CarIcon className="h-5 w-5" />
          </span>
          <span className="text-lg font-bold text-white">MoveTogether</span>
        </a>

        <nav className="hidden items-center gap-8 text-sm font-medium text-white/90 md:flex">
          <a href="#rechercher" className="hover:text-white">Rechercher</a>
          <a href="#comment" className="hover:text-white">Comment ça marche</a>
          <a href="#conducteurs" className="hover:text-white">Conducteurs</a>
        </nav>

        <div className="flex items-center gap-2">
          <a
            href="/login"
            className="hidden rounded-full px-4 py-2 text-sm font-semibold text-white/90 hover:text-white sm:inline-block"
          >
            Connexion
          </a>
          <a
            href="/register"
            className="rounded-full bg-white px-5 py-2 text-sm font-semibold text-[#245FDB] shadow-sm transition hover:bg-[#EEF2F8]"
          >
            Inscription
          </a>
        </div>
      </div>
    </header>
  );
}
