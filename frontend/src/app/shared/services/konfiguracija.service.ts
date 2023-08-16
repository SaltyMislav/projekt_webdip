import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { map } from "rxjs";
import { environment } from "src/environments/environment";

@Injectable({
    providedIn: "root",
})
export class KonfiguracijaService {
    constructor(private http: HttpClient) {}

    dohvatPomaka() {
        return this.http.get(environment.apiUrl + "/dohvativirtualnovrijeme").pipe(
            map((res: any) => {
                return res["data"];
            })
        );
    }

    getdata() {
        return this.http.get(environment.apiUrl + "/konfiguracijaget").pipe(
            map((res: any) => {
                return res["data"];
            })
        );
    }

    postaviStranicenje(data: any) {
        return this.http
            .post(environment.apiUrl + "/postavistranicenje", { data: data })
            .pipe(
                map((res: any) => {
                    return res["data"];
                })
            );
    }
}