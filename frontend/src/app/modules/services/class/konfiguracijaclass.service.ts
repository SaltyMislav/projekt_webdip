import { Injectable } from '@angular/core';
import { Subject, catchError, of, take, tap } from 'rxjs';
import { KonfiguracijaService } from '../konfiguracija.service';

@Injectable({
  providedIn: 'root',
})
export class KonfiguracijaClass {
  private _stranicenje = 0;
  private _pomak = 0;

  public konfiguracijaDataSubject = new Subject<any>();

  constructor(private konfiguracijaService: KonfiguracijaService) {}

  public get stranicenje(): number {
    return this._stranicenje;
  }

  public set stranicenje(value: number) {
    this._stranicenje = value;
  }

  public get pomak(): number {
    return this._pomak;
  }

  public set pomak(value: number) {
    this._pomak = value;
  }

  public getData() {
    this.konfiguracijaService
      .getdata()
      .pipe(
        take(1),
        tap((response) => {
          this.pomak = response[0].Pomak ?? this._pomak;
          this.stranicenje = response[0].Stranicenje ?? this._stranicenje;

          console.log(this._pomak, this._stranicenje);

          this.konfiguracijaDataSubject.next({
            pomak: this._pomak,
            stranicenje: this._stranicenje,
          });

        }),
        catchError((err) => {
          return of(err);
        })
      )
      .subscribe();
  }

  public reset() {
    this.stranicenje = 0;
    this.pomak = 0;
  }
}
