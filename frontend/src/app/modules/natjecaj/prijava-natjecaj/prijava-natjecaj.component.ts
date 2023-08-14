import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-prijava-natjecaj',
  templateUrl: './prijava-natjecaj.component.html',
  styleUrls: ['./prijava-natjecaj.component.css']
})
export class PrijavaNatjecajComponent {
  
    constructor(
      public dialogPrijavljeni: MatDialogRef<PrijavaNatjecajComponent>,
      @Inject(MAT_DIALOG_DATA) public data: any
    ) { 
      console.log(data);
    }
}
