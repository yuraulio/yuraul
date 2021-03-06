<?php

namespace Drupal\yuraul\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ValidPhoneNumber constraint.
 */
class ValidPhoneNumberValidator extends ConstraintValidator {

  /**
   * Gets result of validation and set violation of needed.
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      if (!$this->isValidPhoneNumber($item->value)) {
        $this->context->addViolation($constraint->notValid, ['%value' => $item->value]);
      }
    }
  }

  /**
   * Check the value.
   *
   * Should be a telephone number in an international format starting with a +.
   *
   * @param string $value
   *   Value to check.
   *
   * @return bool
   *   TRUE if value is valid, FALSE instead.
   */
  private function isValidPhoneNumber($value) {
    $match = preg_match('/^\+\d{1,4}\d{8,15}$/s', $value);
    if ($match === 0) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

}
