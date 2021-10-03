<?php

namespace Drupal\forum_one_login\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for the Forum One Login module.
 */
class ForumOneConfigForm extends ConfigFormBase {
  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * ForumOneConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'forum_one_login_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'forum_one_login.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('forum_one_login.settings');

    $form['forum_one_admin_message'] = [
        '#type' => 'textfield',
        '#title' => 'Administrator Message',
        '#description' => 'A custom message that will displayed under the user info',
        '#default_value' => $config->get('forum_one_admin_message')
      ];

    $form['forum_one_anon_user'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Message for anon users?'),
        '#description' => t('If this is checked, the above administrator message will also be displayed for anonymous users i.e. not logged in users 
        <br />If left unchecked, the message will not be displayed for anonymous users'),
        '#weight' => 2,
        '#default_value' => $config->get('forum_one_anon_user')
    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Retrieve the configuration.
    $this->configFactory()->getEditable('forum_one_login.settings')

      // Set the submitted configuration settings.
      ->set('forum_one_admin_message', $form_state->getValue('forum_one_admin_message'))
      ->set('forum_one_anon_user', $form_state->getValue('forum_one_anon_user'))
      ->save();

      drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }
}