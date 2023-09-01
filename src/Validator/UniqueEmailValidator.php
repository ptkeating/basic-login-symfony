<?php

namespace App\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{
    /**
     * @param \Doctrine\ORM\EntityManagerInterface - the container's entity manager
     * 
     */
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }
        /** @var \App\Repository\UserRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        if (!empty($userRepository->findOneBy(['email' => $value]))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
        }
    }
}
