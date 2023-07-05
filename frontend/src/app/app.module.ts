import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

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
import { AppHeaderComponent } from './app-header.component';
import { AppFooterComponent } from './app-footer.component';
import { AppMenuComponent } from './app-menu.component';
import { AppMenuComponentComponent } from './app.menu.component.component';
import { AppFooterComponentComponent } from './app.footer.component.component';
import { AppHeaderComponentComponent } from './app.header.component.component';

@NgModule({
  declarations: [
    AppComponent,
    AppHeaderComponent,
    AppFooterComponent,
    AppMenuComponent,
    RegistracijaComponent,
    PrijavaComponent,
    KorisniciComponent,
    PoduzecaComponent,
    RadniZadatakComponent,
    NatjecajComponent,
    DolazakNaPosaoComponent,
    AppMenuComponentComponent,
    AppFooterComponentComponent,
    AppHeaderComponentComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
