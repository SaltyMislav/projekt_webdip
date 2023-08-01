import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RegistracijaComponent } from './modules/registracija/registracija.component';
import { PrijavaComponent } from './modules/prijava/prijava.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';
import { AktivacijaComponent } from './modules/aktivacija/aktivacija.component';
import { adminGuard } from './guards/admin.guard';
import { moderatorGuard } from './guards/moderator.guard';
import { ZaboravljenaLozinkaComponent } from './modules/zaboravljena-lozinka/zaboravljena-lozinka.component';

const routes: Routes = [
  {
    path: '',
    component: AppHomepageComponent,
  },
  { path: 'registracija', component: RegistracijaComponent },
  { path: 'prijava', component: PrijavaComponent },
  { path: 'dolazak-na-posao', component: DolazakNaPosaoComponent },
  { path: 'korisnici', component: KorisniciComponent, canActivate: [adminGuard] },
  { path: 'natjecaj', component: NatjecajComponent, canActivate: [adminGuard, moderatorGuard] },
  { path: 'poduzeca', component: PoduzecaComponent, canActivate: [adminGuard] },
  { path: 'radni-zadatak', component: RadniZadatakComponent },
  { path: 'aktivacija', component: AktivacijaComponent },
  { path: 'zaboravljena-lozinka', component: ZaboravljenaLozinkaComponent },
  { path: '**', redirectTo: '' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
