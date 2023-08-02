import {
  ChangeDetectionStrategy,
  ChangeDetectorRef,
  Component,
  OnDestroy,
  OnInit,
  ViewChild,
} from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Subject, Subscription, takeUntil } from 'rxjs';
import { Poduzece } from '../../interfaces/interfaces';
import { PoduzeceDialogComponent } from '../poduzece-dialog/poduzece-dialog.component';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { PoduzeceService } from '../services/poduzece.service';

@Component({
  selector: 'app-poduzeca',
  templateUrl: './poduzeca.component.html',
  styleUrls: ['./poduzeca.component.css'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PoduzecaComponent implements OnInit, OnDestroy {
  dataSource!: MatTableDataSource<Poduzece>;
  poduzece: Poduzece[] = [];

  displayedColumns: string[] = [
    'ID',
    'Naziv',
    'Opis',
    'RadnoVrijemeOd',
    'RadnoVrijemeDo',
  ];

  @ViewChild(MatSort) sort!: MatSort;

  pomak!: number;
  stranicenje!: number;

  konfiguracijaDataSubscription!: Subscription;
  notifier = new Subject<any>();
  counter = 0;

  constructor(
    private poduzeceService: PoduzeceService,
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
    this.getPoduzece();
  }

  getPoduzece(): void {
    this.poduzeceService.getAllPoduzece().subscribe({
      next: (data: Poduzece[]) => {
        data.forEach((element) => {
          this.counter++;
        });
        this.poduzece = data;
        this.dataSource = new MatTableDataSource(this.poduzece);
        this.dataSource.sort = this.sort;
        this.cdref.detectChanges();
      },
      error: (error) => console.log(error),
    });
  }

  onDodaj(row?: any): void {
    const dialogRef = this.dialog.open(PoduzeceDialogComponent, {
      width: '500px',
      data: row,
    });

    dialogRef.afterClosed().subscribe(() => {
      this.getPoduzece();
    });
  }

  ngOnDestroy(): void {
    this.notifier.next(null);
    this.notifier.complete();
    this.konfiguracijaDataSubscription.unsubscribe();
  }
}
