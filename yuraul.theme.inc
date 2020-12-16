<?php

/**
 * @file
 * This file contains functions for preprocess custom defined theme hooks.
 */

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_feedback_entity_add_form(&$variables) {
  $variables['form']['status']['#attributes']['class'][] = 'visually-hidden';
  $variables['form']['name']['widget']['#description'] = \Drupal\Core\Field\FieldFilteredMarkup::create(t('Only symbols and underscore. Up to 100.'));
  $variables['form']['name']['widget'][0]['#description'] = \Drupal\Core\Field\FieldFilteredMarkup::create(t('Only symbols and underscore. Up to 100.'));
}
