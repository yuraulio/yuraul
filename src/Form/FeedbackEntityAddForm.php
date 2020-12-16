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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\yuraul\Entity\FeedbackEntity $entity */
    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit']['#value'] = $this->t('Send feedback');
    // Div element to show messages into.
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];
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
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Feedback.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Feedback.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('yuraul.feedback');
  }

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
