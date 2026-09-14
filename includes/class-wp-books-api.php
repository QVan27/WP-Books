<?php

defined('ABSPATH') || exit;

class WP_Books_API
{

  private const API_URL = 'https://gutendex.com/books/';

  private WP_Books_Cache $cache;

  /**
   * @param WP_Books_Cache $cache Gestionnaire de cache.
   */
  public function __construct(WP_Books_Cache $cache)
  {
    $this->cache = $cache;
  }

  /**
   * Récupère les livres.
   *
   * @return array|WP_Error
   */
  public function get_books()
  {
    $cached_books = $this->cache->get();

    if (false !== $cached_books) {
      return $cached_books;
    }

    $response = wp_remote_get(
      self::API_URL,
      array(
        'timeout' => 10,
        'headers' => array(
          'Accept' => 'application/json',
        ),
      )
    );

    if (is_wp_error($response)) {
      return new WP_Error(
        'wp_books_api_connection_error',
        __('Impossible de contacter l’API des livres.', 'wp-books')
      );
    }

    $status_code = wp_remote_retrieve_response_code($response);

    if (200 !== $status_code) {
      return new WP_Error(
        'wp_books_api_http_error',
        __('L’API des livres a retourné une réponse invalide.', 'wp-books')
      );
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (
      !is_array($data) ||
      !isset($data['results']) ||
      !is_array($data['results'])
    ) {
      return new WP_Error(
        'wp_books_api_invalid_response',
        __('Les données reçues depuis l’API sont invalides.', 'wp-books')
      );
    }

    $books = array();

    foreach (array_slice($data['results'], 0, 10) as $book) {
      $books[] = $this->normalize_book($book);
    }

    $this->cache->set($books);
    $this->cache->set_last_update();

    return $books;
  }

  /**
   * Actualise les données sans utiliser le cache existant.
   *
   * @return array|WP_Error
   */
  public function refresh_books()
  {
    $this->cache->delete();

    return $this->get_books();
  }

  /**
   * Transforme une réponse Gutendex en structure interne.
   *
   * @param array $book Données brutes.
   * @return array
   */
  private function normalize_book(array $book)
  {
    $authors = array();

    if (!empty($book['authors']) && is_array($book['authors'])) {
      foreach ($book['authors'] as $author) {
        if (!empty($author['name'])) {
          $authors[] = sanitize_text_field($author['name']);
        }
      }
    }

    $cover_url = '';

    if (!empty($book['formats']['image/jpeg'])) {
      $cover_url = esc_url_raw($book['formats']['image/jpeg']);
    }

    $book_url = '';

    if (!empty($book['formats']['text/html'])) {
      $book_url = esc_url_raw($book['formats']['text/html']);
    } elseif (!empty($book['formats']['text/html; charset=utf-8'])) {
      $book_url = esc_url_raw($book['formats']['text/html; charset=utf-8']);
    }

    return array(
      'id' => !empty($book['id']) ? absint($book['id']) : 0,
      'title' => !empty($book['title'])
        ? sanitize_text_field($book['title'])
        : __('Titre inconnu', 'wp-books'),
      'authors' => $authors,
      'languages' => !empty($book['languages']) && is_array($book['languages'])
        ? array_map('sanitize_text_field', $book['languages'])
        : array(),
      'download_count' => isset($book['download_count'])
        ? absint($book['download_count'])
        : 0,
      'cover_url' => $cover_url,
      'book_url' => $book_url,
    );
  }
}
