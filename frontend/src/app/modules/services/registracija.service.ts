import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class RegistracijaService {
  constructor(private http: HttpClient) {}

  checkUsername(username: string) {
    const params = new HttpParams().set('username', username.toString());

    return this.http
      .post<{ exists: boolean }>(environment.apiUrl + '/provjeraKorisnika', {
        params: params,
      })
      .pipe(map((res) => res.exists));
  }
}
