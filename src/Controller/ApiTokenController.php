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
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class ApiTokenController extends AbstractController
{
    #[Route('/api/api_token', name: 'api_token_post')]
    public function tokenPost(EntityManagerInterface $entityManager, Request $request, LoggerInterface $logger,
                                 UserPasswordHasherInterface $hasher, TokenGenerator $tokenGenerator, SerializerInterface $serializer, ApiTokenManager $apiTokenManager): Response
    {
        try {
            $entityManager->beginTransaction();
            $data = json_decode($request->getContent(), true);
            if (!empty($data['email']) && !empty($data['password'])) {
                $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

                if ($user && $hasher->isPasswordValid($user, $data['password'])) {
                    $tokenData = $apiTokenManager->createApiToken($data, $tokenGenerator, $user);
                    $entityManager->commit();
                    $response = new Response($serializer->serialize($tokenData, 'json'));
                    $response->setStatusCode(200);
                } else {
                    $response = new Response( "Invalid credential");
                    $response->setStatusCode(422);
                }
            } else {
                $response = new Response( "'email' and 'password' are required field");
                $response->setStatusCode(422);
            }

        } catch (\Exception $exception) {
            $entityManager->rollback();
            $logger->error($exception->getMessage());
        }

        return $response;
    }
}