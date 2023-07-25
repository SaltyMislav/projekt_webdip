import { ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { Poduzece } from '../../interfaces/interfaces';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { PoduzeceService } from '../services/poduzece.service';
import { MatDialog } from '@angular/material/dialog';
import { PoduzeceDialogComponent } from '../poduzece-dialog/poduzece-dialog.component';

@Component({
  selector: 'app-poduzeca',
  templateUrl: './poduzeca.component.html',
  styleUrls: ['./poduzeca.component.css']
})
export class PoduzecaComponent implements OnInit{
  dataSource!: MatTableDataSource<Poduzece>;
  poduzece: Poduzece[] = [];

  displayedColumns: string[] = ['ID', 'Naziv', 'Opis', 'RadnoVrijemeOd', 'RadnoVrijemeDo'];

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;

  constructor(
    private poduzeceService: PoduzeceService,
    private cdref: ChangeDetectorRef,
    public dialog: MatDialog
  ) { }

  ngOnInit(): void {
    this.getPoduzece();
  }

  getPoduzece(): void {
    this.poduzeceService.getAllPoduzece().subscribe({
      next: (data: Poduzece[]) => {
        this.poduzece = data;
        this.dataSource = new MatTableDataSource(this.poduzece);
        this.dataSource.sort = this.sort;
        this.dataSource.paginator = this.paginator;
        this.cdref.detectChanges();
      },
      error: (error) => console.log(error)
    });
  }

  onDodaj(row?: any): void {
    const dialogRef = this.dialog.open(PoduzeceDialogComponent, {
      width: '500px',
      data: row
    });

    dialogRef.afterClosed().subscribe(() => {
      this.getPoduzece();
    });
  }
}
