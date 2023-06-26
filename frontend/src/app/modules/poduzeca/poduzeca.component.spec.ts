import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PoduzecaComponent } from './poduzeca.component';

describe('PoduzecaComponent', () => {
  let component: PoduzecaComponent;
  let fixture: ComponentFixture<PoduzecaComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [PoduzecaComponent]
    });
    fixture = TestBed.createComponent(PoduzecaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
