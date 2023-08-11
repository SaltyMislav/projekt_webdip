import {
  AfterViewInit,
  ChangeDetectorRef,
  Component,
  OnInit,
} from '@angular/core';
import { Natjecaj } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { NatjecajService } from '../services/natjecaj.service';

@Component({
  selector: 'app-natjecaj-public',
  templateUrl: './natjecaj-public.component.html',
  styleUrls: ['./natjecaj-public.component.css'],
})
export class NatjecajPublicComponent implements OnInit {
  dataSource: Natjecaj[] = [];
  natjecaji: Natjecaj[] = [];
  sortiraniNatjecaji: Natjecaj[] = [];
  displayedColumns: string[] = [
    'ID',
    'Naziv',
    'VrijemePocetka',
    'VrijemeKraja',
    'Opis',
    'StatusNatjecajaNaziv',
    'PoduzeceNaziv',
  ];
  stranicenje!: number;
  ukupnoNatjecaja = 0;
  datumOd = '';
  datumDo = '';
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  counter = 0;

  constructor(
    private natjecajService: NatjecajService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass
  ) {}

  ngOnInit(): void {
    this.getNatjecaj();
  }

  getNatjecaj(data?: any): void {
    this.natjecajService.getAllNatjecaj(data).subscribe({
      next: (data: Natjecaj[]) => {
        this.dataSource = this.natjecaji = data;
        this.ukupnoNatjecaja = data.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (error) => console.log(error),
    });
  }

  mathceil(value: number, number: number): number {
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
        const isAsc =
          this.sortOrder === 'asc'
            ? true
            : this.sortOrder === ''
            ? true
            : false;
        switch (column) {
          case 'ID':
            return this.compare(a.ID, b.ID, isAsc);
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
          case 'StatusNatjecajaNaziv':
            return this.compare(a.VrstaStatusa, b.VrstaStatusa, isAsc);
          case 'PoduzeceNaziv':
            return this.compare(a.NazivPoduzeca, b.NazivPoduzeca, isAsc);
          default:
            return 0;
        }
      });
    }
    this.dataSource = this.sortiraniNatjecaji = sortedData;
    this.updatePageData(true);
  }

  compare(
    a: number | string | Date,
    b: number | string | Date,
    isAsc: boolean
  ): number {
    if (a instanceof Date && b instanceof Date) {
      return (a.getTime() < b.getTime() ? -1 : 1) * (isAsc ? 1 : -1);
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  applyFilter(): void {
    const fromDate = this.datumOd ? new Date(this.datumOd) : null;
    const toDate = this.datumDo ? new Date(this.datumDo) : null;

    toDate?.setHours(23);
    toDate?.setMinutes(59);
    toDate?.setSeconds(59);

    if (fromDate == null || toDate == null) {
      this.IndexStranice = 0;
      this.getNatjecaj();
    } else {
      this.IndexStranice = 0;
      const value = JSON.parse(
        '{ "fromDate": "' +
          this.toSqlDateString(fromDate) +
          '", "toDate": "' +
          this.toSqlDateString(toDate) +
          '" }'
      );
      this.getNatjecaj(value);
    }
  }

  toSqlDateString(date: Date): string {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const hours = (date.getHours() - 2).toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  }

  clearFilter(): void {
    this.datumOd = '';
    this.datumDo = '';
    this.IndexStranice = 0;
    this.getNatjecaj();
  }

  updatePageData(sort = false, sortiraniNatjecaji = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoNatjecaja / this.stranicenje - 1)
        endIndex = this.ukupnoNatjecaja;

      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniNatjecaji) {
      if (this.IndexStranice >= this.ukupnoNatjecaja / this.stranicenje - 1)
        endIndex = this.ukupnoNatjecaja;
      this.dataSource = this.sortiraniNatjecaji.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.natjecaji.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoNatjecaja / this.stranicenje - 1) {
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
}
