import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class DolazakNaPosaoService {
  constructor(private http: HttpClient) {}

  dolazakNaPosao(data: any) {
    return this.http
      .post(environment.apiUrl + '/dolascinaposaosave', {data: data})
      .pipe(
        map((res: any) => {
          return res['data'];
        })
      );
  }
}
