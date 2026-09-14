<?php

defined('ABSPATH') || exit;

class WP_Books_Renderer
{

  private WP_Books_API $api;

  /**
   * @param WP_Books_API $api Client API.
   */
  public function __construct(WP_Books_API $api)
  {
    $this->api = $api;
  }

  /**
   * Rend la liste des livres.
   *
   * @return string
   */
  public function render()
  {
    $books = $this->api->get_books();

    ob_start();

    include WP_BOOKS_DIR . 'templates/books-list.php';

    return ob_get_clean();
  }

  /**
   * Rend le shortcode [books_list].
   *
   * @return string
   */
  public function render_shortcode()
  {
    return $this->render();
  }
}
