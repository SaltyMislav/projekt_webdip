import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Korisnik } from '../../interfaces/interfaces';
import { KonfiguracijaClass } from '../../shared/services/class/konfiguracijaclass.service';
import { KorisniciService } from '../../shared/services/korisnici.service';
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
  displayedColumns: string[] = [
    'ID',
    'Ime',
    'Prezime',
    'Email',
    'UlogaKorisnikaNaziv',
    'Active',
    'Blokiran',
  ];
  stranicenje!: number;
  ukupnoKorisnika = 0;
  IndexStranice = 0;

  ulogaFilter = '';
  emailFilter = '';

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';
  counter = 0;

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

  getKorisnici(data?: any) {
    this.korisniciService.getAllKorisnici(data).subscribe({
      next: (response) => {
        this.dataSource = this.korisnici = response;
        this.ukupnoKorisnika = response.length;
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
      this.counter++;
      this.sortOrder =
        this.sortOrder === 'asc'
          ? 'desc'
          : this.sortOrder === 'desc'
          ? ''
          : 'asc';
    } else {
      this.sortColumn = column;
      this.sortOrder = 'asc';
      this.counter++;
    }

    const sortedData = this.korisnici.slice();

    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a, b) => {
        const isAsc =
          this.sortOrder === 'asc'
            ? true
            : this.sortOrder === ''
            ? true
            : false;
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
          case 'Active':
            return this.compare(a.Active, b.Active, isAsc);
          case 'Blokiran':
            return this.compare(a.Blokiran, b.Blokiran, isAsc);
          default:
            return 0;
        }
      });
    }
    this.dataSource = this.sortiraniKorisnici = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const data = {
      UlogaKorisnikaNaziv: this.ulogaFilter.trim(),
      Email: this.emailFilter.trim(),
    };

    if (data.UlogaKorisnikaNaziv === '' && data.Email === '') {
      this.IndexStranice = 0;
      this.getKorisnici();
      return;
    }

    this.IndexStranice = 0;
    this.getKorisnici(data);
  }

  clearFilter(): void {
    this.ulogaFilter = '';
    this.emailFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
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

  updatePageData(sort = false, sortiraniKorisnici = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoKorisnika / this.stranicenje - 1)
        endIndex = this.ukupnoKorisnika;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniKorisnici) {
      if (this.IndexStranice >= this.ukupnoKorisnika / this.stranicenje - 1)
        endIndex = this.ukupnoKorisnika;
      this.dataSource = this.sortiraniKorisnici.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.korisnici.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoKorisnika / this.stranicenje - 1) {
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
