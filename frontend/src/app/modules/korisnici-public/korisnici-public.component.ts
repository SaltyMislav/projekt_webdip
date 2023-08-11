import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { Zaposlenik } from 'src/app/interfaces/interfaces';
import { KorisniciService } from '../services/korisnici.service';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';

@Component({
  selector: 'app-korisnici-public',
  templateUrl: './korisnici-public.component.html',
  styleUrls: ['./korisnici-public.component.css'],
})
export class KorisniciPublicComponent implements OnInit {
  dataSource: Zaposlenik[] = [];
  zaposlenici: Zaposlenik[] = [];
  sortiraniZaposlenici: Zaposlenik[] = [];
  displayedColumns: string[] = [
    'Ime',
    'Prezime',
    'KorisnickoIme',
    'PoduzeceNaziv',
  ];
  stranicenje!: number;
  ukupnoZaposlenika = 0;
  IndexStranice = 0;

  prezimeFilter = '';

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  constructor(
    private korisniciService: KorisniciService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    this.zaposleniciGet();
  }

  zaposleniciGet(prezime?: any): void {
    this.korisniciService.getZaposlenici(prezime).subscribe({
      next: (result) => {
        this.dataSource = this.zaposlenici = result;
        this.ukupnoZaposlenika = result.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (err) => {
        console.log(err);
      },
    });
  }

  mathCeil(value: number, number: number): number {
    return Math.ceil(value / number);
  }

  sortData(column: string): void {
    if (this.sortColumn === column) {
      this.sortOrder =
        this.sortOrder === 'asc'
          ? 'desc'
          : this.sortOrder === 'desc'
          ? ''
          : 'asc';
    } else {
      this.sortColumn = column;
      this.sortOrder = 'asc';
    }

    const sortedData = this.zaposlenici.slice();

    sortedData.sort((a: any, b: any) => {
      const isAsc = this.sortOrder === 'asc';
      switch (column) {
        case 'Prezime':
          return this.compare(a.Prezime, b.Prezime, isAsc);
        default:
          return 0;
      }
    });
    this.dataSource = this.sortiraniZaposlenici = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const prezime = this.prezimeFilter
      ? this.prezimeFilter.trim().toLowerCase()
      : '';

    if (prezime === '') {
      this.IndexStranice = 0;
      this.zaposleniciGet();
      return;
    } else {
      this.IndexStranice = 0;
      this.zaposleniciGet(prezime);
    }
  }

  clearFilter(): void {
    this.prezimeFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.zaposleniciGet();
  }

  compare(a: string, b: string, isAsc: boolean): number {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiraniKorisnici = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZaposlenika / this.stranicenje - 1)
        endIndex = this.ukupnoZaposlenika;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniKorisnici) {
      if (this.IndexStranice >= this.ukupnoZaposlenika / this.stranicenje - 1)
        endIndex = this.ukupnoZaposlenika;
      this.dataSource = this.sortiraniZaposlenici.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.zaposlenici.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZaposlenika / this.stranicenje - 1) {
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
