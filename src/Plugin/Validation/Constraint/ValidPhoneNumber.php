<?php

namespace Drupal\yuraul\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if the value can be a valid phone number.
 *
 * @Constraint(
 *   id = "ValidPhoneNumber",
 *   label = @Translation("Valid phone number", context = "Validation"),
 *   type = "string"
 * )
 */
class ValidPhoneNumber extends Constraint {

  // The message that will be shown if the value is not valid.
  public $notValid = '%value is not valid phone number. Check it and try again';

}
