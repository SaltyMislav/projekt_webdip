export interface Natjecaj {
  ID: number;
  Naziv: string;
  VrijemePocetka: string;
  VrijemeKraja: string;
  Opis?: string;
  StatusNatjecajaID: number;
  VrstaStatusa: string;
  PoduzeceID: number;
  NazivPoduzeca: string;
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
  KorisnickoIme: string;
  Email: string;
  UlogaKorisnikaID: number;
  UlogaKorisnikaNaziv: string;
  Active: boolean;
  Blokiran: boolean;
}

export interface Poduzece {
  ID: number;
  Naziv: string;
  Opis: string;
  RadnoVrijemeOd: string;
  RadnoVrijemeDo: string;
}
