<?php

namespace Drupal\forum_one_login\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\forum_one_login\Event\LoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoginSubscriber.
 *
 * @package Drupal\forum_one_login\EventSubscriber
 */
class LoginSubscriber implements EventSubscriberInterface {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * LoginSubscriber constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   Database connection object.
   */
  public function __construct(Connection $database, \Drupal\Core\Datetime\DateFormatterInterface $date_formatter) {
    $this->database = $database;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      LoginEvent::EVENT_NAME => 'user_login_action',
    ];
  }

  /**
   * Process the event dispatched.
   *
   * @param \Drupal\forum_one_login\Event\LoginEvent $event
   */
  public function user_login_action(LoginEvent $event) {

      $user_query = $this->database->select('users_field_data', 'ufd');
      $user_query->addField('ufd', 'login');
      $user_query->addField('ufd', 'name');
      $user_query->addField('ufd', 'timezone');
      $user_query->condition('ufd.uid', $event->account->id());
      $user_result = $user_query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      $name = $user_result[0]['name'];
      $last_login = $user_result[0]['login'];
      $timezone = $user_result[0]['timezone'];

      if(empty($last_login)) {
        $last_login = time();
      }
      else {
        $last_login = $this->dateFormatter->format($last_login, 'forum_one_login', '', $timezone);
      }

      if(!empty($user_result)) {
        \Drupal::service('session')->set('forum_one_last_login', $last_login);
        \Drupal::service('session')->set('forum_one_username', $name);
      }
  }

}