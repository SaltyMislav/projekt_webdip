import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DolazakNaPosaoComponent } from './dolazak-na-posao.component';

describe('DolazakNaPosaoComponent', () => {
  let component: DolazakNaPosaoComponent;
  let fixture: ComponentFixture<DolazakNaPosaoComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DolazakNaPosaoComponent]
    });
    fixture = TestBed.createComponent(DolazakNaPosaoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
