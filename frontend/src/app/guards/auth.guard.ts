import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthenticationService } from '../auth/authentication.service';

export const authGuard: CanActivateFn = (route, state) => {

  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);

  if (authenticationService.isAuthentificated()) {
    return true;
  }

  return false;
};
