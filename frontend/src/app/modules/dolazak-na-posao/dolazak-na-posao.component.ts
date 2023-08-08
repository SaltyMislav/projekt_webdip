import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Subject, Subscription, takeUntil } from 'rxjs';
import { AuthenticationService } from 'src/app/auth/authentication.service';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { DolazakNaPosaoService } from '../services/dolazaknaposao.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-dolazak-na-posao',
  templateUrl: './dolazak-na-posao.component.html',
  styleUrls: ['./dolazak-na-posao.component.css'],
})
export class DolazakNaPosaoComponent implements OnInit {
  form!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private konfiguracijaService: KonfiguracijaClass,
    private authService: AuthenticationService,
    private dolazakNaPosaoService: DolazakNaPosaoService,
    private snackBar: MatSnackBar
  ) {
  }

  ngOnInit(): void {
    const user = this.authService.getUser();

    this.form = this.fb.group({
      DatumVrijemeDolaska: ['', Validators.required],
      KorisnikID: [user.user_ID, Validators.required],
    });
  }

  dolazakNaPosao() {
    if (this.form.valid) {
      this.dolazakNaPosaoService.dolazakNaPosao(this.form.value).subscribe({
        next: (result) => {
          this.snackBar.open('Dolazak na posao uspješno spremljen', 'Zatvori', {
            panelClass: ['green-snackbar'],
          });
        },
        error: (err) => {
          this.snackBar.open(err.error.error.errstr, 'Zatvori', {
            panelClass: ['red-snackbar'],
          });
        },
      });
    }
  }
}
