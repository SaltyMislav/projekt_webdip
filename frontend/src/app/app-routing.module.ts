import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RegistracijaComponent } from './modules/registracija/registracija.component';
import { PrijavaComponent } from './modules/prijava/prijava.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';

const routes: Routes = [
  { path: 'registracija', component: RegistracijaComponent },
  { path: 'prijava', component: PrijavaComponent },
  { path: 'dolazak-na-posao', component: DolazakNaPosaoComponent },
  { path: 'korisnici', component: KorisniciComponent },
  { path: 'natjecaj', component: NatjecajComponent },
  { path: 'poduzeca', component: PoduzecaComponent },
  { path: 'radni-zadatak', component: RadniZadatakComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
