import { Injectable } from '@angular/core';
import { Buffer } from "buffer";
import { CookieService } from 'ngx-cookie-service';
import { environment } from 'src/environments/environment';

window.Buffer = window.Buffer || require('buffer').Buffer;

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  constructor(
    private cookieService: CookieService
    ) { }

  setRememberedUser(user: any): void {
    let zapis = user.user;
    localStorage.setItem('userName', zapis);
    this.setUser(user);
  }

  setUser(user: any): void {
    let zapis = this.jsonToBase64(user);
    sessionStorage.setItem('user', zapis);
  }

  jsonToBase64(user: any): string {
    return Buffer.from(JSON.stringify(user)).toString('base64');
  }

  base64ToJson(user: any): any {
    return JSON.parse(Buffer.from(user, 'base64').toString('ascii'));
  }

  getUser(): any {
    const user = sessionStorage.getItem('user');

    if (user) {
      return this.base64ToJson(user);
    }

    return null;
  }

  removeUser(): void {
    sessionStorage.removeItem('user');
    this.cookieService.delete('PHPSESSID', '/');
    location.href = environment.homePage;
  }

  isAuthentificated(): boolean {
    return this.getUser() !== null;
  }

  isUser(): boolean {
    const user = this.getUser();
    if (user) {
      return user.uloga === 1;
    }
    return false;
  }

  isModerator(): boolean {
    const user = this.getUser();
    if (user) {
      return user.uloga >= 2;
    }
    return false;
  }

  isAdmin(): boolean {
    const user = this.getUser();
    if (user) {
      return user.uloga === 3;
    }
    return false;
  }
}
