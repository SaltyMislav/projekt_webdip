import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { MatMenuModule } from '@angular/material/menu';
import { MatSidenavModule } from '@angular/material/sidenav';

import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { RegistracijaComponent } from './modules/registracija/registracija.component';
import { PrijavaComponent } from './modules/prijava/prijava.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { AppFooterComponent } from './app-footer.component';
import { AppHeaderComponent } from './app-header.component';
import { AppMenuComponent } from './app-menu.component';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';

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
    AppHomepageComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    MatMenuModule,
    MatSidenavModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
