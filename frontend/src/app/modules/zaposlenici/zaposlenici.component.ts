import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../../auth/authentication.service';
import { ZaposlenikPrivatno } from '../../interfaces/interfaces';
import { KonfiguracijaClass } from '../../shared/services/class/konfiguracijaclass.service';
import { KorisniciService } from '../../shared/services/korisnici.service';

@Component({
  selector: 'app-zaposlenici',
  templateUrl: './zaposlenici.component.html',
  styleUrls: ['./zaposlenici.component.css'],
})
export class ZaposleniciComponent implements OnInit {
  dataSource: ZaposlenikPrivatno[] = [];
  zaposlenici: ZaposlenikPrivatno[] = [];
  sortiraniZaposlenici: ZaposlenikPrivatno[] = [];

  displayedColumns: string[] = [
    'Ime',
    'Prezime',
    'PoduzeceNaziv',
    'BrojOdradenihZadataka',
    'BrojNeodradenihZadataka',
  ];
  stranicenje!: number;
  ukupnoZaposlenika = 0;
  IndexStranice = 0;

  imeFilter = '';
  prezimeFilter = '';

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  counter = 0;

  constructor(
    private korisniciService: KorisniciService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass,
    protected authService: AuthenticationService
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    this.zaposleniciGet();
    if (this.authService.isAdmin()) {
      this.displayedColumns.push('BrojDolazakaNaPosao');
    }
  }

  zaposleniciGet(data?: any): void {
    this.korisniciService.getZaposleniciPrivatno(data).subscribe({
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
    console.log(column);
    if (this.sortColumn === column) {
      this.counter++;
      this.sortOrder =
        this.sortOrder === 'asc'
          ? 'desc'
          : this.sortOrder === 'desc'
          ? ''
          : 'asc';
    } else {
      this.sortOrder = 'asc';
      this.sortColumn = column;
      this.counter++;
    }

    const sortedData = this.zaposlenici.slice();

    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a, b) => {
        const isAsc = this.sortOrder === 'asc';
        switch (column) {
          case 'Ime':
            return this.compare(a.Ime, b.Ime, isAsc);
          case 'Prezime':
            return this.compare(a.Prezime, b.Prezime, isAsc);
          case 'PoduzeceNaziv':
            return this.compare(a.PoduzeceNaziv, b.PoduzeceNaziv, isAsc);
          case 'BrojDolazakaNaPosao':
            return this.compare(
              a.BrojDolazakaNaPosao,
              b.BrojDolazakaNaPosao,
              isAsc
            );
          case 'BrojOdradenihZadataka':
            return this.compare(
              a.BrojOdradenihZadataka,
              b.BrojOdradenihZadataka,
              isAsc
            );
          case 'BrojNeodradenihZadataka':
            return this.compare(
              a.BrojNeodradenihZadataka,
              b.BrojNeodradenihZadataka,
              isAsc
            );
          default:
            return 0;
        }
      });

    }

    this.dataSource = this.sortiraniZaposlenici = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const data = {
      Ime: this.imeFilter ? this.imeFilter.trim().toLowerCase() : '',
      Prezime: this.prezimeFilter
        ? this.prezimeFilter.trim().toLowerCase()
        : '',
    };

    if (data.Ime === '' && data.Prezime === '') {
      this.IndexStranice = 0;
      this.zaposleniciGet();
      return;
    }

    this.IndexStranice = 0;
    this.zaposleniciGet(data);
  }

  clearFilter(): void {
    this.imeFilter = '';
    this.prezimeFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.IndexStranice = 0;
    this.zaposleniciGet();
  }

  compare(a: string | number, b: string | number, isAsc: boolean): number {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiraniZaposlenici = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZaposlenika / this.stranicenje - 1)
        endIndex = this.ukupnoZaposlenika;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniZaposlenici) {
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
      const sortiraniZaposlenici =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniZaposlenici);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniZaposlenici =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniZaposlenici);
    }
  }
}
