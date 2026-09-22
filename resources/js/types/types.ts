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

export interface User {
    idUser: number;
    firstName: string;
    lastName: string;
    email: string;
    sex: string;
    birthday: string;
    phoneNumber: string;
    address: string;
    photoId?: string | null;
    numPiece?: string | null;
    scanPiece?: string | null;
    registrationDate?: string;
    lastLogin?: string;
    isActive?: boolean;
    email_verified_at?: string | null;
}

export type PageProps<T = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    userProfile?: User; // À ajouter pour gérer la vue d'un profil tiers
};
