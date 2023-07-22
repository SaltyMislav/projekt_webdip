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
import { FormsModule } from '@angular/forms';
import { MatBottomSheetModule } from '@angular/material/bottom-sheet';
import { MatCardModule } from '@angular/material/card';
import {
  MAT_DIALOG_DEFAULT_OPTIONS,
  MatDialogModule,
} from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatListModule } from '@angular/material/list';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatSortModule } from '@angular/material/sort';
import { MatTableModule } from '@angular/material/table';
import { CookieService } from 'ngx-cookie-service';
import { AppFooterComponent } from './app-footer.component';
import { AppHeaderComponent } from './app-header.component';
import { AppMenuComponent } from './app-menu.component';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent, CookieSheet } from './app.component';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KorisniciPublicComponent } from './modules/korisnici-public/korisnici-public.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajPublicComponent } from './modules/natjecaj-public/natjecaj-public.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { PrijavaComponent } from './modules/prijava/prijava.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { RegistracijaComponent } from './modules/registracija/registracija.component';
import { SearchDialogComponent } from './modules/search-dialog/search-dialog.component';

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
    FormsModule,
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
  ],
  providers: [
    { provide: MAT_DIALOG_DEFAULT_OPTIONS, useValue: { hasBackdrop: false } },
    CookieService,
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
