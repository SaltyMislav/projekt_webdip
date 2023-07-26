import { Injectable } from '@angular/core';
import { Buffer } from "buffer";

window.Buffer = window.Buffer || require('buffer').Buffer;

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  constructor() { }

  setRememberedUser(user: any): void {
    let zapis = this.jsonToBase64(user);
    localStorage.setItem('user', zapis);
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
    const rememberedUser = localStorage.getItem('user');

    if (rememberedUser) {
      return this.base64ToJson(rememberedUser);
    }

    if (user) {
      return this.base64ToJson(user);
    }

    return null;
  }

  removeUser(): void {
    sessionStorage.removeItem('user');
    localStorage.removeItem('user');
  }

  isAuthentificated(): boolean {
    return this.getUser() !== null;
  }

  isModerator(): boolean {
    const user = this.getUser();
    if (user) {
      return user.uloga === 2;
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
