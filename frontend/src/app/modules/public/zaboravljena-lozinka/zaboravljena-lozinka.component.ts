import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PrijavaService } from '../../services/prijava.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-zaboravljena-lozinka',
  templateUrl: './zaboravljena-lozinka.component.html',
  styleUrls: ['./zaboravljena-lozinka.component.css'],
})
export class ZaboravljenaLozinkaComponent implements OnInit {
  formZaboravljenaLozinka!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private prijavaService: PrijavaService,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.formZaboravljenaLozinka = this.fb.group({
      korisnickoIme: ['', Validators.required],
    });
  }

  onSubmit(): void {
    this.formZaboravljenaLozinka.markAllAsTouched();

    if (this.formZaboravljenaLozinka.valid) {
      this.prijavaService
        .zaboravljenaLozinka(this.formZaboravljenaLozinka.value)
        .subscribe({
          next: (result: any) => {
            this.snackBar.open(
              'Uspješno poslana nova lozinka na mail',
              'U redu',
              {
                panelClass: 'green-snackbar',
              }
            );
            setTimeout(() => {
              location.href = environment.loginPage;
            }, 2000);
          },
          error: (error) => {
            this.snackBar.open(error.error.error.errstr, 'U redu', {
              panelClass: 'red-snackbar',
            });
          },
        });
    }
  }
}
