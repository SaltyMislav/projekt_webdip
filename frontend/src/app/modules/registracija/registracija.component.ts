import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { RegistracijaService } from '../services/registracija.service';
import { UsernameValidator } from './username.validator';

@Component({
  selector: 'app-registracija',
  templateUrl: './registracija.component.html',
  styleUrls: ['./registracija.component.css']
})
export class RegistracijaComponent implements OnInit {

  hide = true;
  registracijaForm!: FormGroup;

  recaptchaSiteKey: string = '6Lc6Z8QaAAAAAEx3Z4Q4Q4Z3Z4Q4Q4Z3Z4Q4Q4Z3';
  recaptchaResponse: string = '';

  constructor(
    private fb: FormBuilder,
    private registracijaService: RegistracijaService,
    private snackBar: MatSnackBar,
  ) { }

  ngOnInit(): void {
    this.registracijaForm = this.fb.group({
      ime: ['', Validators.compose([Validators.required, Validators.minLength(3), Validators.maxLength(20)])],
      prezime: ['', Validators.compose([Validators.required, Validators.minLength(3), Validators.maxLength(20)])],
      userName: ['', Validators.compose([Validators.required, Validators.minLength(3), Validators.maxLength(20)]), [UsernameValidator.CreateValidator(this.registracijaService)]],
      email: ['', Validators.compose([Validators.required, Validators.email])],
      password: ['', [Validators.required, Validators.minLength(8), Validators.pattern('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/')]],
      confirmedPassword: ['', [Validators.required, Validators.minLength(8), Validators.pattern('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/')]],
      captcha: ['', Validators.required]
    });
  }

  resolved(captchaResponse: string) {
    this.recaptchaResponse = captchaResponse;
    console.log(`Resolved captcha with response: ${captchaResponse}`);
  }

  //
  //valid form submit on recaptcha success TODO

  onSubmit() {
    console.log(this.registracijaForm.value);
  }
}
