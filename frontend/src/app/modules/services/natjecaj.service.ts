import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class NatjecajService {

  constructor(private http: HttpClient) { }

  getAllNatjecaj() {
    return this.http.get(environment.production + '/natjecaj').pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }
}
