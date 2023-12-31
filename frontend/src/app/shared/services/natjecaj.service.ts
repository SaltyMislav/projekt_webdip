import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class NatjecajService {

  constructor(private http: HttpClient) { }

  getAllNatjecaj(data?: any) {
    return this.http.post(environment.apiUrl + '/natjecaj', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getStatusNatjecaja() {
    return this.http.get(environment.apiUrl + '/statusnatjecaja').pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getPoduzece(data?: any) {
    return this.http.post(environment.apiUrl + '/poduzecezanatjecaj', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      }
    ));
  }

  saveNatjecaj(data: any) {
    return this.http.post(environment.apiUrl + '/natjecajsave', {data: data}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getModeratoriNatjecaj(data?: any) {
    return this.http.post(environment.apiUrl + '/natjecajprivatno', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getPrijavljeniKorisnici(data?: any) {
    return this.http.post(environment.apiUrl + '/natjecajprijavljenikorisnici', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  prijavaKorisnika(data: any) {
    return this.http.post(environment.apiUrl + '/natjecajprijavakorisnika', {data: data}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  removePrijavljeniKorisnik(data: any) {
    return this.http.post(environment.apiUrl + '/deletenatjecajprijava', {data: data}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }
}
