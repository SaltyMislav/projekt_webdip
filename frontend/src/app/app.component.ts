import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import {
  MatBottomSheet,
  MatBottomSheetRef,
} from '@angular/material/bottom-sheet';
import { CookieService } from 'ngx-cookie-service';
import { KonfiguracijaClass } from './modules/services/class/konfiguracijaclass.service';
import { Subject, Subscription, takeUntil } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent implements OnInit {
  title = 'zaposljavanje';

  pomak!: number;
  stranicenje!: number;

  konfiguracijaDataSubscription!: Subscription;
  notifier = new Subject<any>();

  constructor(
    private cookieService: CookieService,
    private bottomSheet: MatBottomSheet,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    if (!this.cookieService.check('prihvaceniKolacici')) {
      this.bottomSheet.open(CookieSheet);
    }
    this.konfiguracijaClass.reset();
    this.konfiguracijaClass.getData();
  }
}

@Component({
  selector: 'cookie-sheet',
  template: `
    <mat-card>
      <mat-card-header>
        <mat-card-title>Upotreba kolačića</mat-card-title>
      </mat-card-header>
      <mat-card-content> TODO: Upotreba kolačića </mat-card-content>
      <mat-card-actions align="end">
        <button mat-raised-button color="primary" (click)="prihvacanje()">
          Prihvaćam
        </button>
        <button mat-button (click)="bottomSheetRef.dismiss()">
          Ne prihvaćam
        </button>
      </mat-card-actions>
    </mat-card>
  `,
})
export class CookieSheet {
  constructor(
    private cookieService: CookieService,
    protected bottomSheetRef: MatBottomSheetRef<CookieSheet>
  ) {}

  prihvacanje() {
    this.cookieService.set('prihvaceniKolacici', 'true', 2);
    this.bottomSheetRef.dismiss();
    window.location.reload();
  }
}
