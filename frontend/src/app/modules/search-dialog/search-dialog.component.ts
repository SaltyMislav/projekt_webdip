import { Component } from '@angular/core';

@Component({
  selector: 'app-search-dialog',
  templateUrl: './search-dialog.component.html',
  styleUrls: ['./search-dialog.component.css']
})
export class SearchDialogComponent {
searchResults: any[] = [];
searchText: string = '';

search(searchText: string) {
  console.log('searchText', this.searchText);
}
}
