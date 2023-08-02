import { ChangeDetectorRef, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Natjecaj } from 'src/app/interfaces/interfaces';
import { NatjecajService } from '../services/natjecaj.service';
import { Subject, Subscription, takeUntil } from 'rxjs';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';

@Component({
  selector: 'app-natjecaj-public',
  templateUrl: './natjecaj-public.component.html',
  styleUrls: ['./natjecaj-public.component.css'],
})
export class NatjecajPublicComponent implements OnInit, OnDestroy {
  dataSource!: MatTableDataSource<Natjecaj>;
  natjecaji: Natjecaj[] = [];
  displayedColumns: string[] = [
    'ID',
    'Naziv',
    'VrijemePocetka',
    'VrijemeKraja',
    'Opis',
    'StatusNatjecajaNaziv',
    'PoduzeceNaziv',
  ];

  @ViewChild(MatSort) sort!: MatSort;

  pomak!: number;
  stranicenje!: number;
  ukupnoNatjecaja = 0;

  subscription!: Subscription;
  notifier = new Subject<any>();

  constructor(
    private natjecajService: NatjecajService,
    private cdref: ChangeDetectorRef,
    private konfiguracijaClass: KonfiguracijaClass
  ) {
  }

  ngOnInit(): void {
    this.getNatjecaj();
    
    this.subscription = this.konfiguracijaClass.konfiguracijaDataSubject
      .pipe(takeUntil(this.notifier))
      .subscribe((data) => {
        this.pomak = data.pomak;
        this.stranicenje = data.stranicenje;

        this.cdref.detectChanges();
      });
  }

  getNatjecaj(): void {
    this.natjecajService.getAllNatjecaj().subscribe({
      next: (data: Natjecaj[]) => {
        data.forEach((element) => {
          this.ukupnoNatjecaja++;
        });
        this.natjecaji = data;
        this.dataSource = new MatTableDataSource(this.natjecaji);
        this.dataSource.sort = this.sort;
        this.cdref.detectChanges();
      },
      error: (error) => console.log(error),
    });
  }

  ngOnDestroy(): void {
    this.notifier.next(null);
    this.notifier.complete();
    this.subscription?.unsubscribe();
  }
}
