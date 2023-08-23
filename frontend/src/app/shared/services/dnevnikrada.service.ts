import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DnevnikradaService {

  constructor(
    private http: HttpClient,
  ) { }

  getDnevnikRada(data: any) {
    return this.http
      .post(environment.apiUrl + '/dnevnikrada', {data: data})
      .pipe(
        map((res: any) => {
          return res['data'];
        }
      )
    );
  }
}
