import { Component } from '@angular/core';

@Component({
  selector: 'app-registracija',
  templateUrl: './registracija.component.html',
  styleUrls: ['./registracija.component.css']
})
export class RegistracijaComponent {

  recaptchaSiteKey: string = '6Lc6Z8QaAAAAAEx3Z4Q4Q4Z3Z4Q4Q4Z3Z4Q4Q4Z3';
  recaptchaResponse: string = '';

  resolved(captchaResponse: string) {
    this.recaptchaResponse = captchaResponse;
    console.log(`Resolved captcha with response: ${captchaResponse}`);
  }

  //
  //valid form submit on recaptcha success TODO
}
