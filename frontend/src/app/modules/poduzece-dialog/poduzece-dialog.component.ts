import { COMMA, ENTER } from '@angular/cdk/keycodes';
import {
  Component,
  ElementRef,
  Inject,
  OnInit,
  ViewChild,
} from '@angular/core';
import {
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { PoduzeceService } from '../services/poduzece.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable, debounceTime, map, startWith } from 'rxjs';
import { MatChipInputEvent } from '@angular/material/chips';
import { KorisniciService } from '../services/korisnici.service';
import { KorisniciModeratori } from 'src/app/interfaces/interfaces';

@Component({
  selector: 'app-poduzece-dialog',
  templateUrl: './poduzece-dialog.component.html',
  styleUrls: ['./poduzece-dialog.component.css'],
})
export class PoduzeceDialogComponent implements OnInit {
  formGroup!: FormGroup;
  separatorKeysCodes: number[] = [ENTER, COMMA];
  filteredKorisnici!: Observable<KorisniciModeratori[]>;
  korisnici: KorisniciModeratori[] = [];
  moderatori: string[] = [];
  moderatoriArray: any[] = [];

  @ViewChild('moderatorInput') moderatorInput!: ElementRef<HTMLInputElement>;

  constructor(
    private fb: FormBuilder,
    private poduzeceService: PoduzeceService,
    private snackBar: MatSnackBar,
    private korisniciService: KorisniciService,
    public dialogRef: MatDialogRef<PoduzeceDialogComponent>,
    @Inject(MAT_DIALOG_DATA) protected data: any
  ) {}

  ngOnInit(): void {
    this.korisniciService.getKorisniciModeratori().subscribe((data: any) => {
      this.korisnici = data;
    });

    this.formGroup = this.fb.group({
      ID: [this.data?.ID],
      Naziv: [this.data?.Naziv, Validators.required],
      Opis: [this.data?.Opis, Validators.required],
      RadnoVrijemeOd: [this.data?.RadnoVrijemeOd, Validators.required],
      RadnoVrijemeDo: [this.data?.RadnoVrijemeDo, Validators.required],
      moderatorCtrl: [''],
    });

    this.data?.Moderatori.forEach((element: any) => {
      this.moderatori.push(element.KorisnickoIme);
      this.moderatoriArray.push({
        ID: element.ID,
        KorisnickoIme: element.KorisnickoIme,
      });
    });

    this.filteredKorisnici = this.formGroup.controls['moderatorCtrl'].valueChanges.pipe(
      startWith(null),
      debounceTime(100),
      map((value) =>
        typeof value === 'string' ? value : value?.KorisnickoIme
      ),
      map((name) => (name ? this._filter(name) : this.korisnici.slice()))
    );
  }

  add(event: MatChipInputEvent) {
    const value = (event.value || '').trim();

    if (value) {
      this.moderatori.push(value);
    }

    event.chipInput!.clear(); // Clear input
    this.formGroup.controls['moderatorCtrl'].setValue(null); // Clear input
  }

  remove(moderator: string) {
    const index = this.moderatori.indexOf(moderator);

    if (index >= 0) {
      this.moderatori.splice(index, 1);
      this.moderatoriArray.splice(index, 1);
    }
  }

  displayFn(user: KorisniciModeratori): string {
    return user && user.KorisnickoIme ? user.KorisnickoIme : '';
  }

  selected(event: any) {
    this.moderatori.push(event.option.viewValue);
    this.moderatoriArray.push(event.option.value);
    this.moderatorInput.nativeElement.value = '';
    this.formGroup.controls['moderatorCtrl'].setValue(null);
  }

  private _filter(value: string): KorisniciModeratori[] {
    const filterValue = value.toLowerCase();

    return this.korisnici.filter((option) =>
      option.KorisnickoIme.toLowerCase().includes(filterValue)
    );
  }

  onSave(): void {
    this.formGroup.markAllAsTouched();
    if (this.formGroup.valid) {
      this.formGroup.controls['moderatorCtrl'].setValue(this.moderatoriArray);
      this.poduzeceService
        .insertUpdatePoduzeca(this.formGroup.value)
        .subscribe({
          next: (data: any) => {
            this.snackBar.open('Poduzeće uspješno spremljeno', 'U redu', {
              panelClass: 'green-snackbar',
            });
            this.dialogRef.close();
          },
          error: (error) => {
            this.snackBar.open(error.error.error.errstr, 'U redu', {
              panelClass: 'red-snackbar',
            });
          },
        });
    }
  }

  onOdustani(): void {
    this.dialogRef.close();
  }
}
