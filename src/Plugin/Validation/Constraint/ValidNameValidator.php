<?php

namespace Drupal\yuraul\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ValidName constraint.
 */
class ValidNameValidator extends ConstraintValidator {

  /**
   * Gets result of validation and set violation of needed.
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      if (!$this->isValidUsername($item->value)) {
        $this->context->addViolation($constraint->notValid, ['%value' => $item->value]);
      }
    }
  }

  /**
   * Check the value.
   *
   * Can contains only ASCII letters, numbers and underscore and should not
   * start with an underscore or number. From 2 up to 100 symbols.
   *
   * @param string $value
   *   Value to check.
   *
   * @return bool
   *   TRUE if value is valid, FALSE instead.
   */
  private function isValidUsername($value) {
    $match = preg_match('/^[a-zA-Z][a-zA-Z_0-9 ]{0,98}[a-zA-Z0-9]$/s', $value);
    if ($match === 0) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

}
