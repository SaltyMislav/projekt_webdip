import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Korisnik } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { KorisniciService } from '../services/korisnici.service';
import { KorisniciDialogComponent } from './korisnici-dialog/korisnici-dialog.component';

@Component({
  selector: 'app-korisnici',
  templateUrl: './korisnici.component.html',
  styleUrls: ['./korisnici.component.css'],
})
export class KorisniciComponent implements OnInit {
  dataSource: Korisnik[] = [];
  korisnici: Korisnik[] = [];
  sortiraniKorisnici: Korisnik[] = [];
  applyFilterKorisnici: Korisnik[] = [];
  displayedColumns: string[] = [
    'ID',
    'Ime',
    'Prezime',
    'Email',
    'UlogaKorisnikaNaziv',
    'BrojDolazakaNaPosao',
    'Active',
    'Blokiran',
  ];
  stranicenje!: number;
  ukupnoNatjecaja = 0;
  IndexStranice = 0;

  ulogaFilter = '';
  emailFilter = '';

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  constructor(
    private korisniciService: KorisniciService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass,
    public dialog: MatDialog
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    this.getKorisnici();
  }

  getKorisnici() {
    this.korisniciService.getAllKorisnici().subscribe({
      next: (response) => {
        this.dataSource = this.korisnici = response;
        this.ukupnoNatjecaja = response.length;
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

    if (this.sortOrder === '' && this.applyFilterKorisnici.length === 0) {
      this.updatePageData();
      return;
    } else if (this.sortOrder === '' && this.applyFilterKorisnici.length > 0) {
      this.updatePageData(false, false, true);
      return;
    }

    const sortedData = this.applyFilterKorisnici.length > 0 ? this.applyFilterKorisnici.slice() : this.korisnici.slice();

    sortedData.sort((a, b) => {
      const isAsc = this.sortOrder === 'asc';
      switch (column) {
        case 'ID':
          return this.compare(a.ID, b.ID, isAsc);
        case 'Ime':
          return this.compare(a.Ime, b.Ime, isAsc);
        case 'Prezime':
          return this.compare(a.Prezime, b.Prezime, isAsc);
        case 'Email':
          return this.compare(a.Email, b.Email, isAsc);
        case 'UlogaKorisnikaNaziv':
          return this.compare(
            a.UlogaKorisnikaNaziv,
            b.UlogaKorisnikaNaziv,
            isAsc
          );
        case 'BrojDolazakaNaPosao':
          return this.compare(
            a.BrojDolazakaNaPosao,
            b.BrojDolazakaNaPosao,
            isAsc
          );
        case 'Active':
          return this.compare(a.Active, b.Active, isAsc);
        case 'Blokiran':
          return this.compare(a.Blokiran, b.Blokiran, isAsc);
        default:
          return 0;
      }
    });
    this.dataSource = this.sortiraniKorisnici = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const uloga = this.ulogaFilter ? this.ulogaFilter.trim().toLowerCase() : '';
    const email = this.emailFilter ? this.emailFilter.trim().toLowerCase() : '';

    if (uloga === '' && email === '') {
      this.dataSource = this.korisnici;
      this.applyFilterKorisnici = [];
      this.IndexStranice = 0;
      this.updatePageData();
    } else if (uloga !== '' && email === '') {
      this.applyFilterKorisnici = this.korisnici.filter((korisnik) => {
        return korisnik.UlogaKorisnikaNaziv.toLowerCase().includes(uloga);
      });
      this.IndexStranice = 0;
      this.ukupnoNatjecaja = this.applyFilterKorisnici.length;
      this.updatePageData(false, false, true);
    } else if (uloga === '' && email !== '') {
      this.applyFilterKorisnici = this.korisnici.filter((korisnik) => {
        return korisnik.Email.toLowerCase().includes(email);
      });
      this.IndexStranice = 0;
      this.ukupnoNatjecaja = this.applyFilterKorisnici.length;
      this.updatePageData(false, false, true);
    } else {
      this.applyFilterKorisnici = this.korisnici.filter((korisnik) => {
        return (
          korisnik.UlogaKorisnikaNaziv.toLowerCase().includes(uloga) &&
          korisnik.Email.toLowerCase().includes(email)
        );
      });
      this.IndexStranice = 0;
      this.ukupnoNatjecaja = this.applyFilterKorisnici.length;
      this.updatePageData(false, false, true);
    }
  }

  clearFilter(): void {
    this.ulogaFilter = '';
    this.emailFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.applyFilterKorisnici = [];
    this.dataSource = this.korisnici;
    this.ukupnoNatjecaja = this.dataSource.length;
    this.IndexStranice = 0;
    this.updatePageData();
  }

  compare(
    a: number | string | boolean,
    b: number | string | boolean,
    isAsc: boolean
  ): number {
    if (typeof a === 'boolean' && typeof b === 'boolean') {
      return (a === b ? 0 : a ? 1 : -1) * (isAsc ? 1 : -1);
    }

    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(
    sort = false,
    sortiraniKorisnici = false,
    applyFilter = false
  ): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (applyFilter) {
      if (this.IndexStranice >= this.ukupnoNatjecaja / this.stranicenje - 1)
        endIndex = this.ukupnoNatjecaja;
      this.dataSource = this.applyFilterKorisnici.slice(startIndex, endIndex);
      return;
    }

    if (sort) {
      if (this.IndexStranice >= this.ukupnoNatjecaja / this.stranicenje - 1)
        endIndex = this.ukupnoNatjecaja;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniKorisnici) {
      if (this.IndexStranice >= this.ukupnoNatjecaja / this.stranicenje - 1)
        endIndex = this.ukupnoNatjecaja;
      this.dataSource = this.sortiraniKorisnici.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.korisnici.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoNatjecaja / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniKorisnici =
        this.sortColumn !== '' && this.sortOrder !== '';
      const applyFilter = this.emailFilter !== '' || this.ulogaFilter !== '';
      this.updatePageData(false, sortiraniKorisnici, applyFilter);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniKorisnici =
        this.sortColumn !== '' && this.sortOrder !== '';
      const applyFilter = this.emailFilter !== '' || this.ulogaFilter !== '';
      this.updatePageData(false, sortiraniKorisnici, applyFilter);
    }
  }

  onDetail(row: any): void {
    const dialogRef = this.dialog.open(KorisniciDialogComponent, {
      width: '400px',
      data: row,
    });

    dialogRef.afterClosed().subscribe((result) => {
      this.IndexStranice = 0;
      this.getKorisnici();
    });
  }
}
