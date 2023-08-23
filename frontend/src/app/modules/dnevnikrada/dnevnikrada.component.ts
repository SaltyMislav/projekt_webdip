import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { DnevnikRada } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from 'src/app/shared/services/class/konfiguracijaclass.service';
import { DnevnikradaService } from 'src/app/shared/services/dnevnikrada.service';

@Component({
  selector: 'app-dnevnikrada',
  templateUrl: './dnevnikrada.component.html',
  styleUrls: ['./dnevnikrada.component.css'],
})
export class DnevnikradaComponent implements OnInit {
  vrstePromjene: any = [
    { ID: 0, Naziv: 'Ništa' },
    { ID: 1, Naziv: 'Dodavanje' },
    { ID: 2, Naziv: 'Update' },
    { ID: 3, Naziv: 'Odabir' },
    { ID: 4, Naziv: 'Brisanje' },
    { ID: 5, Naziv: 'Post dohvaćanje' },
    { ID: 6, Naziv: 'Get dohvaćanje' },
    { ID: 7, Naziv: 'Provjera' },
    { ID: 8, Naziv: 'Greška' },
    { ID: 9, Naziv: 'Uspjeh' },
  ];

  dataSource: DnevnikRada[] = [];
  dnevnikRada: DnevnikRada[] = [];
  sortiraniDnevnikRada: DnevnikRada[] = [];

  displayedColumns: string[] = [
    'DatumPromjene',
    'Detail',
    'Naziv',
  ];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: '' | 'asc' | 'desc' = '';

  vrstaPromjeneIDFilter: number = 0;
  vrijemePocetkaFilter!: string;

  counter = 0;

  constructor(
    private dnevnikRadaService: DnevnikradaService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    const data = {
      vrstaPromjeneID: this.vrstaPromjeneIDFilter
        ? this.vrstaPromjeneIDFilter
        : 0,
      datumPromjene: this.vrijemePocetkaFilter
        ? this.vrijemePocetkaFilter
        : '',
    };

    this.dohvatiDnevnikRada(data);
  }

  dohvatiDnevnikRada(data: any) {
    this.dnevnikRadaService.getDnevnikRada(data).subscribe({
      next: (res: DnevnikRada[]) => {
        this.dataSource = this.dnevnikRada = res;
        this.ukupnoZapisa = res.length;
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

    const sortedData = this.dnevnikRada.slice();
    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a: any, b: any) => {
        const a1 = a[column];
        const b1 = b[column];
        return (a1 < b1 ? -1 : 1) * (this.sortOrder === 'asc' ? 1 : -1);
      });
    }

    this.dataSource = this.sortiraniDnevnikRada = sortedData;
    this.updatePageData(true);
  }

  applyFilter(): void {
    const data = {
      vrstaPromjeneID: this.vrstaPromjeneIDFilter,
      datumPromjene: this.vrijemePocetkaFilter
        ? this.vrijemePocetkaFilter
        : '',
    };

    this.IndexStranice = 0;
    this.dohvatiDnevnikRada(data);
  }

  clearFilter(): void {
    this.vrstaPromjeneIDFilter = 0;
    this.vrijemePocetkaFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';

    this.IndexStranice = 0;

    const data = {
      vrstaPromjeneID: this.vrstaPromjeneIDFilter,
      datumPromjene: this.vrijemePocetkaFilter,
    };

    this.dohvatiDnevnikRada(data);
  }

  updatePageData(sort = false, sortiraniDnevnik = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniDnevnik) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.sortiraniDnevnikRada.slice(startIndex, endIndex);
      return;
    }

    this.dataSource = this.dnevnikRada.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniDnevnikRada =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniDnevnikRada);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniDnevnikRada =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniDnevnikRada);
    }
  }
}
