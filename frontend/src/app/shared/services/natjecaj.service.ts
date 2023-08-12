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

  getModeratoriNatjecaj(data?: any) {
    return this.http.post(environment.apiUrl + '/natjecajprivatno', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }
}
