import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { KonfiguracijaService } from '../services/konfiguracija.service';

@Component({
  selector: 'app-konfiguracija',
  templateUrl: './konfiguracija.component.html',
  styleUrls: ['./konfiguracija.component.css'],
})
export class KonfiguracijaComponent implements OnInit {
  pomak!: number;
  stranicenje!: number;

  form!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private konfiguracijaService: KonfiguracijaService,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef,
    private snackBar: MatSnackBar
  ) {
    this.pomak = this.konfiguracijaClass.pomak;
    this.stranicenje = this.konfiguracijaClass.stranicenje;
  }

  ngOnInit(): void {
    this.form = this.fb.group({
      pomak: [{value: this.pomak, disabled: true}],
      stranicenje: [this.stranicenje, Validators.min(1)],
    });
  }

  dohvatPomaka(): void {
    this.konfiguracijaService.dohvatPomaka().subscribe({
      next: (data: any) => {
        this.snackBar.open('Uspješno postavljen novi pomak!', 'Zatvori', {
          panelClass: 'green-snackbar',
        });

        this.form.patchValue({
          pomak: data,
        });
        this.cdref.detectChanges();
      },
      error: (err: any) => {
        this.snackBar.open('Problem kod dohvata pomaka vremena', 'Zatvori', {
          panelClass: 'red-snackbar',
        });
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.snackBar.open('Forma nije validna', 'Zatvori', {
        panelClass: 'red-snackbar',
      });
      return;
    }

    this.konfiguracijaService.postaviStranicenje(this.form.value).subscribe({
      next: (data: any) => {
        this.snackBar.open('Uspješno postavljeno novo straničenje!', 'Zatvori', {
          panelClass: 'green-snackbar',
        });

        this.konfiguracijaClass.getData();
      },
      error: (err: any) => {
        this.snackBar.open(err.error.error.errstr, 'Zatvori', {
          panelClass: 'red-snackbar',
        });
      },
    });
  }
}
