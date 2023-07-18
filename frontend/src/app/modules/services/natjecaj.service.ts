import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NatjecajService {

  baseurl = 'http://localhost/backend';

  constructor(private http: HttpClient) { }

  getAllNatjecaj() {
    return this.http.get(this.baseurl + '/natjecaj').pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }
}
