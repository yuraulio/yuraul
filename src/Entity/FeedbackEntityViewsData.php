<?php

namespace Drupal\yuraul\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Feedback entities.
 */
class FeedbackEntityViewsData extends EntityViewsData {

  /**
   * Just get from parent.
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    return $data;
  }

}
