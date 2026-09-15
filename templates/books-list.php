<?php

defined('ABSPATH') || exit;

/**
 * @var array|WP_Error $books
 * @var string $title
 * @var string $intro
 * @var string $title_id
 * @var string $search_id
 */
?>

<section class="wp-books" aria-labelledby="<?php echo esc_attr($title_id); ?>">
  <div class="wp-books__header">
    <?php if ($title !== '') : ?>
      <h2
        id="<?php echo esc_attr($title_id); ?>"
        class="wp-books__title">
        <?php echo esc_html($title); ?>
      </h2>
    <?php endif; ?>
    <?php if ($intro !== '') : ?>
      <p class="wp-books__intro">
        <?php echo esc_html($intro); ?>
      </p>
    <?php endif; ?>
  </div>
  <?php if (is_wp_error($books)) : ?>
    <p class="wp-books__message wp-books__message--error" role="alert">
      <?php echo esc_html($books->get_error_message()); ?>
    </p>
  <?php elseif (empty($books)) : ?>
    <p class="wp-books__message">
      <?php echo esc_html__('Aucun livre n’est disponible pour le moment.', 'wp-books'); ?>
    </p>
  <?php else : ?>
    <div class="wp-books__controls">
      <div
        class="wp-books__view-switcher"
        role="group"
        aria-label="<?php echo esc_attr__('Affichage des livres', 'wp-books'); ?>">
        <button
          class="wp-books__view-button is-active"
          type="button"
          data-view="grid"
          aria-pressed="true">
          <?php echo esc_html__('Grille', 'wp-books'); ?>
        </button>
        <button
          class="wp-books__view-button"
          type="button"
          data-view="list"
          aria-pressed="false">
          <?php echo esc_html__('Liste', 'wp-books'); ?>
        </button>
      </div>
      <div class="wp-books__search">
        <label
          class="wp-books__search-label"
          for="<?php echo esc_attr($search_id); ?>">
          <?php echo esc_html__('Rechercher un livre', 'wp-books'); ?>
        </label>
        <input
          class="wp-books__search-input"
          type="search"
          id="<?php echo esc_attr($search_id); ?>"
          placeholder="<?php echo esc_attr__('Rechercher par titre...', 'wp-books'); ?>"
          autocomplete="off">
      </div>
    </div>
    <p
      class="wp-books__search-empty"
      hidden
      role="status">
      <?php echo esc_html__('Aucun livre ne correspond à votre recherche.', 'wp-books'); ?>
    </p>
    <ul class="wp-books__list wp-books__list--grid">
      <?php foreach ($books as $book) : ?>
        <li class="wp-books__item">
          <article class="wp-books__book">
            <?php if (!empty($book['cover_url'])) : ?>
              <div class="wp-books__cover">
                <img
                  src="<?php echo esc_url($book['cover_url']); ?>"
                  alt="<?php echo esc_attr($book['title']); ?>"
                  loading="lazy">
              </div>
            <?php endif; ?>
            <div class="wp-books__content">
              <h3 class="wp-books__book-title">
                <?php echo esc_html($book['title']); ?>
              </h3>
              <dl class="wp-books__metadata">
                <?php if (!empty($book['authors'])) : ?>
                  <div class="wp-books__metadata-row">
                    <dt>
                      <?php echo esc_html__('Auteur(s)', 'wp-books'); ?>
                    </dt>

                    <dd>
                      <?php echo esc_html(implode(', ', $book['authors'])); ?>
                    </dd>
                  </div>
                <?php endif; ?>
                <?php if (!empty($book['languages'])) : ?>
                  <div class="wp-books__metadata-row">
                    <dt>
                      <?php echo esc_html__('Langue(s)', 'wp-books'); ?>
                    </dt>

                    <dd>
                      <?php echo esc_html(implode(', ', $book['languages'])); ?>
                    </dd>
                  </div>
                <?php endif; ?>
                <div class="wp-books__metadata-row">
                  <dt>
                    <?php echo esc_html__('Téléchargements', 'wp-books'); ?>
                  </dt>

                  <dd>
                    <?php echo esc_html(number_format_i18n($book['download_count'])); ?>
                  </dd>
                </div>
              </dl>
              <?php if (!empty($book['book_url'])) : ?>
                <a
                  class="wp-books__link"
                  href="<?php echo esc_url($book['book_url']); ?>"
                  target="_blank"
                  rel="noopener noreferrer">
                  <?php echo esc_html__('Lire le livre', 'wp-books'); ?>
                </a>
              <?php endif; ?>
            </div>
          </article>
        </li>
      <?php endforeach; ?>
    </ul>
    <nav class="wp-books__pagination" aria-label="<?php echo esc_attr__('Pagination', 'wp-books'); ?>"></nav>
  <?php endif; ?>
</section>