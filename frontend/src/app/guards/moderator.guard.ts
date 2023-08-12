import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { environment } from '../../environments/environment';
import { AuthenticationService } from '../auth/authentication.service';

export const moderatorGuard: CanActivateFn = (route, state) => {

  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);

  if (authenticationService.isModerator()) {
    return true;
  }

  router.navigateByUrl(environment.homePage);
  return false;
};
