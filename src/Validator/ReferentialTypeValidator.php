<?php

namespace App\Validator;

use App\Entity\Referential;
use App\Validator\Constraint\ReferentialTypeConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ReferentialTypeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReferentialTypeConstraint) {
            throw new UnexpectedValueException($constraint, ReferentialTypeConstraint::class);
        }

        if (!\is_object($value)) {
            throw new UnexpectedValueException($value, 'object');
        }

        if (\is_a($value, Referential::class)) {
            $this->validateReferential($value, $constraint);
        } elseif (\is_a($value, ArrayCollection::class) && !$value->isEmpty()) {
            $this->validateReferentialCollection($value, $constraint);
        }
    }

    /**
     * @param ArrayCollection<int, Referential> $collection
     */
    public function validateReferentialCollection(ArrayCollection $collection, ReferentialTypeConstraint $constraint): void
    {
        if (!$collection->filter(fn (Referential $r) => $constraint->getMatch() !== $r->type)->isEmpty()) {
            $this->context
                ->buildViolation(\sprintf('Impossible to add to this list a reference whose type is not "%s".', $constraint->getMatch()->value))
                ->addViolation();
        }
    }

    public function validateReferential(Referential $referential, ReferentialTypeConstraint $constraint): void
    {
        if ($constraint->getMatch() !== $referential->type) {
            $this->context
                ->buildViolation(\sprintf('Impossible to fill a reference whose type is not "%s".', $constraint->getMatch()->value))
                ->addViolation();
        }
    }
}
