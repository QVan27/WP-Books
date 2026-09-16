<?php

defined('ABSPATH') || exit;

class WP_Books_Admin
{

  private WP_Books_API $api;
  private WP_Books_Cache $cache;

  private const OPTION_TITLE = 'wp_books_title';
  private const OPTION_INTRO = 'wp_books_intro';

  /**
   * @param WP_Books_API   $api   Client API.
   * @param WP_Books_Cache $cache Gestionnaire de cache.
   */
  public function __construct(WP_Books_API $api, WP_Books_Cache $cache)
  {
    $this->api = $api;
    $this->cache = $cache;

    add_action('admin_menu', array($this, 'register_menu'));
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_post_wp_books_refresh', array($this, 'refresh_books'));
    add_action('admin_post_wp_books_clear_cache', array($this, 'clear_cache'));
  }

  /**
   * Enregistre les réglages du plugin.
   *
   * @return void
   */
  public function register_settings()
  {
    register_setting(
      'wp_books_settings',
      self::OPTION_TITLE,
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Sélection de livres',
      )
    );

    register_setting(
      'wp_books_settings',
      self::OPTION_INTRO,
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default' => 'Découvrez une sélection de livres provenant du Projet Gutenberg.',
      )
    );
  }

  /**
   * Ajoute la page d’administration.
   *
   * @return void
   */
  public function register_menu()
  {
    add_options_page(
      __('WP Books', 'wp-books'),
      __('WP Books', 'wp-books'),
      'manage_options',
      'wp-books',
      array($this, 'render_page')
    );
  }

  /**
   * Traite l’actualisation des livres.
   *
   * @return void
   */
  public function refresh_books()
  {
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('Vous n’avez pas les permissions nécessaires.', 'wp-books'));
    }

    check_admin_referer('wp_books_refresh');

    $result = $this->api->refresh_books();

    $status = is_wp_error($result) ? 'error' : 'success';

    wp_safe_redirect(
      add_query_arg(
        array(
          'page' => 'wp-books',
          'wp_books_status' => $status,
        ),
        admin_url('options-general.php')
      )
    );

    exit;
  }

  /**
   * Traite la suppression du cache.
   *
   * @return void
   */
  public function clear_cache()
  {
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('Vous n’avez pas les permissions nécessaires.', 'wp-books'));
    }

    check_admin_referer('wp_books_clear_cache');

    $this->cache->delete();
    $this->cache->delete_last_update();

    wp_safe_redirect(
      add_query_arg(
        array(
          'page' => 'wp-books',
          'wp_books_status' => 'cleared',
        ),
        admin_url('options-general.php')
      )
    );

    exit;
  }

  /**
   * Affiche la page d’administration.
   *
   * @return void
   */
  public function render_page()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    $last_update = $this->cache->get_last_update();
    $status = isset($_GET['wp_books_status'])
      ? sanitize_key(wp_unslash($_GET['wp_books_status']))
      : '';

    $title = get_option(self::OPTION_TITLE, 'Sélection de livres');
    $intro = get_option(self::OPTION_INTRO, 'Découvrez une sélection de livres provenant du Projet Gutenberg.');
?>

    <div class="wrap">
      <h1>
        <?php echo esc_html__('WP Books', 'wp-books'); ?>
      </h1>

      <?php if ('success' === $status) : ?>
        <div class="notice notice-success is-dismissible">
          <p>
            <?php echo esc_html__('Les livres ont été actualisés.', 'wp-books'); ?>
          </p>
        </div>
      <?php elseif ('error' === $status) : ?>
        <div class="notice notice-error is-dismissible">
          <p>
            <?php echo esc_html__('La récupération des livres a échoué.', 'wp-books'); ?>
          </p>
        </div>
      <?php elseif ('cleared' === $status) : ?>
        <div class="notice notice-success is-dismissible">
          <p>
            <?php echo esc_html__('Le cache a été supprimé.', 'wp-books'); ?>
          </p>
        </div>
      <?php endif; ?>

      <h2>
        <?php echo esc_html__('Textes affichés sur le site', 'wp-books'); ?>
      </h2>

      <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
        <?php settings_fields('wp_books_settings'); ?>

        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">
              <label for="wp_books_title">
                <?php echo esc_html__('Titre', 'wp-books'); ?>
              </label>
            </th>

            <td>
              <input
                type="text"
                id="wp_books_title"
                name="wp_books_title"
                value="<?php echo esc_attr($title); ?>"
                class="regular-text">
            </td>
          </tr>

          <tr>
            <th scope="row">
              <label for="wp_books_intro">
                <?php echo esc_html__('Introduction', 'wp-books'); ?>
              </label>
            </th>

            <td>
              <textarea
                id="wp_books_intro"
                name="wp_books_intro"
                rows="4"
                class="large-text"><?php echo esc_textarea($intro); ?></textarea>
            </td>
          </tr>
        </table>

        <?php submit_button(__('Enregistrer les textes', 'wp-books')); ?>
      </form>

      <hr>

      <h2>
        <?php echo esc_html__('Gestion des livres', 'wp-books'); ?>
      </h2>

      <p>
        <strong>
          <?php echo esc_html__('Dernière récupération :', 'wp-books'); ?>
        </strong>

        <?php if ($last_update) : ?>
          <?php
          echo esc_html(
            wp_date(
              get_option('date_format') . ' ' . get_option('time_format'),
              $last_update
            )
          );
          ?>
        <?php else : ?>
          <?php echo esc_html__('Aucune récupération effectuée.', 'wp-books'); ?>
        <?php endif; ?>
      </p>

      <p>
        <?php
        echo esc_html__(
          'Le cache est conservé pendant une heure.',
          'wp-books'
        );
        ?>
      </p>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="wp_books_refresh">

        <?php wp_nonce_field('wp_books_refresh'); ?>

        <?php submit_button(
          __('Actualiser les livres', 'wp-books'),
          'primary',
          'submit',
          false
        ); ?>
      </form>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="wp_books_clear_cache">

        <?php wp_nonce_field('wp_books_clear_cache'); ?>

        <?php submit_button(
          __('Vider le cache', 'wp-books'),
          'secondary',
          'submit',
          false
        ); ?>
      </form>
    </div>

<?php
  }
}
