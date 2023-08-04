import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { KorisniciService } from '../../services/korisnici.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { UlogaKorisnika } from 'src/app/interfaces/interfaces';

@Component({
  selector: 'app-korisnici-dialog',
  templateUrl: './korisnici-dialog.component.html',
  styleUrls: ['./korisnici-dialog.component.css']
})
export class KorisniciDialogComponent implements OnInit {
  form!: FormGroup;
  uloge: UlogaKorisnika[] = [
    { ID: '1', Naziv: 'Korisnik' },
    { ID: '2', Naziv: 'Moderator'},
    { ID: '3', Naziv: 'Administrator' },
  ];

  selected = this.data.UlogaKorisnikaID;

  constructor(
    private fb: FormBuilder,
    private korisnikService: KorisniciService,
    private snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<KorisniciDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) { }

  ngOnInit(): void {
    this.form = this.fb.group({
      ID: [this.data.ID],
      Ime: [{value: this.data.Ime, disabled: true}],
      Prezime: [{value: this.data.Prezime, disabled: true}],
      KorisnickoIme: [{value: this.data.KorisnickoIme, disabled: true}],
      Email: [{value: this.data.Email, disabled: true}],
      NeuspjesnePrijave: [this.data.NeuspjesnePrijave],
      UlogaKorisnikaID: [this.data.UlogaKorisnikaID],
      Active: [+this.data.Active],
      Blokiran: [+this.data.Blokiran],
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    this.korisnikService.onSaveKorisnik(this.form.getRawValue()).subscribe({
      next: (result) => {
        this.snackBar.open('Korisnik je uspješno spremljen', 'Close', {
          duration: 2000,
        });
        this.dialogRef.close();
      },
      error: (err) => {
        this.snackBar.open(err.message, 'Close', {
          duration: 2000,
        });
      }
    });
  }
}
