import { COMMA, ENTER } from '@angular/cdk/keycodes';
import {
  Component,
  ElementRef,
  Inject,
  OnInit,
  ViewChild,
} from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { KorisniciService } from '../../services/korisnici.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Poduzece, UlogaKorisnika } from 'src/app/interfaces/interfaces';
import { Observable, debounce, debounceTime, map, startWith } from 'rxjs';
import { PoduzeceService } from '../../services/poduzece.service';
import { MatChipInputEvent } from '@angular/material/chips';

@Component({
  selector: 'app-korisnici-dialog',
  templateUrl: './korisnici-dialog.component.html',
  styleUrls: ['./korisnici-dialog.component.css'],
})
export class KorisniciDialogComponent implements OnInit {
  form!: FormGroup;
  uloge: UlogaKorisnika[] = [
    { ID: 1, Naziv: 'Korisnik' },
    { ID: 2, Naziv: 'Moderator' },
    { ID: 3, Naziv: 'Administrator' },
  ];

  separatorKeysCodes: number[] = [ENTER, COMMA];
  filteredPoduzeca!: Observable<Poduzece[]>;
  svaPoduzeca: Poduzece[] = [];
  poduzeca: string[] = [];
  poduzecaArray: any[] = [];

  selectedUloga = 1;

  @ViewChild('poduzeceInput') poduzeceInput!: ElementRef<HTMLInputElement>;

  constructor(
    private fb: FormBuilder,
    private korisnikService: KorisniciService,
    private poduzeceService: PoduzeceService,
    private snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<KorisniciDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {}

  ngOnInit(): void {
    this.poduzeceService.getPoduzeceWithoutModerator().subscribe((data: any) => {
      this.svaPoduzeca = data;
    });

    console.log(this.data);

    this.form = this.fb.group({
      ID: [this.data.ID],
      Ime: [{ value: this.data.Ime, disabled: true }],
      Prezime: [{ value: this.data.Prezime, disabled: true }],
      KorisnickoIme: [{ value: this.data.KorisnickoIme, disabled: true }],
      Email: [{ value: this.data.Email, disabled: true }],
      NeuspjesnePrijave: [this.data.NeuspjesnePrijave],
      UlogaKorisnikaID: [this.data.UlogaKorisnikaID],
      Active: [+this.data.Active],
      Blokiran: [+this.data.Blokiran],
      poduzeceCtrl: [''],
    });

    this.selectedUloga = this.data.UlogaKorisnikaID;

    this.data?.Poduzece.forEach((element: any) => {
      this.poduzeca.push(element.Naziv);
      this.poduzecaArray.push({
        ID: element.ID,
        Naziv: element.Naziv,
      });
    });

    this.filteredPoduzeca = this.form.controls[
      'poduzeceCtrl'
    ].valueChanges.pipe(
      startWith(null),
      debounceTime(100),
      map((value) => (typeof value === 'string' ? value : value?.Naziv)),
      map((name: string | null) =>
        name ? this._filter(name) : this.svaPoduzeca.slice()
      )
    );
  }

  add(event: MatChipInputEvent) {
    const value = (event.value || '').trim();

    if (value) {
      this.poduzeca.push(value);
    }

    event.chipInput!.clear();
    this.form.controls['poduzeceCtrl'].setValue(null);
  }

  remove(poduzece: string) {
    const index = this.poduzeca.indexOf(poduzece);

    if (index >= 0) {
      this.poduzeca.splice(index, 1);
      this.poduzecaArray.splice(index, 1);
    }
  }

  displayFn(poduzece: Poduzece): string {
    return poduzece && poduzece.Naziv ? poduzece.Naziv : '';
  }

  selected(event: any): void {
    this.poduzeca.push(event.option.viewValue);
    this.poduzecaArray.push(event.option.value);
    this.poduzeceInput.nativeElement.value = '';
    this.form.controls['poduzeceCtrl'].setValue(null);
  }

  private _filter(name: string): Poduzece[] {
    const filterValue = name.toLowerCase();

    return this.svaPoduzeca.filter((poduzece) =>
      poduzece.Naziv.toLowerCase().includes(filterValue)
    );
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    this.form.markAllAsTouched();
    if (this.form.valid) {
      this.form.controls['poduzeceCtrl'].setValue(this.poduzecaArray);
      this.korisnikService.onSaveKorisnik(this.form.getRawValue()).subscribe({
        next: (result) => {
          this.snackBar.open('Korisnik je uspješno spremljen', 'U redu', {
            panelClass: 'green-snackbar',
          });
          this.dialogRef.close();
        },
        error: (err) => {
          this.snackBar.open(err.error.error.errstr, 'U redu', {
            panelClass: 'red-snackbar',
          });
        },
      });
    }
  }
}
