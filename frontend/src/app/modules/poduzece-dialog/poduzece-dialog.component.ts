import {COMMA, ENTER} from '@angular/cdk/keycodes';
import { Component, ElementRef, Inject, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { PoduzeceService } from '../services/poduzece.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable } from 'rxjs';
import { MatChipInputEvent } from '@angular/material/chips';

@Component({
  selector: 'app-poduzece-dialog',
  templateUrl: './poduzece-dialog.component.html',
  styleUrls: ['./poduzece-dialog.component.css'],
})
export class PoduzeceDialogComponent implements OnInit {
  formGroup!: FormGroup;
  separatorKeysCodes: number[] = [ENTER, COMMA];
  korisnici!: Observable<string[]>;
  moderatori: string[] = [];

  @ViewChild('moderatorInput') moderatorInput!: ElementRef<HTMLInputElement>;

  constructor(
    private fb: FormBuilder,
    private poduzeceService: PoduzeceService,
    private snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<PoduzeceDialogComponent>,
    @Inject(MAT_DIALOG_DATA) protected data: any
  ) {}

  ngOnInit(): void {
    this.formGroup = this.fb.group({
      ID: [this.data?.ID],
      Naziv: [this.data?.Naziv, Validators.required],
      Opis: [this.data?.Opis, Validators.required],
      RadnoVrijemeOd: [this.data?.RadnoVrijemeOd, Validators.required],
      RadnoVrijemeDo: [this.data?.RadnoVrijemeDo, Validators.required],
      moderatorCtrl: [this.data?.Moderatori],
    });

    console.log(this.formGroup.controls['moderatorCtrl'].value);

    this.data?.Moderatori.forEach((element: { KorisnickoIme: string; }) => {
      this.moderatori.push(element.KorisnickoIme);
    });
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
    }
  }

  selected(event: any) {
    this.moderatori.push(event.option.viewValue);
    this.moderatorInput.nativeElement.value = '';
    this.formGroup.controls['moderatorCtrl'].setValue(null);
  }

  onSave(): void {
    this.formGroup.markAllAsTouched();
    if (this.formGroup.valid) {
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

  onDelete(): void {
    this.poduzeceService.deletePoduzece(this.formGroup.value.ID).subscribe({
      next: (data: any) => {
        this.snackBar.open('Poduzeće uspješno obrisano', 'U redu', {
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

  onOdustani(): void {
    this.dialogRef.close();
  }
}
