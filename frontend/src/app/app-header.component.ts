import { Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { SearchDialogComponent } from './modules/search-dialog/search-dialog.component';

@Component({
  selector: 'app-header',
  templateUrl: './app-header.component.html',
  styles: [
    `
      .example-spacer {
        flex: 1 1 auto;
      }
    `,
  ],
})
export class AppHeaderComponent {
  constructor(public dialog: MatDialog) {}

  openDialog(): void {
    const dialogRef = this.dialog.open(SearchDialogComponent);

    dialogRef.afterClosed().subscribe((result) => {
      console.log('The dialog was closed');
    });
  }
}
