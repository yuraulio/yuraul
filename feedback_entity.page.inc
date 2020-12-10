<?php

/**
 * @file
 * Contains feedback_entity.page.inc.
 *
 * Page callback for Feedback entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Feedback templates.
 *
 * Default template: feedback_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_feedback_entity(array &$variables) {
  // Fetch FeedbackEntity Entity Object.
  $feedback_entity = $variables['elements']['#feedback_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
