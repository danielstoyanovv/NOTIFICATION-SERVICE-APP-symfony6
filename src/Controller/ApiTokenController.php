<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ApiToken;
use App\Entity\User;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\TokenGenerator;
use App\Service\ApiTokenManager;

#[AsController]
class ApiTokenController extends AbstractController
{
    #[Route('/api/api_token', name: 'api_token_post')]
    public function tokenPost(
        EntityManagerInterface $entityManager,
        Request $request,
        LoggerInterface $logger,
        UserPasswordHasherInterface $hasher,
        TokenGenerator $tokenGenerator,
        ApiTokenManager $apiTokenManager
    ): Response {
        try {
            if ($request->getMethod() === 'POST') {
                $entityManager->beginTransaction();
                $data = json_decode($request->getContent(), true);
                if (!empty($data['email']) && !empty($data['password'])) {
                    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

                    if ($user && $hasher->isPasswordValid($user, $data['password'])) {
                        if (!empty($user->getApiToken()) && $user->getApiToken()->hasExpired() === false) {
                            return $this->json($user->getApiToken());
                        } else {
                            $tokenData = $apiTokenManager->createApiToken($data, $tokenGenerator, $user);
                            $entityManager->commit();
                            return $this->json($tokenData, Response::HTTP_CREATED);
                        }
                    } else {
                        return $this->json("Invalid credential", Response::HTTP_UNAUTHORIZED);
                    }
                } else {
                    return $this->json("'email' and 'password' are required field", Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        } catch (\Exception $exception) {
            $entityManager->rollback();
            $logger->error($exception->getMessage());
        }

        return $this->json('Invalid credentials', Response::HTTP_FORBIDDEN);
    }
}
