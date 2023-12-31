import { NgModule } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatMenuModule } from '@angular/material/menu';
import { MatSelectModule } from '@angular/material/select';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { BrowserModule } from '@angular/platform-browser';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { HttpClientModule } from '@angular/common/http';

import { NgIf } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatBottomSheetModule } from '@angular/material/bottom-sheet';
import { MatCardModule } from '@angular/material/card';
import { MatCheckboxModule } from '@angular/material/checkbox';
import {
  MAT_DIALOG_DEFAULT_OPTIONS,
  MatDialogModule,
} from '@angular/material/dialog';
import {
  MAT_FORM_FIELD_DEFAULT_OPTIONS,
  MatFormFieldModule,
} from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatListModule } from '@angular/material/list';
import { MatPaginatorModule } from '@angular/material/paginator';
import {
  MAT_SNACK_BAR_DEFAULT_OPTIONS,
  MatSnackBarModule,
} from '@angular/material/snack-bar';
import { MatSortModule } from '@angular/material/sort';
import { MatTableModule } from '@angular/material/table';
import { CookieService } from 'ngx-cookie-service';
import { AppFooterComponent } from './app-footer.component';
import { AppHeaderComponent } from './app-header.component';
import { AppMenuComponent } from './app-menu.component';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent, CookieSheet } from './app.component';
import { AktivacijaComponent } from './modules/public/aktivacija/aktivacija.component';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KorisniciPublicComponent } from './modules/public/korisnici-public/korisnici-public.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajPublicComponent } from './modules/public/natjecaj-public/natjecaj-public.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { PoduzeceDialogComponent } from './modules/poduzeca/poduzece-dialog/poduzece-dialog.component';
import { PrijavaComponent } from './modules/public/prijava/prijava.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { RegistracijaComponent } from './modules/public/registracija/registracija.component';
import { SearchDialogComponent } from './modules/search-dialog/search-dialog.component';
import { RecaptchaModule } from 'ng-recaptcha';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { ZaboravljenaLozinkaComponent } from './modules/public/zaboravljena-lozinka/zaboravljena-lozinka.component';
import { KonfiguracijaComponent } from './modules/konfiguracija/konfiguracija.component';
import { KorisniciDialogComponent } from './modules/korisnici/korisnici-dialog/korisnici-dialog.component';
import { MatSlideToggleModule } from '@angular/material/slide-toggle';
import { MatChipsModule } from '@angular/material/chips';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { ZaposleniciComponent } from './modules/zaposlenici/zaposlenici.component';
import { NatjecajDialogComponent } from './modules/natjecaj/natjecaj-dialog/natjecaj-dialog.component';
import { PrijavaNatjecajComponent } from './modules/natjecaj/prijava-natjecaj/prijava-natjecaj.component';
import { NatjecajDialogPublicComponent } from './modules/public/natjecaj-public/natjecaj-dialog-public/natjecaj-dialog-public.component';
import { RadniZadatakDialogComponent } from './modules/radni-zadatak/radni-zadatak-dialog/radni-zadatak-dialog.component';
import { DnevnikradaComponent } from './modules/dnevnikrada/dnevnikrada.component';
import { OAutoruComponent } from './modules/public/oautoru/oautoru.component';
import { DokumentacijaComponent } from './modules/public/dokumentacija/dokumentacija.component';

@NgModule({
  declarations: [
    AppComponent,
    CookieSheet,
    RegistracijaComponent,
    PrijavaComponent,
    KorisniciComponent,
    PoduzecaComponent,
    RadniZadatakComponent,
    NatjecajPublicComponent,
    DolazakNaPosaoComponent,
    AppFooterComponent,
    AppHeaderComponent,
    AppMenuComponent,
    AppHomepageComponent,
    SearchDialogComponent,
    NatjecajComponent,
    KorisniciPublicComponent,
    AktivacijaComponent,
    PoduzeceDialogComponent,
    ZaboravljenaLozinkaComponent,
    KonfiguracijaComponent,
    KorisniciDialogComponent,
    ZaposleniciComponent,
    NatjecajDialogComponent,
    PrijavaNatjecajComponent,
    NatjecajDialogPublicComponent,
    RadniZadatakDialogComponent,
    DnevnikradaComponent,
    OAutoruComponent,
    DokumentacijaComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    MatMenuModule,
    MatSidenavModule,
    MatToolbarModule,
    MatIconModule,
    MatButtonModule,
    MatSelectModule,
    MatFormFieldModule,
    MatInputModule,
    BrowserAnimationsModule,
    NgIf,
    MatDialogModule,
    MatListModule,
    MatTableModule,
    MatSortModule,
    MatPaginatorModule,
    MatCardModule,
    MatBottomSheetModule,
    ReactiveFormsModule,
    FormsModule,
    MatSnackBarModule,
    MatCheckboxModule,
    RecaptchaModule,
    MatProgressSpinnerModule,
    MatSlideToggleModule,
    MatChipsModule,
    MatAutocompleteModule,
  ],
  providers: [
    {
      provide: MAT_FORM_FIELD_DEFAULT_OPTIONS,
      useValue: { appearance: 'outline' },
    },
    { provide: MAT_DIALOG_DEFAULT_OPTIONS, useValue: { hasBackdrop: true } },
    {
      provide: MAT_SNACK_BAR_DEFAULT_OPTIONS,
      useValue: {
        duration: 2000,
        horizontalPosition: 'end',
        verticalPosition: 'top',
      },
    },
    CookieService,
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
