import { useMemo } from 'react';
import { usePage } from '@inertiajs/react';

interface User {
    idUser?: number;
    document?: {
        numPermis?: string;
        scanPermis?: string;
        certificatApt?: string;
        certificatApt_scan?: string;
    };
    car?: {
        mark?: string;
        model?: string;
        registrationNumber?: string;
        nbrPlaces?: number;
        carte_grise_scan?: string;
        assurance_numero?: string;
        assurance_expiration?: string;
    };
}

interface DocumentCarCheckResult {
    documentComplete: boolean;
    carComplete: boolean;
    canPublishRide: boolean;
    missingDocuments: string[];
    missingCarInfo: string[];
}

export function useDocumentCarCheck(): DocumentCarCheckResult {
    const { props } = usePage();
    const user = (props.auth?.user as User) || null;

    return useMemo(() => {
        const document = user?.document;
        const car = user?.car;

        const documentComplete = !!(
            document &&
            document.numPermis &&
            document.scanPermis &&
            document.certificatApt &&
            document.certificatApt_scan
        );

        const carComplete = !!(
            car &&
            car.mark &&
            car.model &&
            car.registrationNumber &&
            car.nbrPlaces &&
            car.carte_grise_scan &&
            car.assurance_numero &&
            car.assurance_expiration
        );

        const missingDocuments: string[] = [];
        if (!document?.numPermis) missingDocuments.push('Numéro de permis');
        if (!document?.scanPermis) missingDocuments.push('Scan du permis');
        if (!document?.certificatApt) missingDocuments.push('Certificat d\'aptitude');
        if (!document?.certificatApt_scan) missingDocuments.push('Scan du certificat');

        const missingCarInfo: string[] = [];
        if (!car?.mark) missingCarInfo.push('Marque');
        if (!car?.model) missingCarInfo.push('Modèle');
        if (!car?.registrationNumber) missingCarInfo.push('Immatriculation');
        if (!car?.nbrPlaces) missingCarInfo.push('Nombre de places');
        if (!car?.carte_grise_scan) missingCarInfo.push('Carte grise');
        if (!car?.assurance_numero) missingCarInfo.push('Assurance');
        if (!car?.assurance_expiration) missingCarInfo.push('Date assurance');

        return {
            documentComplete,
            carComplete,
            canPublishRide: documentComplete && carComplete,
            missingDocuments,
            missingCarInfo,
        };
    }, [user?.document, user?.car]);
}
