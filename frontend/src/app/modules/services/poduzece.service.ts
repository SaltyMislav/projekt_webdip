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

  getAllPoduzece() {
    return this.http.get(environment.apiUrl + '/poduzece').pipe(
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

  deletePoduzece(id: number) {

    const params = new HttpParams()
        .set('id', id.toString());

    return this.http
      .delete(environment.apiUrl + '/poduzecedelete', { params: params })
      .pipe(map((res: any) => res['data']));
  }
}
