export interface Natjecaj {
    ID: number;
    Naziv: string;
    VrijemePocetka: string;
    VrijemeKraja: string;
    Opis?: string;
    StatusNatjecajaID: number;
}

export interface StatusNatjecaja {
    ID: number;
    Naziv: string;
}

export interface DnevnikRada {
    ID: number;
    DatumPromjene: Date;
    Detail: string;
    VrstaPromjeneID: number;
}

export interface VrstaPromjene {
    ID: number;
    Naziv: string;
}

export interface Korisnik {
    ID: number;
    Ime: string;
    Prezime: string;
    Email: string;
    Lozinka: string;
}

