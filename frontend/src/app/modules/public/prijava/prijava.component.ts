import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { environment } from '../../../../environments/environment';
import { AuthenticationService } from '../../../auth/authentication.service';
import { PrijavaService } from '../../../shared/services/prijava.service';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-prijava',
  templateUrl: './prijava.component.html',
  styleUrls: ['./prijava.component.css'],
})
export class PrijavaComponent implements OnInit {
  hide = true;
  prijavaForm!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private prijavaService: PrijavaService,
    private authService: AuthenticationService,
    private snackBar: MatSnackBar,
    private cookieService: CookieService
  ) {}

  ngOnInit(): void {
    this.prijavaForm = this.fb.group({
      korisnickoIme: [localStorage.getItem("userName"), Validators.required],
      password: ['', Validators.required],
      zapamtiMe: [false],
    });
  }

  onSubmit(): void {
    this.prijavaForm.markAllAsTouched();

    if (this.prijavaForm.valid) {
      this.prijavaService.login(this.prijavaForm.value).subscribe({
        next: (result: any) => {
          if (this.prijavaForm.value.zapamtiMe) {
            this.authService.setRememberedUser(result);
            this.snackBar.open('Prijava uspješna', 'U redu', {
              panelClass: 'green-snackbar',
            });
            if (
              this.cookieService.get('prikupljanjePodataka') == 'noCollection'
            ) {
              setTimeout(() => {
                location.href = environment.homePage;
              }, 2000);
            } else {
              location.href = environment.homePage;
            }
          } else {
            localStorage.removeItem('userName');
            this.authService.setUser(result);
            this.snackBar.open('Prijava uspješna', 'U redu', {
              panelClass: 'green-snackbar',
            });
            if (
              this.cookieService.get('prikupljanjePodataka') == 'noCollection'
            ) {
              setTimeout(() => {
                location.href = environment.homePage;
              }, 2000);
            } else {
              location.href = environment.homePage;
            }
          }
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
