<?php

/**
 * @file
 * This file contains functions for preprocess custom defined theme hooks.
 */

/**
 * Implements template_preprocess_HOOK().
 *
 * Hide fields anonymous user not allowed to change.
 *
 * @param $variables
 *   The variables array (modify in place).
 */
function template_preprocess_feedback_entity_add2_form(&$variables) {
  $variables['form']['status']['#attributes']['class'][] = 'visually-hidden';
  $variables['form']['created']['#attributes']['class'][] = 'visually-hidden';
}
