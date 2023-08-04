import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { map } from "rxjs";
import { environment } from "src/environments/environment";

@Injectable({
    providedIn: "root",
})
export class KorisniciService {
    constructor(private http: HttpClient) {}

    getAllKorisnici() {
        return this.http.get(environment.apiUrl + '/korisnici').pipe(
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