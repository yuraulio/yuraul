<?php

namespace Drupal\yuraul\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Feedback edit forms.
 *
 * @ingroup yuraul
 */
class FeedbackEntityAddForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * Some dependency injection.
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * Builds the form for the guestbook's main page.
   *
   * @param array $form
   *   The complete form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   A render array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\yuraul\Entity\FeedbackEntity $entity */
    // Get the standard Drupal entity add form.
    $form = parent::buildForm($form, $form_state);

    // Change the text on submit button.
    $form['actions']['submit']['#value'] = $this->t('Send feedback');

    // Div element to show messages into.
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];

    // AJAX element to make this form hadled via AJAX.
    $form['actions']['submit']['#ajax'] = [
      'callback' => '::ajaxSubmit',
      'event' => 'click',
      'progress' => [
        'type' => 'throbber',
      ],
    ];

    return $form;
  }

  /**
   * Saving a new entity, shows message and reloads the page.
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    $this->messenger()->addMessage($this->t('Thank you for your feedback!'));
    $form_state->setRedirect('yuraul.feedback');
  }

  /**
   * AJAX submit callback.
   *
   * Shows validation messages if any.
   *
   * @param array $form
   *   The complete form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    // If there are no validation errors sending response with redirect
    // to feedback page.
    if (!$form_state->hasAnyErrors()) {
      $url = Url::fromRoute('yuraul.feedback')->toString();
      $ajax_response->addCommand(new RedirectCommand($url));
    }
    // Else sending response with rendered errors to show it in form.
    else {
      $message = [
        '#theme' => 'status_messages',
        '#message_list' => \Drupal::messenger()->all(),
        '#status_headings' => [
          'status' => t('Status message'),
          'error' => t('Error message'),
          'warning' => t('Warning message'),
        ],
        '#marckup' => time(),
      ];
      \Drupal::messenger()->deleteAll();
      $messages = \Drupal::service('renderer')->render($message);
      $ajax_response->addCommand(new HtmlCommand('#form-system-messages', $messages));
    }

    return $ajax_response;
  }

}
