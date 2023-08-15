import { ChangeDetectorRef, Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import {
  OcijenaZaposlenika,
  Poduzece,
  ZaposleniciOdabir,
} from 'src/app/interfaces/interfaces';
import { KorisniciService } from 'src/app/shared/services/korisnici.service';
import { NatjecajService } from 'src/app/shared/services/natjecaj.service';
import { RadnizadatakService } from 'src/app/shared/services/radnizadatak.service';

@Component({
  selector: 'app-radni-zadatak-dialog',
  templateUrl: './radni-zadatak-dialog.component.html',
  styleUrls: ['./radni-zadatak-dialog.component.css'],
})
export class RadniZadatakDialogComponent implements OnInit {
  form!: FormGroup;
  zaposlenici: ZaposleniciOdabir[] = [];
  ocijene: OcijenaZaposlenika[] = [];

  selectedZaposlenik!: number;
  selectedPoduzece!: number;
  selectedOcijena!: number;

  constructor(
    private fb: FormBuilder,
    private radnizadatakService: RadnizadatakService,
    private natjecajService: NatjecajService,
    protected authService: AuthenticationService,
    private cdref: ChangeDetectorRef,
    private snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<RadniZadatakDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) { }

  ngOnInit(): void {
    this.radnizadatakService.getOcijena().subscribe((ocijene) => {
      this.ocijene = ocijene;
    });

    const zaposleniciData = {
      KorisnikID: this.authService.getUser().user_ID,
      UlogaID: this.authService.getUser().uloga,
    };

    this.radnizadatakService
      .getZaposlenici(zaposleniciData)
      .subscribe((res: any) => {
        this.zaposlenici = res;
      });

    if (this.authService.isModerator()) {
      this.form = this.fb.group({
        ID: [this.data?.ID],
        Naziv: [this.data?.Naziv, Validators.required],
        Opis: [this.data?.Opis, Validators.required],
        Datum: [this.data?.Datum, Validators.required],
        KorisnikID: [this.data?.KorisnikID, Validators.required],
        OcijenaZaposlenikaID: [
          {
            value: this.data?.OcijenaZaposlenikaID
              ? this.data.OcijenaZaposlenikaID
              : 1,
            disabled: this.data?.Odradeno ? false : true,
          },
          Validators.required,
        ],
        Odradeno: [
          { value: this.data?.Odradeno, disabled: true },
          Validators.required,
        ],
      });
    } else {
      this.form = this.fb.group({
        ID: [this.data?.ID],
        Naziv: [
          { value: this.data?.Naziv, disabled: true },
          Validators.required,
        ],
        Opis: [{value: '', disabled: this.data.Odradeno}, Validators.required],
        Datum: [
          { value: this.data?.Datum, disabled: true },
          Validators.required,
        ],
        KorisnikID: [
          { value: this.data?.KorisnikID, disabled: true },
          Validators.required,
        ],
        OcijenaZaposlenikaID: [
          {
            value: this.data?.OcijenaZaposlenikaID
              ? this.data.OcijenaZaposlenikaID
              : 1,
            disabled: true,
          },
          Validators.required,
        ],
        Odradeno: [
          { value: this.data?.Odradeno, disabled: true },
          Validators.required,
        ],
      });
    }

    this.selectedOcijena = this.data?.OcijenaZaposlenikaID
      ? this.data.OcijenaZaposlenikaID
      : 1;
    this.selectedZaposlenik = this.data?.UlogaKorisnikaID;
    this.selectedPoduzece = this.data?.PoduzeceID;

    if (this.data == undefined) {
      this.form.controls['Odradeno'].setValue(false);
    }

    if (this.data?.Odradeno == true) {
      this.form.controls['Opis'].setValue(this.data?.Opis);
    }

    this.cdref.detectChanges();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  onYesClick(): void {
    if (this.form.valid) {
      const formData = this.form.getRawValue();
      formData.PoduzeceID = this.selectedPoduzece;
      if (this.authService.isModerator()) {
        this.radnizadatakService.radniZadatakSave(formData).subscribe({
          next: (res) => {
            this.snackBar.open('Radni zadatak uspješno spremljen', 'U redu', {
              panelClass: ['green-snackbar'],
            });
            this.dialogRef.close();
          },
          error: (err) => {
            this.snackBar.open(err.error.error.errstr, 'U redu', {
              panelClass: ['red-snackbar'],
            });
          },
        });
      } else {
        this.radnizadatakService.radniZadatakKorisnikSave(formData).subscribe({
          next: (res) => {
            this.snackBar.open('Radni zadatak uspješno spremljen', 'U redu', {
              panelClass: ['green-snackbar'],
            });
            this.dialogRef.close();
          },
          error: (err) => {
            this.snackBar.open(err.error.error.errstr, 'U redu', {
              panelClass: ['red-snackbar'],
            });
          },
        });
      }
    }
  }

  onKorisnikChange(event: any) {
    this.zaposlenici.findIndex((z) => {
      if (z.ID == event.value) {
        this.selectedPoduzece = z.PoduzeceID;
      }
    });
  }
}
