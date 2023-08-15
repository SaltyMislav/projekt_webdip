import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import { RadniZadatak } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from 'src/app/shared/services/class/konfiguracijaclass.service';
import { RadnizadatakService } from 'src/app/shared/services/radnizadatak.service';
import { RadniZadatakDialogComponent } from './radni-zadatak-dialog/radni-zadatak-dialog.component';

@Component({
  selector: 'app-radni-zadatak',
  templateUrl: './radni-zadatak.component.html',
  styleUrls: ['./radni-zadatak.component.css'],
})
export class RadniZadatakComponent implements OnInit {
  dataSource: RadniZadatak[] = [];
  radniZadaci: RadniZadatak[] = [];
  sortiraniRadniZadaci: RadniZadatak[] = [];

  displayedColumns: string[] = [
    'Naziv',
    'Datum',
    'ImePrezime',
    'Odradeno',
    'Ocijena',
    'PoduzeceNaziv',
  ];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  nazivZadatkaFilter = '';
  opisZadatkaFilter = '';

  counter = 0;

  constructor(
    private radnizadatakService: RadnizadatakService,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef,
    protected authService: AuthenticationService,
    public dialog: MatDialog
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    const data = {
      Naziv: this.nazivZadatkaFilter
        ? this.nazivZadatkaFilter.trim().toLowerCase()
        : '',
      Opis: this.opisZadatkaFilter
        ? this.opisZadatkaFilter.trim().toLowerCase()
        : '',
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.getRadniZadaci(data);
  }

  getRadniZadaci(data: any): void {
    this.radnizadatakService.getAll(data).subscribe({
      next: (result) => {
        this.dataSource = this.radniZadaci = result;
        this.ukupnoZapisa = result.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (err) => console.log(err),
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

    const sortedData = this.radniZadaci.slice();
    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a, b) => {
        const isAsc = this.sortOrder === 'asc';
        switch (column) {
          case 'Naziv':
            return this.compare(a.Naziv, b.Naziv, isAsc);
          case 'Datum':
            return this.compare(new Date(a.Datum), new Date(b.Datum), isAsc);
          case 'ImePrezime':
            return this.compare(a.ImePrezime, b.ImePrezime, isAsc);
          case 'Odradeno':
            return this.compare(a.Odradeno, b.Odradeno, isAsc);
          case 'Ocijena':
            return this.compare(a.Ocijena, b.Ocijena, isAsc);
          case 'PoduzeceNaziv':
            return this.compare(a.PoduzeceNaziv, b.PoduzeceNaziv, isAsc);
          default:
            return 0;
        }
      });
    }
    this.dataSource = this.sortiraniRadniZadaci = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const data = {
      Naziv: this.nazivZadatkaFilter
        ? this.nazivZadatkaFilter.trim().toLowerCase()
        : '',
      Opis: this.opisZadatkaFilter
        ? this.opisZadatkaFilter.trim().toLowerCase()
        : '',
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.IndexStranice = 0;
    this.getRadniZadaci(data);
  }

  clearFilters(): void {
    this.nazivZadatkaFilter = '';
    this.opisZadatkaFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.IndexStranice = 0;

    const data = {
      Naziv: this.nazivZadatkaFilter,
      Opis: this.opisZadatkaFilter,
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.getRadniZadaci(data);
  }

  compare(
    a: number | string | Date | boolean,
    b: number | string | Date | boolean,
    isAsc: boolean
  ): number {
    if (a instanceof Date && b instanceof Date) {
      return (a.getTime() < b.getTime() ? -1 : 1) * (isAsc ? 1 : -1);
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiraniRadniZadaci = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniRadniZadaci) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.sortiraniRadniZadaci.slice(startIndex, endIndex);
    }

    this.dataSource = this.radniZadaci.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniRadniZadaci =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniRadniZadaci);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniRadniZadaci =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniRadniZadaci);
    }
  }

  onDodaj(row?: any): void {
    const dialogRef = this.dialog.open(RadniZadatakDialogComponent, {
      width: '60%',
      data: row,
    });

    dialogRef.afterClosed().subscribe((result) => {
      const data = {
        Naziv: '',
        Opis: '',
        UlogaID: this.authService.getUser().uloga,
        KorisnikID: this.authService.getUser().user_ID,
      };
      this.getRadniZadaci(data);
    });
  }
}
