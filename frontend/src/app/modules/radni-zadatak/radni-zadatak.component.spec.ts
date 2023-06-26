import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RadniZadatakComponent } from './radni-zadatak.component';

describe('RadniZadatakComponent', () => {
  let component: RadniZadatakComponent;
  let fixture: ComponentFixture<RadniZadatakComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [RadniZadatakComponent]
    });
    fixture = TestBed.createComponent(RadniZadatakComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
