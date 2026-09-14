export default class BooksList {
  constructor(element) {
    this.element = element
    this.input = this.element.querySelector('.wp-books__search-input')
    this.items = this.element.querySelectorAll('.wp-books__item')
    this.emptyMessage = this.element.querySelector('.wp-books__search-empty')

    if (!this.input) return

    this.onSearch = this.onSearch.bind(this)

    this.events()
  }

  events() {
    this.input.addEventListener('input', this.onSearch)
  }

  onSearch(event) {
    const search = event.target.value.trim().toLowerCase()
    let visibleItems = 0

    this.items.forEach((item) => {
      const title = item.querySelector('.wp-books__book-title')

      if (!title) return

      const match = title.textContent.trim().toLowerCase().includes(search)

      item.hidden = !match

      if (match) visibleItems++
    })

    if (this.emptyMessage) this.emptyMessage.hidden = visibleItems > 0
  }
}