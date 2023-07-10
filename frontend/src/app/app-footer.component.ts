import { Component } from '@angular/core';

interface PrikupljanjePodataka {
  value: string;
  viewValue: string;
}

@Component({
  selector: 'app-footer',
  templateUrl: './app-footer.component.html',
  styles: [
  ]
})
export class AppFooterComponent {
  prikupljanjePodataka: PrikupljanjePodataka[] = [
    {value: 'ne-0', viewValue: 'Bez Prikupljanja'},
    {value: 'da-1', viewValue: 'Osnovno'},
    {value: 'da-2', viewValue: 'Sve'},
  ];
}
