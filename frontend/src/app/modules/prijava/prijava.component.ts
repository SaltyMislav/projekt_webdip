import { Component, OnInit } from '@angular/core';
import { PrijavaEnforcehttpsService } from '../services/prijavaEnforcehttps.service';
import { PrijavaService } from '../services/prijava.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthenticationService } from '../../auth/authentication.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-prijava',
  templateUrl: './prijava.component.html',
  styleUrls: ['./prijava.component.css'],
})
export class PrijavaComponent implements OnInit {

  prijavaForm!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private enforceHttps: PrijavaEnforcehttpsService,
    private prijavaService: PrijavaService,
    private authService: AuthenticationService,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.enforceHttps.canActivate();

    this.prijavaForm = this.fb.group({
      korisnickoIme: ['', Validators.required],
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
            location.href = environment.homePage;
            this.snackBar.open('Prijava uspješna', 'U redu', {
              panelClass: 'green-snackbar',
            });
          } else {
            this.authService.setUser(result);
            location.href = environment.homePage;
            this.snackBar.open('Prijava uspješna', 'U redu', {
              panelClass: 'green-snackbar',
            });
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
