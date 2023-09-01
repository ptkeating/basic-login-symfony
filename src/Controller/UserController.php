<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\UserDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface
     * @param \Doctrine\ORM\EntityManagerInterface
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users', methods: ['POST'])]
    #[Route('/api/register', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] UserDto $userDto,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = new User();
        foreach (['full_name', 'email'] as $fieldName) {
            $user->{'set' . snake_to_studly_case($fieldName)}($userDto->{'get' . snake_to_studly_case($fieldName)}());
        }
        $user->setRawPassword($userDto->getPassword());
        $errors = $validator->validate($user, null, ['group' => 'registration']);
        if ($errors->count()) {
            return $this->json($errors, 422);
        }
        $user->setPassword($passwordHasher->hashPassword($user, $userDto->getPassword()));
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(['data' => $user]);
    }

    /**
     * @param \App\Entity\User $user - The user to be viewed 
     * @param \App\Entity\User $currentUser - The logged in user
     * 
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException;
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users/{id}', methods: ['GET'])]
    public function show(User $user, #[CurrentUser] User $currentUser): JsonResponse
    {
        if ($user->getId() !== $currentUser->getId()) {
            throw new AccessDeniedException();
        }
        return $this->json(['data' => $user]);
    }

    /**
     * @param \App\Entity\User $user - The user to be edited 
     * @param \App\Entity\User $currentUser - The logged in user
     * @param \Symfony\Component\HttpFoundation\Request
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface
     * @param \Doctrine\ORM\EntityManagerInterface
     * 
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users/{id}', methods: ['PUT', 'PATCH'])]
    public function edit(
        User $user,
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] UserDto $userDto,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if ($user->getId() !== $currentUser->getId()) {
            throw new AccessDeniedException();
        }
        /** @var \App\Repository\UserRepository */
        $userRepository = $entityManager->getRepository(User::class);
        foreach ($userRepository->getUpdatableColumns() as $fieldName) {
            if (!empty($userDto->{'get' . snake_to_studly_case($fieldName)}())) {
                $user->{'set' . snake_to_studly_case($fieldName)}($userDto->{'get' . snake_to_studly_case($fieldName)}());
            }
        }
        $errors = $validator->validate($user, null, ['group' => 'update_user']);
        if ($errors->count()) {
            return $this->json($errors, 422);
        }
        $entityManager->flush();
        return $this->json(['data' => $user]);
    }

    /**
     * Quick and dirty method to serialize - NOT USED
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $errors
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function serializeConstraintViolationList(ConstraintViolationListInterface $errors): JsonResponse
    {
        $returnArray = [];
        for ($i = 0; $i < $errors->count(); $i++) {
            $violation = $errors->get($i);
            $returnArray[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $this->json($returnArray, 422);
    }
}
