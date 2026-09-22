export type CarData = {
    id?: number;
    idCar?: number;
    type: string;
    mark: string;
    model: string;
    registrationNumber?: string;
    registration_number?: string;
    nbrPlaces?: number;
    nbr_places?: number;
    color: string;
    carte_grise_numero?: string;
    carte_grise_scan?: string;
    carte_grise_scan_file?: File | null;
    assurance_numero?: string;
    assurance_expiration?: string;
};

export const CAR_BRANDS = [
    'Peugeot', 'Renault', 'Citroën', 'Toyota', 'Volkswagen',
    'Ford', 'Opel', 'BMW', 'Mercedes-Benz', 'Audi', 'Dacia',
    'Hyundai', 'Kia', 'Nissan', 'Fiat', 'Honda', 'Tesla', 'Autre'
];
