import { Component, OnInit } from '@angular/core';
import { PrijavaEnforcehttpsService } from '../services/prijavaEnforcehttps.service';

@Component({
  selector: 'app-prijava',
  templateUrl: './prijava.component.html',
  styleUrls: ['./prijava.component.css']
})
export class PrijavaComponent implements OnInit{

  constructor(private enforceHttps: PrijavaEnforcehttpsService) { }

  ngOnInit(): void {
    this.enforceHttps.canActivate();
  }
}
