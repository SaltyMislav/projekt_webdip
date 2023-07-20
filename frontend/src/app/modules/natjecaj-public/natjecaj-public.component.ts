import { ChangeDetectorRef, Component, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Natjecaj } from 'src/app/interfaces/interfaces';
import { NatjecajService } from '../services/natjecaj.service';

@Component({
  selector: 'app-natjecaj-public',
  templateUrl: './natjecaj-public.component.html',
  styleUrls: ['./natjecaj-public.component.css']
})
export class NatjecajPublicComponent {
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

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;

  constructor(
    private natjecajService: NatjecajService,
    private cdref: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.getNatjecaj();
  }

  getNatjecaj(): void {
    this.natjecajService.getAllNatjecaj().subscribe(
      (data: Natjecaj[]) => {
        console.log(data);
        this.natjecaji = data;
        this.dataSource = new MatTableDataSource(this.natjecaji);
        this.dataSource.sort = this.sort;
        this.dataSource.paginator = this.paginator;
        this.cdref.detectChanges();
      },
      (error) => console.log(error)
    );
  }
}
