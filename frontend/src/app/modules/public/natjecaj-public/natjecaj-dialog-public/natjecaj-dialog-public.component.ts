import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { PrijavaKorisnika } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from 'src/app/shared/services/class/konfiguracijaclass.service';

@Component({
  selector: 'app-natjecaj-dialog-public',
  templateUrl: './natjecaj-dialog-public.component.html',
  styleUrls: ['./natjecaj-dialog-public.component.css'],
})
export class NatjecajDialogPublicComponent implements OnInit {
  form!: FormGroup;

  displayedColumns = ['Ime', 'Prezime', 'Slika'];
  dataSource: PrijavaKorisnika[] = [];
  prijavljeniKorisnici: PrijavaKorisnika[] = [];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: '' | 'asc' | 'desc' = '';

  imeKorisnikaFilter = '';
  prezimeKorisnikaFilter = '';

  counter = 0;

  constructor(
    private fb: FormBuilder,
    private konfiguracijaClass: KonfiguracijaClass,
    public dialogRef: MatDialogRef<NatjecajDialogPublicComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {
    console.log(data);
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    this.form = this.fb.group({
      Naziv: { value: this.data.Naziv, disabled: true },
      Opis: { value: this.data.Opis, disabled: true },
      VrijemePocetka: { value: this.data.VrijemePocetka, disabled: true },
      VrijemeKraja: { value: this.data.VrijemeKraja, disabled: true },
      PoduzeceNaziv: { value: this.data.PoduzeceNaziv, disabled: true },
      StatusNatjecajaNaziv: {
        value: this.data.StatusNatjecajaNaziv,
        disabled: true,
      },
    });

    this.dataSource = this.prijavljeniKorisnici = this.data.Prijavljeni;
    this.ukupnoZapisa = this.dataSource.length;
    this.updatePageData(true);
  }

  mathCeil(value: number, number: number): number {
    return Math.ceil(value / number);
  }

  sortData(sort: any): void {}

  compare(a: string, b: string, isAsc: boolean): number {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiraniKorisnici = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniKorisnici) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.prijavljeniKorisnici = this.prijavljeniKorisnici.slice(
        startIndex,
        endIndex
      );
      return;
    }

    this.dataSource = this.prijavljeniKorisnici.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniKorisnici =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniKorisnici);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniKorisnici =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniKorisnici);
    }
  }
}
