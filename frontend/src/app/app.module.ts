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

import { FormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { AppFooterComponent } from './app-footer.component';
import { AppHeaderComponent } from './app-header.component';
import { AppMenuComponent } from './app-menu.component';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { PrijavaComponent } from './modules/prijava/prijava.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { RegistracijaComponent } from './modules/registracija/registracija.component';

@NgModule({
  declarations: [
    AppComponent,
    RegistracijaComponent,
    PrijavaComponent,
    KorisniciComponent,
    PoduzecaComponent,
    RadniZadatakComponent,
    NatjecajComponent,
    DolazakNaPosaoComponent,
    AppFooterComponent,
    AppHeaderComponent,
    AppMenuComponent,
    AppHomepageComponent,
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
  ],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
