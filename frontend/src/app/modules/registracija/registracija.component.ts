import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { RegistracijaService } from '../services/registracija.service';
import { UsernameValidator } from './username.validator';
import { passwordValidator } from './password.validator';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-registracija',
  templateUrl: './registracija.component.html',
  styleUrls: ['./registracija.component.css'],
})
export class RegistracijaComponent implements OnInit {
  hide = true;
  hide2 = true;

  recaptchaSiteKey: string = '6Lc6Z8QaAAAAAEx3Z4Q4Q4Z3Z4Q4Q4Z3Z4Q4Q4Z3';
  recaptchaResponse: string = '';

  registracijaForm = this.fb.group(
    {
      ime: [
        '',
        [
          Validators.required,
          Validators.minLength(3),
          Validators.maxLength(20),
        ],
      ],
      prezime: [
        '',
        [
          Validators.required,
          Validators.minLength(3),
          Validators.maxLength(20),
        ],
      ],
      userName: [
        '',
        {
          validators: [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(20),
          ],
          asyncValidators: [
            this.usernameValidator.validate.bind(this.usernameValidator),
          ],
          updateOn: 'blur',
        },
      ],
      email: ['', [Validators.required, Validators.email]],
      password: [
        '',
        [
          Validators.required,
          Validators.minLength(8),
          Validators.pattern(
            '^(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$'
          ),
        ],
      ],
      confirmedPassword: ['', [Validators.required]],
    },
    { validators: passwordValidator }
  );

  constructor(
    private fb: FormBuilder,
    protected registracijaService: RegistracijaService,
    private usernameValidator: UsernameValidator,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {}

  resolved(captchaResponse: string) {
    this.recaptchaResponse = captchaResponse;
    console.log(`Resolved captcha with response: ${captchaResponse}`);
  }

  getImeError() {
    if (this.registracijaForm.controls['ime'].hasError('required')) {
      return 'Ime je obavezno';
    }
    if (this.registracijaForm.controls['ime'].hasError('minlength')) {
      return 'Ime mora imati najmanje 3 znaka';
    }
    if (this.registracijaForm.controls['ime'].hasError('maxlength')) {
      return 'Ime može imati najviše 20 znakova';
    }
    return '';
  }

  getPrezimeError() {
    if (this.registracijaForm.controls['prezime'].hasError('required')) {
      return 'Prezime je obavezno';
    }
    if (this.registracijaForm.controls['prezime'].hasError('minlength')) {
      return 'Prezime mora imati najmanje 3 znaka';
    }
    if (this.registracijaForm.controls['prezime'].hasError('maxlength')) {
      return 'Prezime može imati najviše 20 znakova';
    }
    return '';
  }

  getEmailError() {
    if (this.registracijaForm.controls['email'].hasError('required')) {
      return 'Email je obavezan';
    }
    if (this.registracijaForm.controls['email'].hasError('email')) {
      return 'Email nije ispravnog formata';
    }
    return '';
  }

  getPasswordError() {
    if (this.registracijaForm.controls['password'].hasError('required')) {
      return 'Lozinka je obavezna';
    }
    if (this.registracijaForm.controls['password'].hasError('minlength')) {
      return 'Lozinka mora imati najmanje 8 znakova';
    }
    if (this.registracijaForm.controls['password'].hasError('pattern')) {
      return 'Lozinka mora sadržavati barem jedno veliko slovo, jedno malo slovo i jedan broj';
    }
    return '';
  }

  getConfirmedPasswordError() {
    if (
      this.registracijaForm.controls['confirmedPassword'].hasError('required')
    ) {
      return 'Potvrda lozinke je obavezna';
    }
    if (
      this.registracijaForm.controls['confirmedPassword'].hasError(
        'passwordMismatch'
      )
    ) {
      return 'Lozinke se ne podudaraju';
    }
    return '';
  }

  getUsernameError() {
    if (this.registracijaForm.controls['userName'].hasError('required')) {
      return 'Korisničko ime je obavezno';
    }
    if (this.registracijaForm.controls['userName'].hasError('minlength')) {
      return 'Korisničko ime mora imati najmanje 3 znaka';
    }
    if (this.registracijaForm.controls['userName'].hasError('maxlength')) {
      return 'Korisničko ime može imati najviše 20 znakova';
    }
    if (this.registracijaForm.controls['userName'].hasError('usernameExists')) {
      return 'Korisničko ime već postoji';
    }
    return '';
  }

  onSubmit() {
    this.registracijaForm.markAllAsTouched();

    if(this.recaptchaResponse == '') {
      this.snackBar.open('Molimo potvrdite da niste robot', 'U redu', {
        panelClass: 'red-snackbar',
      });
    }

    if (this.registracijaForm.valid && this.recaptchaResponse != '') {

      const formValues = this.registracijaForm.value;
      formValues.captcha = this.recaptchaResponse;
      
      this.registracijaService.submitRegistration(formValues).subscribe({
        next: (result: any) => {
          location.href = environment.homePage;
          this.snackBar.open('Registracija uspješna', 'U redu', {
            panelClass: 'green-snackbar',
          });
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
