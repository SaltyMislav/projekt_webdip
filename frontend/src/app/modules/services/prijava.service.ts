import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class PrijavaService {

  constructor(private http: HttpClient) { }

  login(data: any) {
    return this.http.post(environment.apiUrl + '/prijava', {data: data})
    .pipe(map((res: any) => res['data']));
  }

  zaboravljenaLozinka(data: any) {
    return this.http.post(environment.apiUrl + '/zaboravljenaLozinka', {data: data})
    .pipe(map((res: any) => res['data']));
  }
}
