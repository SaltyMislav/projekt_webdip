import { Component } from '@angular/core';
import { AuthenticationService } from './auth/authentication.service';

@Component({
  selector: 'app-menu',
  templateUrl: './app-menu.component.html',
  styles: [
  ]
})
export class AppMenuComponent {
  constructor(protected authService: AuthenticationService) { }
}
