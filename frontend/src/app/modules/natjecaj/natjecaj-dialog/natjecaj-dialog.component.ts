import { ChangeDetectorRef, Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import { Poduzece, StatusNatjecaja } from 'src/app/interfaces/interfaces';
import { NatjecajService } from 'src/app/shared/services/natjecaj.service';

@Component({
  selector: 'app-natjecaj-dialog',
  templateUrl: './natjecaj-dialog.component.html',
  styleUrls: ['./natjecaj-dialog.component.css'],
})
export class NatjecajDialogComponent implements OnInit {
  dataSource: any[] = [];
  form!: FormGroup;
  poduzeca: Poduzece[] = [];
  statusNatjecaja: StatusNatjecaja[] = [];
  selectedPoduzece!: number;
  selectedStatus!: number;

  displayedColumns = ['Ime', 'Prezime', 'Slika'];

  constructor(
    private fb: FormBuilder,
    private natjecajService: NatjecajService,
    private authService: AuthenticationService,
    private cdref: ChangeDetectorRef,
    private snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<NatjecajDialogComponent>,
    @Inject(MAT_DIALOG_DATA) protected data: any
  ) {}

  ngOnInit(): void {
    const postData = {
      UlogaID: this.authService.getUser().uloga,
      KorisnikID: this.authService.getUser().user_ID,
    };

    this.natjecajService
      .getPoduzece(postData)
      .subscribe((poduzeca: Poduzece[]) => {
        this.poduzeca = poduzeca;
      });

    this.natjecajService
      .getStatusNatjecaja()
      .subscribe((statusNatjecaja: StatusNatjecaja[]) => {
        this.statusNatjecaja = statusNatjecaja;
      });

    this.form = this.fb.group({
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

    this.selectedStatus = this.data?.StatusNatjecajaID;
    this.selectedPoduzece = this.data?.PoduzeceID;

    this.dataSource = this.data?.Prijavljeni;
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
}
