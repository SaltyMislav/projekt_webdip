import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Subject, Subscription, takeUntil } from 'rxjs';
import { KonfiguracijaClass } from '../services/class/konfiguracijaclass.service';
import { KonfiguracijaService } from '../services/konfiguracija.service';

@Component({
  selector: 'app-konfiguracija',
  templateUrl: './konfiguracija.component.html',
  styleUrls: ['./konfiguracija.component.css'],
})
export class KonfiguracijaComponent implements OnInit, OnDestroy {
  pomak!: number;
  stranicenje!: number;

  private konfiguracijaDataSubscription!: Subscription;
  notifier = new Subject<any>();

  form!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private konfiguracijaService: KonfiguracijaService,
    private konfiguracijaClass: KonfiguracijaClass,
    private cdref: ChangeDetectorRef,
    private snackBar: MatSnackBar
  ) {
    this.konfiguracijaDataSubscription = this.konfiguracijaClass.konfiguracijaDataSubject
      .pipe(takeUntil(this.notifier))
      .subscribe((data) => {
        this.pomak = data.pomak;
        this.stranicenje = data.stranicenje;

        this.form.patchValue({
          pomak: this.pomak,
          stranicenje: this.stranicenje,
        });

        this.cdref.detectChanges();
      });
  }

  ngOnInit(): void {
    this.konfiguracijaClass.getData();
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

  ngOnDestroy(): void {
    this.notifier.next(null);
    this.notifier.complete();
    this.konfiguracijaDataSubscription.unsubscribe();
  }
}
