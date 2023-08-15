import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class RadnizadatakService {
  constructor(private http: HttpClient) {}

  getAll(data: any) {
    return this.http
      .post(environment.apiUrl + '/radnizadatak', { data: data })
      .pipe(
        map((res: any) => {
          return res['data'];
        })
      );
  }

  getOcijena() {
    return this.http.get(environment.apiUrl + '/ocijenazaposlenika').pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getZaposlenici(data: any) {
    return this.http.post(environment.apiUrl + '/zaposleniciodabir', {data: data}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  radniZadatakSave(data: any) {
    return this.http.post(environment.apiUrl + '/radnizadataksave', { data: data }).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  radniZadatakKorisnikSave(data: any) {
    return this.http.post(environment.apiUrl + '/radnizadatakkorisniksave', { data: data }).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }
}
