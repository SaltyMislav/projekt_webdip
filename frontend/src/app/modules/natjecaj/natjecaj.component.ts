import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Natjecaj } from '../../interfaces/interfaces';
import { KonfiguracijaClass } from '../../shared/services/class/konfiguracijaclass.service';
import { NatjecajService } from '../../shared/services/natjecaj.service';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import { NatjecajDialogComponent } from './natjecaj-dialog/natjecaj-dialog.component';

@Component({
  selector: 'app-natjecaj',
  templateUrl: './natjecaj.component.html',
  styleUrls: ['./natjecaj.component.css'],
})
export class NatjecajComponent implements OnInit {
  dataSource: Natjecaj[] = [];
  natjecaji: Natjecaj[] = [];
  sortiraniNatjecaji: Natjecaj[] = [];
  displayedColumns: string[] = [
    'Naziv',
    'VrijemePocetka',
    'VrijemeKraja',
    'Opis',
    'StatusNatjecajaNaziv',
    'PoduzeceNaziv',
  ];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  nazivNatjecajaFilter = '';
  vrijemePocetkaFilter = '';

  counter = 0;

  constructor(
    private natjecajService: NatjecajService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass,
    protected authService: AuthenticationService,
    private dialog: MatDialog
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    const data = {
      NazivNatjecaja: this.nazivNatjecajaFilter
        ? this.nazivNatjecajaFilter.trim().toLowerCase()
        : '',
      VrijemePocetka: this.vrijemePocetkaFilter
        ? this.vrijemePocetkaFilter
        : '',
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };
    this.getNatjecaji(data);
  }

  getNatjecaji(data?: any): void {
    this.natjecajService.getModeratoriNatjecaj(data).subscribe({
      next: (result: Natjecaj[]) => {
        this.dataSource = this.natjecaji = result;
        this.ukupnoZapisa = result.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (err: any) => {
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

    const sortedData = this.natjecaji.slice();
    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a, b) => {
        const isAsc = this.sortOrder === 'asc';
        switch (column) {
          case 'Naziv':
            return this.compare(a.Naziv, b.Naziv, isAsc);
          case 'VrijemePocetka':
            return this.compare(
              new Date(a.VrijemePocetka),
              new Date(b.VrijemePocetka),
              isAsc
            );
          case 'VrijemeKraja':
            return this.compare(
              new Date(a.VrijemeKraja),
              new Date(b.VrijemeKraja),
              isAsc
            );
          case 'Opis':
            return this.compare(a.Opis, b.Opis, isAsc);
          case 'StatusNatjecajaNaziv':
            return this.compare(
              a.StatusNatjecajaNaziv,
              b.StatusNatjecajaNaziv,
              isAsc
            );
          case 'PoduzeceNaziv':
            return this.compare(a.PoduzeceNaziv, b.PoduzeceNaziv, isAsc);
          default:
            return 0;
        }
      });
    }
    this.dataSource = this.sortiraniNatjecaji = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const data = {
      NazivNatjecaja: this.nazivNatjecajaFilter
        ? this.nazivNatjecajaFilter.trim().toLowerCase()
        : '',
      VrijemePocetka: this.vrijemePocetkaFilter
        ? this.vrijemePocetkaFilter
        : '',
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.IndexStranice = 0;
    this.getNatjecaji(data);
  }

  clearFilter(): void {
    this.nazivNatjecajaFilter = '';
    this.vrijemePocetkaFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.IndexStranice = 0;

    const data = {
      NazivNatjecaja: this.nazivNatjecajaFilter,
      VrijemePocetka: this.vrijemePocetkaFilter,
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.getNatjecaji(data);
  }

  compare(
    a: number | string | Date | undefined,
    b: number | string | Date | undefined,
    isAsc: boolean
  ): number {
    if (a === undefined || b === undefined) return 0;
    if (a instanceof Date && b instanceof Date) {
      return (a.getTime() < b.getTime() ? -1 : 1) * (isAsc ? 1 : -1);
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiraniNatjecaji = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniNatjecaji) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.sortiraniNatjecaji.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.natjecaji.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniNatjecaji =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniNatjecaji);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniNatjecaji =
        this.sortColumn !== '' && this.sortOrder !== '';
      this.updatePageData(false, sortiraniNatjecaji);
    }
  }

  onDodaj(row?: any): void {
    const dialogRef = this.dialog.open(NatjecajDialogComponent, {
      width: '40%',
      data: row,
    });

    dialogRef.afterClosed().subscribe((result) => {
      const data = {
        NazivNatjecaja: '',
        VrijemePocetka: '',
        KorisnikID: this.authService.getUser().user_ID,
        UlogaID: this.authService.getUser().uloga,
      };
      this.getNatjecaji(data);
    });
  }
}
