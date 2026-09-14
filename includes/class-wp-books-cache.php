<?php

defined('ABSPATH') || exit;

class WP_Books_Cache
{

  private const CACHE_KEY = 'wp_books_books';

  private const CACHE_DURATION = HOUR_IN_SECONDS;

  /**
   * Récupère les livres mis en cache.
   *
   * @return array|false
   */
  public function get()
  {
    return get_transient(self::CACHE_KEY);
  }

  /**
   * Enregistre les livres dans le cache.
   *
   * @param array $books Livres à mettre en cache.
   * @return bool
   */
  public function set(array $books)
  {
    return set_transient(
      self::CACHE_KEY,
      $books,
      self::CACHE_DURATION
    );
  }

  /**
   * Supprime le cache.
   *
   * @return bool
   */
  public function delete()
  {
    return delete_transient(self::CACHE_KEY);
  }

  /**
   * Retourne la durée du cache.
   *
   * @return int
   */
  public function get_duration()
  {
    return self::CACHE_DURATION;
  }

  /**
   * Retourne la date de dernière mise en cache.
   *
   * @return int|false
   */
  public function get_last_update()
  {
    $last_update = get_option('wp_books_last_update', false);

    return $last_update ? (int) $last_update : false;
  }

  /**
   * Enregistre la date de mise à jour.
   *
   * @return bool
   */
  public function set_last_update()
  {
    return update_option(
      'wp_books_last_update',
      time()
    );
  }

  /**
   * Supprime la date de mise à jour.
   *
   * @return bool
   */
  public function delete_last_update()
  {
    return delete_option('wp_books_last_update');
  }
}
