<?php

namespace Drupal\yuraul\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Feedback entities.
 */
class FeedbackEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
