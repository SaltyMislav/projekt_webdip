import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import { KorisniciPrijava } from 'src/app/interfaces/interfaces';
import { KonfiguracijaClass } from 'src/app/shared/services/class/konfiguracijaclass.service';
import { KorisniciService } from 'src/app/shared/services/korisnici.service';
import { NatjecajService } from 'src/app/shared/services/natjecaj.service';

@Component({
  selector: 'app-prijava-natjecaj',
  templateUrl: './prijava-natjecaj.component.html',
  styleUrls: ['./prijava-natjecaj.component.css'],
})
export class PrijavaNatjecajComponent implements OnInit {
  form!: FormGroup;
  korisnici: KorisniciPrijava[] = [];
  selectedKorisnik!: number;

  base64Image!: string;
  maxSize!: number;

  constructor(
    private fb: FormBuilder,
    protected authService: AuthenticationService,
    private korisniciService: KorisniciService,
    private natjecajService: NatjecajService,
    private konfiguracijaClass: KonfiguracijaClass,
    private snackBar: MatSnackBar,
    public dialogPrijavljeni: MatDialogRef<PrijavaNatjecajComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {
    this.maxSize = this.konfiguracijaClass.imageSize * 1024;
  }

  ngOnInit(): void {
    if (this.authService.isModerator()){
      this.korisniciService.getKorisniciZaPrijavu().subscribe((res: any) => {
        this.korisnici = res;
      });
    } else {
      this.selectedKorisnik = this.authService.getUser().user_ID;
      this.form.controls['KorisnikID'].disable();
    }

    this.form = this.fb.group({
      ID: [this.data?.ID],
      KorisnikID: [this.data?.KorisnikID, Validators.required],
      NatjecajID: [this.data?.NatjecajID, Validators.required],
    });

    this.base64Image = this.data?.Slika;
  }

  onFileSelected(event: any) {
    const file: File = event.target.files[0] ?? '';

    if (file) {
      const reader = new FileReader();
      console.log(file.size, this.maxSize);
      if (file.size > this.maxSize) {
        this.snackBar.open(
          'Slika je prevelika. Maksimalna veličina slike je ' +
            this.maxSize +
            ' KB.',
          'U redu',
          {
            duration: 5000,
            panelClass: ['red-snackbar']
          }
        );
        return;
      }
      reader.onload = (e: ProgressEvent<FileReader>) => {
        const arraybuffer = e.target?.result as string;
        this.base64Image = arraybuffer.split(',')[1];
      };
      reader.readAsDataURL(file);
    } else {
      this.base64Image = '';
    }
  }

  onYesClick(){
    const formData = this.form.getRawValue();
    formData.Slika = this.base64Image;
    if (this.form.valid && (this.base64Image != '' || this.base64Image != undefined)) {
      this.natjecajService.prijavaKorisnika(formData).subscribe({
        next: (result: any) => {
          this.snackBar.open(result, 'U redu', {
            panelClass: ['green-snackbar'],
          });
          this.dialogPrijavljeni.close();
        },
        error: (err: any) => {
          this.snackBar.open(err.error.error.errstr, 'U redu', {
            panelClass: ['red-snackbar'],
          });
        }
      });
    }
  }

  onRemoveClick(){
    console.log(this.data?.ID);
    if (this.data?.ID != undefined){
      this.natjecajService.removePrijavljeniKorisnik(this.data?.ID).subscribe({
        next: (result: any) => {
          this.snackBar.open(result, 'U redu', {
            panelClass: ['green-snackbar'],
          });
          this.dialogPrijavljeni.close();
        },
        error: (err: any) => {
          this.snackBar.open(err.error.error.errstr, 'U redu', {
            panelClass: ['red-snackbar'],
          });
        }
      });
    }
  }

  onNoClick(){
    this.dialogPrijavljeni.close();
  }
}
