<?php

namespace App\Validator\Constraint;

use App\Enum\ReferentialTypeEnum;
use App\Validator\ReferentialTypeValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ReferentialTypeConstraint extends Constraint
{
    public function __construct(
        private readonly ReferentialTypeEnum $match,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return ReferentialTypeValidator::class;
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function getMatch(): ReferentialTypeEnum
    {
        return $this->match;
    }
}
