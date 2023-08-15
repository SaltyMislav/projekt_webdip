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
}
