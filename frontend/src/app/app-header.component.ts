import { Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { SearchDialogComponent } from './modules/search-dialog/search-dialog.component';
import { AuthenticationService } from './auth/authentication.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-header',
  templateUrl: './app-header.component.html',
  styles: [
    `
      .example-spacer {
        flex: 1 1 auto;
      }

      span {
        cursor: pointer;
      }
    `,
  ],
})
export class AppHeaderComponent {

  username!: string;

  constructor(
    public dialog: MatDialog,
    protected authService: AuthenticationService
  ) {
    this.getUser();
  }

  openDialog(): void {
    const dialogRef = this.dialog.open(SearchDialogComponent);

    dialogRef.afterClosed().subscribe((result) => {
      console.log('The dialog was closed');
    });
  }

  goToHomePage() {
    location.href = environment.homePage;
  }

  getUser() {
    const user = this.authService.getUser();

    if (user) {
      this.username = user.user;
    }
  }
}
