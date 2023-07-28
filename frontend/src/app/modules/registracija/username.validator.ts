import {
    AbstractControl,
    AsyncValidator,
    ValidationErrors
} from '@angular/forms';
import { Observable, catchError, first, map, of } from 'rxjs';
import { RegistracijaService } from '../services/registracija.service';
import { Injectable } from '@angular/core';

@Injectable({
    providedIn: 'root',
})
export class UsernameValidator implements AsyncValidator {
  constructor(private registracijaService: RegistracijaService) {}

  validate(
    control: AbstractControl
  ): Observable<ValidationErrors | null> {
    return this.registracijaService.checkUsername(control.value).pipe(
      map(res => {
        if (res == "true") {
          return { usernameExists: true };
        }
        return null;
      }),
      catchError(() => of(null))
    ).pipe(first());
  }
}
