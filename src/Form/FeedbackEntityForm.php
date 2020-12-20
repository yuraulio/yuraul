<?php

namespace Drupal\yuraul\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Feedback edit forms.
 *
 * @ingroup yuraul
 */
class FeedbackEntityForm extends ContentEntityForm {

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
   * Building a standard edit form.
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
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * Saves entity after editing and redirect to the entity page.
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    // This form used by administrator to create and edit entity.
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
    $form_state->setRedirect('entity.feedback_entity.canonical', ['feedback_entity' => $entity->id()]);
  }

}
