export default class BooksList {
  constructor(element) {
    this.element = element
    this.perPage = 10
    this.currentPage = 1
    this.search = ''

    this.getElems()
    this.bindMethods()
    this.events()
    this.render()
  }

  bindMethods() {
    this.onSearch = this.onSearch.bind(this)
    this.onLanguageChange = this.onLanguageChange.bind(this)
    this.onViewChange = this.onViewChange.bind(this)
    this.onPageChange = this.onPageChange.bind(this)
  }

  getElems() {
    this.$input = this.element.querySelector('.wp-books__search-input')
    this.$languageSelect = this.element.querySelector('.wp-books__language-select')
    this.$list = this.element.querySelector('.wp-books__list')
    this.$items = [...this.element.querySelectorAll('.wp-books__item')]
    this.$emptyMessage = this.element.querySelector('.wp-books__search-empty')
    this.$pagination = this.element.querySelector('.wp-books__pagination')
    this.$viewButtons = this.element.querySelectorAll('.wp-books__view-button')
  }

  events() {
    if (this.$input) this.$input.addEventListener('input', this.onSearch)
    if (this.$languageSelect) this.$languageSelect.addEventListener('change', this.onLanguageChange)

    this.$viewButtons.forEach((button) => {
      button.addEventListener('click', this.onViewChange)
    })

    if (this.$pagination) this.$pagination.addEventListener('click', this.onPageChange)
  }

  onSearch(event) {
    this.search = event.target.value.trim().toLowerCase()
    this.currentPage = 1

    this.render()
    this.scrollToList()
  }

  onLanguageChange(event) {
    this.language = event.target.value

    this.currentPage = 1

    this.render()
    this.scrollToList()
  }

  onPageChange(event) {
    const button = event.target.closest('[data-page]')

    if (!button) return

    event.preventDefault()

    const page = Number(button.dataset.page)

    if (!page || page === this.currentPage) return

    this.currentPage = page

    this.render()
    this.scrollToList()
  }

  getFilteredItems() {
    return this.$items.filter((item) => {
      const title = item.querySelector('.wp-books__book-title')

      if (!title) return false

      const titleMatch = !this.search || title.textContent.trim().toLowerCase().includes(this.search)
      const languageMatch = !this.language || item.dataset.languages?.split(',').includes(this.language)

      return titleMatch && languageMatch
    })
  }

  render() {
    const filteredItems = this.getFilteredItems()
    const totalPages = Math.max(1, Math.ceil(filteredItems.length / this.perPage))

    this.currentPage = Math.min(this.currentPage, totalPages)

    const start = (this.currentPage - 1) * this.perPage
    const end = start + this.perPage

    const visibleItems = filteredItems.slice(start, end)

    this.$items.forEach((item) => {
      item.hidden = !visibleItems.includes(item)
    })

    if (this.$emptyMessage) this.$emptyMessage.hidden = filteredItems.length > 0

    this.renderPagination(totalPages)
  }

  renderPagination(totalPages) {
    if (!this.$pagination) return

    if (totalPages <= 1) {
      this.$pagination.innerHTML = ''
      this.$pagination.hidden = true

      return
    }

    this.$pagination.hidden = false

    let html = ''

    for (let page = 1; page <= totalPages; page++) {
      const isActive = page === this.currentPage

      html += `
        <a
          class="wp-books__pagination-button${isActive ? ' is-active' : ''}"
          href="#"
          data-page="${page}"
          ${isActive ? 'aria-current="page"' : ''}>
          ${page}
        </a>
      `
    }

    this.$pagination.innerHTML = html
  }

  scrollToList() {
    if (!this.$list) return

    const top = this.$list.getBoundingClientRect().top + window.scrollY - 100

    window.scrollTo({
      top,
      behavior: 'smooth'
    })
  }

  onViewChange(event) {
    const button = event.currentTarget
    const view = button.dataset.view

    if (!view) return

    const currentView = this.element.dataset.view || 'grid'

    if (view === currentView) return

    if (!this.$list) return

    this.element.dataset.view = view

    this.$viewButtons.forEach((viewButton) => {
      const isActive = viewButton === button

      viewButton.classList.toggle('is-active', isActive)
      viewButton.setAttribute('aria-pressed', isActive ? 'true' : 'false')
    })

    this.scrollToList()

    gsap.to(this.$list, {
      autoAlpha: 0,
      scale: 0.98,
      duration: 0.3,
      ease: 'power2.inOut',
      onComplete: () => {
        this.$list.classList.remove('wp-books__list--grid', 'wp-books__list--list')

        this.$list.classList.add(`wp-books__list--${view}`)

        gsap.fromTo(this.$list, {
          autoAlpha: 0,
          scale: 0.98
        }, {
          autoAlpha: 1,
          scale: 1,
          duration: 0.4,
          ease: 'power2.out'
        })
      }
    })
  }
}