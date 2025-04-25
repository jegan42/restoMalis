<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/account', name: 'app_api_account_')]
final class AccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserRepository $repository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(int $id): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            $responseData = $this->serializer->serialize($user, 'json');
            return new JsonResponse(
                $responseData,
                Response::HTTP_CREATED,
                [],
                true
            );
        }
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND,
        );
    }

    #[Route('/edit', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            $user = $this->serializer->deserialize($request->getContent(), user::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
            $user->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();
            return new JsonResponse(
                null,
                Response::HTTP_NO_CONTENT,
            );
        }
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND,
        );
    }
}
