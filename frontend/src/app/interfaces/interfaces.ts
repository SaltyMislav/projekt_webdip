export interface Natjecaj {
  ID: number;
  Naziv: string;
  VrijemePocetka: string;
  VrijemeKraja: string;
  Opis?: string;
  StatusNatjecajaID: number;
  StatusNatjecajaNaziv: string;
  PoduzeceID: number;
  PoduzeceNaziv: string;
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

export interface Zaposlenik {
  ID: number;
  Ime: string;
  Prezime: string;
  PoduzeceID: number;
  PoduzeceNaziv: string;
}

export interface ZaposlenikPrivatno extends Zaposlenik {
  BrojDolazakaNaPosao?: number;
  BrojOdradenihZadataka: number;
  BrojNeodradenihZadataka: number;
}

export interface Korisnik {
  ID: number;
  Ime: string;
  Prezime: string;
  KorisnickoIme: string;
  Email: string;
  UlogaKorisnikaID: number;
  UlogaKorisnikaNaziv: string;
  BrojDolazakaNaPosao: number;
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

export interface UlogaKorisnika {
  ID: number;
  Naziv: string;
}

export interface KorisniciModeratori {
  ID: number;
  KorisnickoIme: string;
}

export interface PrijavaKorisnika {
  ID: number;
  Ime: string;
  Prezime: string;
  Slika: string;
}