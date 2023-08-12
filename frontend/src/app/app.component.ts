import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import {
  MatBottomSheet,
  MatBottomSheetRef,
} from '@angular/material/bottom-sheet';
import { CookieService } from 'ngx-cookie-service';
import { KonfiguracijaClass } from './shared/services/class/konfiguracijaclass.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent implements OnInit {
  title = 'zaposljavanje';

  constructor(
    private cookieService: CookieService,
    private bottomSheet: MatBottomSheet,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef
  ) {
    this.konfiguracijaClass.reset();
    this.konfiguracijaClass.getData();
  }

  ngOnInit(): void {
    if (!this.cookieService.check('prihvaceniKolacici')) {
      this.bottomSheet.open(CookieSheet);
    }
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
