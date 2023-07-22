import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

interface PrikupljanjePodataka {
  value: string;
  viewValue: string;
}

@Component({
  selector: 'app-footer',
  templateUrl: './app-footer.component.html',
  styles: [],
})
export class AppFooterComponent implements OnInit {
  prikupljanjePodataka: PrikupljanjePodataka[] = [
    { value: 'noCollection', viewValue: 'Bez Prikupljanja' },
    { value: 'BasicCollection', viewValue: 'Osnovno' },
    { value: 'EverythingCollection', viewValue: 'Sve' },
  ];

  selected = '';

  constructor(
    private cookieService: CookieService,
    private cdref: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    if (this.cookieService.check('prikupljanjePodataka')) {
      switch (this.cookieService.get('prikupljanjePodataka')) {
        case 'noCollection':
          this.selected = 'noCollection';
          break;
        case 'BasicCollection':
          this.selected = 'BasicCollection';
          break;
        case 'EverythingCollection':
          this.selected = 'EverythingCollection';
          break;
        default:
          break;
      }
    }
  }

  onPrikupljanjePodatakaChange(event: any) {
    switch (event.value) {
      case 'noCollection':
        if (this.cookieService.check('prikupljanjePodataka'))
          this.cookieService.delete('prikupljanjePodataka');
        this.cdref.detectChanges();
        break;
      case 'BasicCollection':
        this.cookieService.set('prikupljanjePodataka', 'BasicCollection', 2);
        this.cdref.detectChanges();
        break;
      case 'EverythingCollection':
        this.cookieService.set(
          'prikupljanjePodataka',
          'EverythingCollection',
          2
        );
        this.cdref.detectChanges();
        break;
      default:
        break;
    }
  }
}
