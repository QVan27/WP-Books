export default class BooksList {
  constructor(element) {
    this.element = element

    this.getElems()
    this.bindMethods()
    this.events()
  }

  bindMethods() {
    this.onSearch = this.onSearch.bind(this)
    this.onViewChange = this.onViewChange.bind(this)
  }

  getElems() {
    this.$input = this.element.querySelector('.wp-books__search-input')
    this.$list = this.element.querySelector('.wp-books__list')
    this.$items = this.element.querySelectorAll('.wp-books__item')
    this.$emptyMessage = this.element.querySelector('.wp-books__search-empty')
    this.$viewButtons = this.element.querySelectorAll('.wp-books__view-button')
  }

  events() {
    if (this.$input) this.$input.addEventListener('input', this.onSearch)

    this.$viewButtons.forEach((button) => {
      button.addEventListener('click', this.onViewChange)
    })
  }

  onSearch(event) {
    const search = event.target.value.trim().toLowerCase()
    let visibleItems = 0

    this.$items.forEach((item) => {
      const title = item.querySelector('.wp-books__book-title')

      if (!title) return

      const match = title.textContent.trim().toLowerCase().includes(search)

      item.hidden = !match

      if (match) visibleItems++
    })

    if (this.$emptyMessage) this.$emptyMessage.hidden = visibleItems > 0
  }

  onViewChange(event) {
    const button = event.currentTarget
    const view = button.dataset.view

    if (!view || !this.$list) return

    const currentView = this.$list.classList.contains('wp-books__list--list') ? 'list' : 'grid'

    if (view === currentView) return

    this.$viewButtons.forEach((viewButton) => {
      const isActive = viewButton === button

      viewButton.classList.toggle('is-active', isActive)
      viewButton.setAttribute('aria-pressed', isActive ? 'true' : 'false')
    })

    gsap.to(this.$list, {
      autoAlpha: 0,
      scale: 0.98,
      duration: 0.25,
      ease: 'power2.inOut',
      onComplete: () => {
        this.$list.classList.remove('wp-books__list--grid', 'wp-books__list--list')

        this.$list.classList.add(`wp-books__list--${view}`)

        gsap.to(this.$list, {
          autoAlpha: 1,
          scale: 1,
          duration: 0.35,
          ease: 'power2.out'
        })
      }
    })
  }
}