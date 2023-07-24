//create service for enforcing https
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class PrijavaEnforcehttpsService {
  constructor(private http: HttpClient) {}

  canActivate() {
    if (
      location.protocol !== 'https:' &&
      !location.href.startsWith('https://') &&
      !location.href.includes('localhost')
    ) {
      return this.http.get(environment.apiUrl + '/enforceHttps').subscribe();
    }
    return null;
  }
}
