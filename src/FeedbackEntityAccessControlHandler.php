<?php

namespace Drupal\yuraul;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Feedback entity.
 *
 * @see \Drupal\yuraul\Entity\FeedbackEntity.
 */
class FeedbackEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\yuraul\Entity\FeedbackEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished feedback entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published feedback entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit feedback entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete feedback entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add feedback entities');
  }


}
