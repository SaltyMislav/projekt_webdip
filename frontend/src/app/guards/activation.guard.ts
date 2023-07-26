import { CanActivateFn } from '@angular/router';

export const activationGuard: CanActivateFn = (route, state) => {
  return true;
};
