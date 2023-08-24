import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { adminGuard } from './guards/admin.guard';
import { authGuard } from './guards/auth.guard';
import { korisnikGuard } from './guards/korisnik.guard';
import { moderatorGuard } from './guards/moderator.guard';
import { AppHomepageComponent } from './modules/app-homepage/app-homepage.component';
import { DnevnikradaComponent } from './modules/dnevnikrada/dnevnikrada.component';
import { DolazakNaPosaoComponent } from './modules/dolazak-na-posao/dolazak-na-posao.component';
import { KonfiguracijaComponent } from './modules/konfiguracija/konfiguracija.component';
import { KorisniciComponent } from './modules/korisnici/korisnici.component';
import { NatjecajComponent } from './modules/natjecaj/natjecaj.component';
import { PoduzecaComponent } from './modules/poduzeca/poduzeca.component';
import { AktivacijaComponent } from './modules/public/aktivacija/aktivacija.component';
import { DokumentacijaComponent } from './modules/public/dokumentacija/dokumentacija.component';
import { OAutoruComponent } from './modules/public/oautoru/oautoru.component';
import { PrijavaComponent } from './modules/public/prijava/prijava.component';
import { RegistracijaComponent } from './modules/public/registracija/registracija.component';
import { ZaboravljenaLozinkaComponent } from './modules/public/zaboravljena-lozinka/zaboravljena-lozinka.component';
import { RadniZadatakComponent } from './modules/radni-zadatak/radni-zadatak.component';
import { ZaposleniciComponent } from './modules/zaposlenici/zaposlenici.component';

const routes: Routes = [
  {
    path: '',
    component: AppHomepageComponent,
  },
  { path: 'registracija', component: RegistracijaComponent },
  { path: 'prijava', component: PrijavaComponent },
  {
    path: 'dolazak-na-posao',
    component: DolazakNaPosaoComponent,
    canActivate: [korisnikGuard],
  },
  {
    path: 'korisnici',
    component: KorisniciComponent,
    canActivate: [adminGuard],
  },
  { path: 'natjecaj', component: NatjecajComponent, canActivate: [authGuard] },
  { path: 'poduzeca', component: PoduzecaComponent, canActivate: [adminGuard] },
  {
    path: 'dnevnik-rada',
    component: DnevnikradaComponent,
    canActivate: [adminGuard],
  },
  {
    path: 'radni-zadatak',
    component: RadniZadatakComponent,
    canActivate: [authGuard],
  },
  { path: 'aktivacija', component: AktivacijaComponent },
  { path: 'zaboravljena-lozinka', component: ZaboravljenaLozinkaComponent },
  {
    path: 'konfiguracija',
    component: KonfiguracijaComponent,
    canActivate: [adminGuard],
  },
  {
    path: 'zaposlenici',
    component: ZaposleniciComponent,
    canActivate: [moderatorGuard],
  },
  {
    path: 'oautoru',
    component: OAutoruComponent,
  },
  {
    path: 'dokumentacija',
    component: DokumentacijaComponent,
  },
  { path: '**', redirectTo: '' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
