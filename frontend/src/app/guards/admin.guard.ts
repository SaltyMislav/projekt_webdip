import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthenticationService } from '../auth/authentication.service';
import { environment } from 'src/environments/environment';

export const adminGuard: CanActivateFn = (route, state) => {

  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);

  if (authenticationService.isAdmin()) {
    return true;
  }

  router.navigateByUrl(environment.homePage);
  return false;
};
