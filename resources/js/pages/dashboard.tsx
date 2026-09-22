import { Head, Link } from '@inertiajs/react';
import {
    Car,
    Search,
    PlusCircle,
    Calendar,
    MapPin,
    Users,
    ArrowRight,
    Clock,
    DollarSign
} from 'lucide-react';
import { Button } from '@/components/ui/button';

// --- Types & Interfaces ---
export interface Ride {
    id: number;
    departure: string;
    destination: string;
    departureTime: string;
    availableSeats: number;
    price: number;
    currency?: string;
}

export interface Props {
    auth?: {
        user?: {
            firstName?: string;
            lastName?: string;
            name?: string;
        };
    };
    stats?: {
        activeRides?: number;
        completedRides?: number;
        totalPassengers?: number;
        totalEarnings?: number;
    };
    upcomingRides?: Ride[];
}

// --- Main Dashboard Component ---
export default function Dashboard({ auth, stats, upcomingRides = [] }: Props) {
    const userName = auth?.user?.firstName || auth?.user?.name || 'Conducteur';

    return (
        <>
            <Head title="Tableau de bord" />

            <div className="w-full relative flex flex-col gap-6 px-4 sm:px-6 lg:px-8 py-6 bg-[#EEF2F6] dark:bg-slate-950 min-h-screen">
                <div className="max-w-7xl mx-auto w-full flex flex-col gap-6">
                    {/* 1. Banner de Bienvenue */}
                    <div className="bg-linear-to-r from-[#1D63ED] to-blue-600 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold">Bonjour, {userName} !</h1>
                            <p className="text-blue-100 text-sm mt-1">
                                Prêt pour votre prochain trajet ? Recherchez ou proposez une course dès maintenant.
                            </p>
                        </div>
                        <div className="flex items-center gap-3 w-full md:w-auto">
                            <Link href="/rides/search" className="w-full md:w-auto">
                                <Button className="w-full bg-white text-[#1D63ED] hover:bg-slate-100 font-semibold shadow-sm">
                                    <Search className="w-4 h-4 mr-2" />
                                    Rechercher
                                </Button>
                            </Link>
                            <Link href="/rides/verify-before-publish" className="w-full md:w-auto">
                                <Button className="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold">
                                    <PlusCircle className="w-4 h-4 mr-2" />
                                    Publier
                                </Button>
                            </Link>
                            <Link href="/cars/create" className="hidden md:block">
                                <Button variant="outline" className="text-white border-white hover:bg-white/10 font-semibold">
                                    <Car className="w-4 h-4 mr-2" />
                                    Mes véhicules
                                </Button>
                            </Link>
                        </div>
                    </div>

                    {/* 2. Cartes de Statistiques */}
                    <div className="grid auto-rows-min gap-4 md:grid-cols-4">
                        <div className="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                            <div>
                                <p className="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trajets en cours</p>
                                <h3 className="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{stats?.activeRides ?? 0}</h3>
                            </div>
                            <div className="w-12 h-12 bg-blue-50 text-[#1D63ED] rounded-xl flex items-center justify-center">
                                <Car className="w-6 h-6" />
                            </div>
                        </div>

                        <div className="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                            <div>
                                <p className="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trajets effectués</p>
                                <h3 className="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{stats?.completedRides ?? 0}</h3>
                            </div>
                            <div className="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                                <Calendar className="w-6 h-6" />
                            </div>
                        </div>

                        <div className="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                            <div>
                                <p className="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passagers transportés</p>
                                <h3 className="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{stats?.totalPassengers ?? 0}</h3>
                            </div>
                            <div className="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                                <Users className="w-6 h-6" />
                            </div>
                        </div>

                        <div className="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                            <div>
                                <p className="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gains cumulés</p>
                                <h3 className="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                                    {(stats?.totalEarnings ?? 0).toLocaleString()} FCFA
                                </h3>
                            </div>
                            <div className="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                                <DollarSign className="w-6 h-6" />
                            </div>
                        </div>
                    </div>

                    {/* 3. Section Derniers Trajets / Activité récente */}
                    <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex-1">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-lg font-bold text-slate-800 dark:text-slate-100">Vos prochains trajets</h2>
                            <Link href="/rides" className="text-xs font-semibold text-[#1D63ED] hover:underline flex items-center gap-1">
                                Voir tout <ArrowRight className="w-3 h-3" />
                            </Link>
                        </div>

                        {upcomingRides.length > 0 ? (
                            <div className="space-y-3">
                                {upcomingRides.map((ride) => (
                                    <div
                                        key={ride.id}
                                        className="p-4 rounded-xl border border-slate-100 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-slate-300 transition-colors"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 bg-blue-100 text-[#1D63ED] rounded-lg flex items-center justify-center shrink-0">
                                                <MapPin className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="font-semibold text-slate-800">
                                                    {ride.departure} → {ride.destination}
                                                </p>
                                                <p className="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                                    <span>{ride.departureTime}</span>
                                                    <span>•</span>
                                                    <span>{ride.availableSeats} place{ride.availableSeats > 1 ? 's' : ''} disponible{ride.availableSeats > 1 ? 's' : ''}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                                            <span className="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full whitespace-nowrap">
                                                {ride.price.toLocaleString()} {ride.currency || 'FCFA'}
                                            </span>
                                            <Link href={`/rides/${ride.id}`}>
                                                <Button variant="outline" size="sm" className="text-slate-600 border-slate-300">
                                                    Détails
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="py-12 flex flex-col items-center justify-center text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                <Clock className="w-10 h-10 text-slate-300 mb-3" />
                                <h3 className="text-sm font-semibold text-slate-700">Aucun trajet prévu</h3>
                                <p className="text-xs text-slate-500 mt-1 max-w-sm">
                                    Vous n'avez pas encore de trajet à venir. Proposez une course ou recherchez-en une dès maintenant.
                                </p>
                                <div className="mt-4 flex gap-3">
                                <Link href="/rides/verify-before-publish">
                                        <Button size="sm" className="bg-[#1D63ED] hover:bg-blue-700 text-white">
                                            Publier un trajet
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ],
};
