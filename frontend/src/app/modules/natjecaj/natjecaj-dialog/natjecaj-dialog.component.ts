import { ChangeDetectorRef, Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import {
  MAT_DIALOG_DATA,
  MatDialog,
  MatDialogRef,
} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import {
  Poduzece,
  PrijavaKorisnika,
  StatusNatjecaja,
} from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from 'src/app/shared/services/class/konfiguracijaclass.service';
import { NatjecajService } from 'src/app/shared/services/natjecaj.service';
import { PrijavaNatjecajComponent } from '../prijava-natjecaj/prijava-natjecaj.component';

@Component({
  selector: 'app-natjecaj-dialog',
  templateUrl: './natjecaj-dialog.component.html',
  styleUrls: ['./natjecaj-dialog.component.css'],
})
export class NatjecajDialogComponent implements OnInit {
  form!: FormGroup;
  poduzeca: Poduzece[] = [];
  statusNatjecaja: StatusNatjecaja[] = [];
  selectedPoduzece!: number;
  selectedStatus!: number;

  displayedColumns = ['Ime', 'Prezime', 'Slika'];
  dataSource: PrijavaKorisnika[] = [];
  prijvaljeniKorisnici: PrijavaKorisnika[] = [];
  sortiraniPrijavljeniKorisnici: PrijavaKorisnika[] = [];

  stranicenje!: number;
  ukupnoZapisa!: number;
  IndexStranice = 0;

  sortColumn = '';
  sortOrder: 'asc' | 'desc' | '' = '';

  imeKorisnikaFilter = '';
  prezimeKorisnikaFilter = '';

  counter = 0;

  constructor(
    private fb: FormBuilder,
    private natjecajService: NatjecajService,
    protected authService: AuthenticationService,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef,
    private snackBar: MatSnackBar,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<NatjecajDialogComponent>,
    @Inject(MAT_DIALOG_DATA) protected data: any
  ) {
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    const postData = {
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.natjecajService
      .getPoduzece(postData)
      .subscribe((result: Poduzece[]) => {
        this.poduzeca = result;
      });

    this.natjecajService
      .getStatusNatjecaja()
      .subscribe((result: StatusNatjecaja[]) => {
        this.statusNatjecaja = result;
      });

    if (this.authService.isUser()) {
      this.form = this.fb.group({
        ID: { value: this.data?.ID, disabled: true },
        Naziv: [
          { value: this.data?.Naziv, disabled: true },
          Validators.required,
        ],
        Opis: [{ value: this.data?.Opis, disabled: true }, Validators.required],
        VrijemePocetka: [
          { value: this.data?.VrijemePocetka, disabled: true },
          Validators.required,
        ],
        VrijemeKraja: [
          { value: this.data?.VrijemeKraja, disabled: true },
          Validators.required,
        ],
        PoduzeceID: [
          { value: this.data?.PoduzeceID, disabled: true },
          Validators.required,
        ],
        StatusNatjecajaID: [
          { value: this.data?.StatusNatjecajaID, disabled: true },
          Validators.required,
        ],
      });
    } else {
      this.form = this.fb.group({
        ID: [this.data?.ID],
        Naziv: [this.data?.Naziv, Validators.required],
        Opis: [this.data?.Opis, Validators.required],
        VrijemePocetka: [this.data?.VrijemePocetka, Validators.required],
        VrijemeKraja: [
          { value: this.data?.VrijemeKraja, disabled: true },
          Validators.required,
        ],
        PoduzeceID: [this.data?.PoduzeceID, Validators.required],
        StatusNatjecajaID: [this.data?.StatusNatjecajaID, Validators.required],
      });
    }

    this.selectedStatus = this.data?.StatusNatjecajaID;
    this.selectedPoduzece = this.data?.PoduzeceID;

    this.dataSource = this.data?.Prijavljeni ? this.data?.Prijavljeni : [];
    this.ukupnoZapisa = this.dataSource.length;
    this.updatePageData(true);
  }

  setDatumZavrsetka(event: FocusEvent): void {
    const startValue = new Date((event.target as HTMLInputElement).value);

    if (startValue) {
      startValue.setDate(startValue.getDate() + 10);
      startValue.setHours(startValue.getHours() + 2);
      const formatedDate = startValue.toISOString().split('.')[0];
      this.form.controls['VrijemeKraja'].setValue(formatedDate);
    }
  }

  onSave(): void {
    if (this.form.valid) {
      this.natjecajService.saveNatjecaj(this.form.getRawValue()).subscribe({
        next: (result: any) => {
          this.snackBar.open(result, 'U redu', {
            panelClass: ['green-snackbar'],
          });
        },
        error: (err: any) => {
          this.snackBar.open(err.error.error.errstr, 'U redu', {
            panelClass: ['red-snackbar'],
          });
        },
      });
    }
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

    const sortedData = this.dataSource.slice();
    if (this.counter % 3 === 0) {
      this.counter = 0;
    } else {
      sortedData.sort((a, b) => {
        const isAsc = this.sortOrder === 'asc';
        switch (column) {
          case 'Ime':
            return this.compare(a.Ime, b.Ime, isAsc);
          case 'Prezime':
            return this.compare(a.Prezime, b.Prezime, isAsc);
          default:
            return 0;
        }
      });
    }

    this.dataSource = this.sortiraniPrijavljeniKorisnici = sortedData;
    this.updatePageData(true);
  }

  compare(a: number | string, b: number | string, isAsc: boolean): number {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
  }

  applyFilter(): void {
    const dataFilter = {
      Ime: this.imeKorisnikaFilter
        ? this.imeKorisnikaFilter.trim().toLowerCase()
        : null,
      Prezime: this.prezimeKorisnikaFilter
        ? this.prezimeKorisnikaFilter.trim().toLowerCase()
        : null,
      NatjecajID: this.data?.ID,
    };
    this.IndexStranice = 0;
    this.getPrijavljeniKorisnici(dataFilter);
  }

  clearFilter(): void {
    this.imeKorisnikaFilter = '';
    this.prezimeKorisnikaFilter = '';
    this.sortColumn = '';
    this.sortOrder = '';
    this.IndexStranice = 0;

    const dataFilter = {
      Ime: this.imeKorisnikaFilter,
      Prezime: this.prezimeKorisnikaFilter,
      NatjecajID: this.data?.ID,
    };
    this.getPrijavljeniKorisnici(dataFilter);
  }

  getPrijavljeniKorisnici(dataFilter: any): void {
    this.natjecajService.getPrijavljeniKorisnici(dataFilter).subscribe({
      next: (result: PrijavaKorisnika[]) => {
        this.dataSource = this.prijvaljeniKorisnici = result;
        this.ukupnoZapisa = result.length;
        this.stranicenje = this.konfiguracijaClass.stranicenje;
        this.updatePageData();
        this.cdref.detectChanges();
      },
      error: (err: any) => {
        console.log(err);
      },
    });
  }

  updatePageData(sort = false, sortiraniKorisnici = false): void {
    const startIndex = this.IndexStranice * this.stranicenje;
    let endIndex = startIndex + this.stranicenje;

    if (sort) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.dataSource.slice(startIndex, endIndex);
      return;
    }

    if (sortiraniKorisnici) {
      if (this.IndexStranice >= this.ukupnoZapisa / this.stranicenje - 1)
        endIndex = this.ukupnoZapisa;
      this.dataSource = this.sortiraniPrijavljeniKorisnici.slice(
        startIndex,
        endIndex
      );
      return;
    }

    this.dataSource = this.prijvaljeniKorisnici.slice(startIndex, endIndex);
  }

  nextPage(): void {
    if (this.IndexStranice < this.ukupnoZapisa / this.stranicenje - 1) {
      this.IndexStranice++;
      const sortiraniKorisnici =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniKorisnici);
    }
  }

  previousPage(): void {
    if (this.IndexStranice > 0) {
      this.IndexStranice--;
      const sortiraniKorisnici =
        this.sortColumn != '' && this.sortOrder != '';
      this.updatePageData(false, sortiraniKorisnici);
    }
  }

  onAdd(row?: any) {
    let dataSend = {};
    if (row === undefined) {
      dataSend = {
        NatjecajID: this.data?.ID,
        RowID: row?.ID,
      };

      if (!this.authService.isModerator()) {
        dataSend = {
          ...dataSend,
          KorisnikID: this.authService.getUser().user_ID,
        };
      }
    }
    console.log(row);
    const dialogPrijavljeni = this.dialog.open(PrijavaNatjecajComponent, {
      width: '20%',
      data: row ? row : dataSend,
    });

    dialogPrijavljeni.afterClosed().subscribe((result) => {
      const dataFilter = {
        Ime: '',
        Prezime: '',
        NatjecajID: this.data?.ID,
      };
      this.getPrijavljeniKorisnici(dataFilter);
    });
  }
}
