import { AbstractControl, AsyncValidatorFn, ValidationErrors } from "@angular/forms";
import { RegistracijaService } from "../services/registracija.service";
import { Observable, map } from "rxjs";

export class UsernameValidator {
    
    static CreateValidator(registracijaService: RegistracijaService): AsyncValidatorFn {
        return (control: AbstractControl): Promise<ValidationErrors | null> | Observable<ValidationErrors | null> => {
            return registracijaService.checkUsername(control.value).pipe(
                map(res => res ? { usernameExists: true } : null)
            );
        };
    }
}