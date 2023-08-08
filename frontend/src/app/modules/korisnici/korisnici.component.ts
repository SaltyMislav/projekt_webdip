import {
  AfterViewInit,
  ChangeDetectorRef,
  Component,
  OnInit,
  ViewChild
} from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Korisnik } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { KorisniciService } from '../services/korisnici.service';
import { KorisniciDialogComponent } from './korisnici-dialog/korisnici-dialog.component';

@Component({
  selector: 'app-korisnici',
  templateUrl: './korisnici.component.html',
  styleUrls: ['./korisnici.component.css'],
})
export class KorisniciComponent implements OnInit, AfterViewInit {
  dataSource!: MatTableDataSource<Korisnik>;

  korisnik: Korisnik[] = [];

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

  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;

  stranicenje!: number;

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
  }

  ngAfterViewInit(): void {
    this.getKorisnici();
    this.sort.sortChange.subscribe(() => (this.paginator.pageIndex = 0));
  }

  getKorisnici() {
    this.korisniciService.getAllKorisnici().subscribe({
      next: (response) => {
        response.forEach((element: any) => {
          this.counter++;
        });
        this.korisnik = response;
        this.dataSource = new MatTableDataSource(this.korisnik);
        this.cdref.detectChanges();
        this.dataSource.sort = this.sort;
        this.dataSource.paginator = this.paginator;
      },
      error: (err) => {
        console.log(err);
      },
    });
  }

  onDetail(row: any): void {
    const dialogRef = this.dialog.open(KorisniciDialogComponent, {
      width: '400px',
      data: row,
    });

    dialogRef.afterClosed().subscribe((result) => {
      this.getKorisnici();
    });
  }
}
