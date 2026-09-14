<?php

defined('ABSPATH') || exit;

/**
 * @var array|WP_Error $books
 */
?>

<section class="wp-books" aria-labelledby="wp-books-title">
  <div class="wp-books__header">
    <h2 id="wp-books-title" class="wp-books__title">
      <?php echo esc_html__('Sélection de livres', 'wp-books'); ?>
    </h2>

    <p class="wp-books__intro">
      <?php
      echo esc_html__(
        'Découvrez une sélection de livres provenant du Projet Gutenberg.',
        'wp-books'
      );
      ?>
    </p>
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

    <ul class="wp-books__list">
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

  <?php endif; ?>
</section>