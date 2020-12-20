<?php

namespace Drupal\yuraul\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if the value can be a valid username.
 *
 * @Constraint(
 *   id = "ValidName",
 *   label = @Translation("Valid username", context = "Validation"),
 *   type = "string"
 * )
 */
class ValidName extends Constraint {

  /**
   * The message that will be shown if the value is not valid.
   *
   * @var string
   *   An error message.
   */
  public $notValid = '%value is not valid username. Check it and try again';

}
