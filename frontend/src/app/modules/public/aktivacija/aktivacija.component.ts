import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-aktivacija',
  templateUrl: './aktivacija.component.html',
  styleUrls: ['./aktivacija.component.css'],
})
export class AktivacijaComponent {
  username: string = '';
  token: string = '';
  showSuccessMessage: boolean = false;
  showErrorMessage: boolean = false;

  constructor(private http: HttpClient, private snackBar: MatSnackBar) {}

  ngOnInit(): void {
    const url = window.location.search;

    const params = new URLSearchParams(url);

    this.username = params.get('userName')!;
    this.token = params.get('token')!;

    this.http
      .post(environment.apiUrl + '/aktivacija', {
        username: this.username,
        token: this.token,
      })
      .subscribe({
        next: (res: any) => {
          this.showSuccessMessage = true;
          this.snackBar.open('Uspješno ste aktivirali račun!', 'Zatvori', {
            panelClass: 'green-snackbar',
          });
        },
        error: (err: any) => {
          this.showErrorMessage = true;
          this.snackBar.open('Neuspješna aktivacija računa!', 'Zatvori', {
            panelClass: 'red-snackbar',
          });
        },
      });
  }
}
