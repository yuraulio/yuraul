<?php
/**
 * @file
 * Contains \Drupal\yuraul\Entity\Feedback.
 */

namespace Drupal\yuraul\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the Feedback entity.
 *
 * @ingroup yuraul
 *
 * @ContentEntityType(
 *   id = "yuraul_feedback",
 *   label = @Translation("Feedback entity"),
 *   base_table = "yuraul_feedback",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */

class Feedback extends ContentEntityBase implements ContentEntityInterface {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Feedback entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Feedback entity.'))
      ->setReadOnly(TRUE);

    return $fields;
  }
}
