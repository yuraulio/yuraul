<?php

namespace Drupal\yuraul\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting Feedback entities.
 *
 * @ingroup yuraul
 */
class FeedbackEntityDeleteForm extends ContentEntityDeleteForm {

  /**
   * Delete entity and attached images.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $this->getEntity();
    $message = $this->getDeletionMessage();

    // Make sure that deleting a translation does not delete the whole entity.
    if (!$entity->isDefaultTranslation()) {
      $untranslated_entity = $entity->getUntranslated();
      $untranslated_entity->removeTranslation($entity->language()->getId());
      $untranslated_entity->save();
      $form_state->setRedirectUrl($untranslated_entity->toUrl('canonical'));
    }
    else {
      // Delete avatar if not default.
      $id = $entity->avatar->target_id;
      if (!empty($id)) {
        $avatar = \Drupal::entityTypeManager()
          ->getStorage('file')
          ->load($id);
        \Drupal::service('file.usage')
          ->delete($avatar, 'yuraul', 'entity', $entity->id(), 0);
        $avatar->delete();
      }

      // Delete attached picture if exist.
      $id = $entity->picture->target_id;
      if (!empty($id)) {
        $picture = \Drupal::entityTypeManager()
          ->getStorage('file')
          ->load($id);
        \Drupal::service('file.usage')
          ->delete($picture, 'yuraul', 'entity', $entity->id(), 0);
        $picture->delete();
      }

      // Delete entity at last.
      $entity->delete();
      $form_state->setRedirectUrl($this->getRedirectUrl());
    }

    $this->messenger()->addStatus($message);
    $this->logDeletionMessage();
  }

}
