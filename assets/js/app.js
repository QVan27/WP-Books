import BooksList from './BooksList.js'

window.addEventListener('load', () => {
  const booksLists = document.querySelectorAll('.wp-books')

  booksLists.forEach((element) => { new BooksList(element) })
})