<?php

namespace Drupal\forum_one_login\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\user\UserInterface;

/**
 * This will be fired when a user logs in.
 */
class LoginEvent extends Event {

  const EVENT_NAME = 'forum_one_login_event';

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  public $account;

  /**
   * Constructs the object.
   *
   * @param \Drupal\user\UserInterface $account
   *   The account of the user logged in. The account object will be available to all subscribers of this event.
   */
  public function __construct(UserInterface $account) {
    $this->account = $account;
  }

}