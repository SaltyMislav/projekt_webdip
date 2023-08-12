import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { map } from "rxjs";
import { environment } from "src/environments/environment";

@Injectable({
    providedIn: "root",
})
export class KorisniciService {
    constructor(private http: HttpClient) {}

    getAllKorisnici(data?: any) {
        return this.http.post(environment.apiUrl + '/korisnici', data ? {data: data} : null).pipe(
            map((res: any) => {
                return res['data'];
            })
        );
    }

    getZaposlenici(data?: any) {
        return this.http.post(environment.apiUrl + '/zaposlenici', data ? {data: data} : null).pipe(
            map((res: any) => {
                return res['data'];
            })
        );
    }

    getZaposleniciPrivatno(data?: any) {
        return this.http.post(environment.apiUrl + '/zaposleniciPrivatno', data ? {data: data} : null).pipe(
            map((res: any) => {
                return res['data'];
            })
        );
    }

    getKorisniciModeratori() {
        return this.http.get(environment.apiUrl + '/korisniciModeratori').pipe(
            map((res: any) => {
                return res['data'];
            })
        );
    }

    onSaveKorisnik(korisnik: any) {
        return this.http.post(environment.apiUrl + '/korisniciSave', {data: korisnik}).pipe(
            map((res: any) => {
                return res['data'];
            })
        );
    }
}