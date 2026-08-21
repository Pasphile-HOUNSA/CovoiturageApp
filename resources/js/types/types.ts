export interface Trajet {
  depart: string;
  arrivee: string;
  prix: string;
  duree?: string;
  places?: number;
  date?: string;
}

export interface Conducteur {
  nom: string;
  membreDepuis: string;
  note: number;
  avatarUrl?: string;
  trajetsRecents: Trajet[];
}
