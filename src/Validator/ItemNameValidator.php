<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ItemNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ItemName */

        if (empty($value) || is_numeric($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
