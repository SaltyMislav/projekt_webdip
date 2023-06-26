import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NatjecajComponent } from './natjecaj.component';

describe('NatjecajComponent', () => {
  let component: NatjecajComponent;
  let fixture: ComponentFixture<NatjecajComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [NatjecajComponent]
    });
    fixture = TestBed.createComponent(NatjecajComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
