import {
  AfterViewInit,
  ChangeDetectorRef,
  Component,
  OnDestroy,
  OnInit,
  ViewChild,
} from '@angular/core';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Subject, Subscription, takeUntil } from 'rxjs';
import { Korisnik } from 'src/app/interfaces/interfaces';
import { KorisniciService } from '../services/korisnici.service';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { MatDialog } from '@angular/material/dialog';
import { KorisniciDialogComponent } from './korisnici-dialog/korisnici-dialog.component';
import { MatPaginator } from '@angular/material/paginator';

@Component({
  selector: 'app-korisnici',
  templateUrl: './korisnici.component.html',
  styleUrls: ['./korisnici.component.css'],
})
export class KorisniciComponent implements OnInit, OnDestroy, AfterViewInit {
  dataSource!: MatTableDataSource<Korisnik>;

  korisnik: Korisnik[] = [];

  displayedColumns: string[] = [
    'ID',
    'Ime',
    'Prezime',
    'Email',
    'UlogaKorisnikaNaziv',
    'Active',
    'Blokiran',
  ];

  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;

  pomak!: number;
  stranicenje!: number;

  konfiguracijaDataSubscription!: Subscription;
  notifier = new Subject<any>();
  counter = 0;

  constructor(
    private korisniciService: KorisniciService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass,
    public dialog: MatDialog
  ) {
    this.konfiguracijaDataSubscription =
      this.konfiguracijaClass.konfiguracijaDataSubject
        .pipe(takeUntil(this.notifier))
        .subscribe((data) => {
          this.pomak = data.pomak;
          this.stranicenje = data.stranicenje;

          this.cdref.detectChanges();
        });
  }

  ngOnInit(): void {
    this.konfiguracijaClass.getData();
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

  ngOnDestroy(): void {
    this.notifier.next(null);
    this.notifier.complete();
    this.konfiguracijaDataSubscription.unsubscribe();
  }
}
