<?php

defined('ABSPATH') || exit;

class WP_Books_Block
{

  private WP_Books_Renderer $renderer;

  /**
   * @param WP_Books_Renderer $renderer Renderer du bloc.
   */
  public function __construct(WP_Books_Renderer $renderer)
  {
    $this->renderer = $renderer;

    add_action('init', array($this, 'register_block'));
  }

  /**
   * Enregistre le bloc Gutenberg.
   *
   * @return void
   */
  public function register_block()
  {
    register_block_type(
      WP_BOOKS_DIR . 'blocks/books-list',
      array(
        'render_callback' => array($this->renderer, 'render'),
      )
    );
  }
}
