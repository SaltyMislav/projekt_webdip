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
    const params = new HttpParams().set('username', username);

    return this.http
      .post(environment.apiUrl + '/provjeraKorisnika', {
        params: params,
      })
      .pipe(map((res: any) => res['exists']));
  }

  submitRegistration(data: any) {
    return this.http
      .post(environment.apiUrl + '/registracija', { data: data })
      .pipe(map((res: any) => res['data']));
  }
}
