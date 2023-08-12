import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export const passwordValidator: ValidatorFn = (
  control: AbstractControl
): ValidationErrors | null => {
  const password = control.get('password');
  const confirmedPassword = control.get('confirmedPassword');

  password && confirmedPassword && password.value !== confirmedPassword.value
    ? control.get('confirmedPassword')?.setErrors({ passwordMismatch: true })
    : control.get('confirmedPassword')?.setErrors(null);

  return null;
};
