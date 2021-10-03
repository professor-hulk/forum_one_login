<?php

namespace Drupal\forum_one_login\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * This block provides info about the user login information
 *
 * @Block(
 * id = "forum_one_login",
 * label = @Translation("Displays user info"),
 * admin_label = @Translation("Displays user info"),
 * category = @Translation("Forum One Login"),
 * module = "forum_one_login",
 * )
 */
class ForumOneLogin extends BlockBase implements ContainerFactoryPluginInterface {

    protected $session;
    
    protected $account;

  /**
   * {@inheritdoc}
   *
   * @param array $configuration
   *   Provides configuration array.
   * @param string $plugin_id
   *   Plugin id.
   * @param object $plugin_definition
   *   Plugin defination.
   * @param Drupal\Core\Session\AccountProxyInterface $account
   *   The user account variable.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Session $session, AccountProxyInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->session = $session;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user_id = $this->account->id();

    $message_for_anon_users = \Drupal::config('forum_one_login.settings')->get('forum_one_anon_user');
    $admin_message = \Drupal::config('forum_one_login.settings')->get('forum_one_admin_message');

    if ($user_id > 0) {

        $data['#markup'] = '<div>' 
        . $this->t("Hello ") . $this->session->get('forum_one_username') . '<br />'
        . $this->t("Your last log in was ") . $this->session->get('forum_one_last_login') . '<br />'
        . "<a href='/user/$user_id'>Visit your profile</a>" . '<br />'
        . $admin_message
        . '</div>';

        $data['#cache']['max-age'] = 0;

        return $data;
    }
    else if($message_for_anon_users && !empty($admin_message)) {
        $data['#markup'] = "<div>$admin_message</div>";
        return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('session'),
            $container->get('current_user')
        );
    }

  /**
   * {@inheritdoc}
   */
    public function getCacheMaxAge() {
        return 0;
    }

}
