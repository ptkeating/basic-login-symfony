<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'The email {{ email }} has already been registered.';

    #[HasNamedArguments]
    public function __construct(
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
