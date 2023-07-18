import {
  ChangeDetectorRef,
  Component,
  OnInit,
  ViewChild
} from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Natjecaj } from '../../interfaces/interfaces';
import { NatjecajService } from '../services/natjecaj.service';

@Component({
  selector: 'app-app-homepage',
  templateUrl: './app-homepage.component.html',
  styleUrls: ['./app-homepage.component.css'],
})
export class AppHomepageComponent implements OnInit {
  dataSource!: MatTableDataSource<Natjecaj>;
  natjecaji: Natjecaj[] = [];
  displayedColumns: string[] = [
    'ID',
    'Naziv',
    'VrijemeKraja',
    'VrijemePocetka',
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
