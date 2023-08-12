import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { map } from 'rxjs';
import { Poduzece } from 'src/app/interfaces/interfaces';

@Injectable({
  providedIn: 'root',
})
export class PoduzeceService {
  constructor(private http: HttpClient) {}

  getAllPoduzece(data?: any) {
    return this.http.post(environment.apiUrl + '/poduzece', data ? {data: data} : null).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  getPoduzeceWithoutModerator() {
    return this.http.get(environment.apiUrl + '/poduzecebezmoderatora').pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  insertUpdatePoduzeca(data: Poduzece) {
    return this.http
      .post(environment.apiUrl + '/poduzeceinsert', { data: data })
      .pipe(map((res: any) => res['data']));
  }
}
