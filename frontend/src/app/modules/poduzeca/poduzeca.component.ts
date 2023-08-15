import {
  ChangeDetectionStrategy,
  ChangeDetectorRef,
  Component,
  OnInit,
} from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Poduzece } from '../../interfaces/interfaces';
import { KonfiguracijaClass } from '../../shared/services/class/konfiguracijaclass.service';
import { PoduzeceService } from '../../shared/services/poduzece.service';
import { PoduzeceDialogComponent } from './poduzece-dialog/poduzece-dialog.component';

@Component({
  selector: 'app-poduzeca',
  templateUrl: './poduzeca.component.html',
  styleUrls: ['./poduzeca.component.css'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PoduzecaComponent implements OnInit {
  dataSource: Poduzece[] = [];
  poduzeca: Poduzece[] = [];
  sortiranaPoduzeca: Poduzece[] = [];
  displayedColumns: string[] = [
    'ID',
    'Naziv',
    'Opis',
    'RadnoVrijemeOd',
    'RadnoVrijemeDo',
  ];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  nazivFilter = '';
  opisFilter = '';

  counter = 0;

  constructor(
    private poduzeceService: PoduzeceService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass,
    public dialog: MatDialog
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    const data = {
      Naziv: this.nazivFilter ? this.nazivFilter.trim().toLowerCase() : '',
      Opis: this.opisFilter ? this.opisFilter.trim().toLowerCase() : '',
    };
    this.getPoduzece(data);
  }

  getPoduzece(data: any): void {
    this.poduzeceService.getAllPoduzece(data).subscribe({
      next: (data: Poduzece[]) => {
        this.dataSource = this.poduzeca = data;
        this.ukupnoZapisa = data.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (error) => console.log(error),
    });
  }

  applyFilter(): void {
    const data = {
      Naziv: this.nazivFilter ? this.nazivFilter.trim().toLowerCase() : '',
      Opis: this.opisFilter ? this.opisFilter.trim().toLowerCase() : '',
    };

    this.IndexStranice = 0;
    this.getPoduzece(data);
  }

  clearFilter(): void {
    this.nazivFilter = '';
    this.opisFilter = '';
    const data = {
      Naziv: this.nazivFilter,
      Opis: this.opisFilter,
    };
    this.getPoduzece(data);
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

    const sortedData = this.poduzeca.slice();

    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a: any, b: any) => {
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
          case 'Opis':
            return this.compare(a.Opis, b.Opis, isAsc);
          default:
            return 0;
        }
      });
    }
    this.dataSource = this.sortiranaPoduzeca = sortedData;
    this.updatePageData(true);
  }

  compare(a: string | number, b: string | number, isAsc: boolean): number {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  updatePageData(sort = false, sortiranaPoduzeca = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIdex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIdex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIdex);
      return;
    }

    if (sortiranaPoduzeca) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIdex = this.ukupnoZapisa;
      this.dataSource = this.sortiranaPoduzeca.slice(startIndex, endIdex);
      return;
    }

    this.dataSource = this.poduzeca.slice(startIndex, endIdex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiranaPoduzeca = this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiranaPoduzeca);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiranaPoduzeca = this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiranaPoduzeca);
    }
  }

  onDodaj(row?: any): void {
    const dialogRef = this.dialog.open(PoduzeceDialogComponent, {
      width: '500px',
      data: row,
    });

    dialogRef.afterClosed().subscribe(() => {
      const data = {
        Naziv: '',
        Opis: '',
      };
      this.getPoduzece(data);
    });
  }
}
