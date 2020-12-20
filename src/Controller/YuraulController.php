<?php

namespace Drupal\yuraul\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Constructs a guestbook page.
 */
class YuraulController extends ControllerBase {

  /**
   * Build a guestbook main page.
   *
   * Add a form to add new posts and a list of existing feedbacks.
   *
   * @return mixed
   *   A render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function buildGuestbook() {
    // Adding form for sending post to the page.
    $entity = \Drupal::entityTypeManager()->getStorage('feedback_entity')->create();
    $page['form'] = \Drupal::service('entity.form_builder')->getForm($entity, 'add2');

    // Add a view with the feedback posts list.
    $page['view'] = [
      '#type' => 'view',
      '#name' => 'feedback_view',
      '#display_id' => 'default',
    ];

    return $page;
}

}
