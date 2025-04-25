<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'app_api_')]
final class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer) {}

    #[Route('/registration', name: 'registration', methods: 'POST')]
    #[
        OA\Post(
            path: '/api/registration',
            summary: 'Register a new user',
            requestBody: new OA\RequestBody(
                required: true,
                description: 'User registration data',
                content: [
                    new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                                new OA\Property(property: 'password', type: 'string', example: 'securepassword123'),
                            ]
                        )
                    )
                ]
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'User registered successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'user', type: 'string', example: 'user@example.com'),
                                    new OA\Property(property: 'apiToken', type: 'string', example: 'abc123xyzabc123xyzabc123xyz'),
                                    new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER')),
                                ]
                            )
                        )
                    ]
                ),
                // new OA\Response(
                //     response: 400,
                //     description: 'Invalid input data',
                //     content: [
                //         new OA\MediaType(
                //             mediaType: 'application/json',
                //             schema: new OA\Schema(
                //                 type: 'object',
                //                 properties: [
                //                     new OA\Property(property: 'message', type: 'string', example: 'Invalid input data'),
                //                 ]
                //             )
                //         )
                //     ]
                // ),
                // new OA\Response(
                //     response: 409,
                //     description: 'User already exists',
                //     content: [
                //         new OA\MediaType(
                //             mediaType: 'application/json',
                //             schema: new OA\Schema(
                //                 type: 'object',
                //                 properties: [
                //                     new OA\Property(property: 'message', type: 'string', example: 'User already exists'),
                //                 ]
                //             )
                //         )
                //     ]
                // )
            ]
        )
    ]

    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new DateTimeImmutable());
        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(
            ['user'  => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    #[
        OA\Post(
            path: '/api/login',
            summary: 'Login a user',
            requestBody: new OA\RequestBody(
                required: true,
                description: 'User login data',
                content: [
                    new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'username', type: 'string', example: 'user@example.com'),
                                new OA\Property(property: 'password', type: 'string', example: 'securepassword123'),
                            ]
                        )
                    )
                ]
            ),
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'User logged successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'user', type: 'string', example: 'user@example.com'),
                                    new OA\Property(property: 'apiToken', type: 'string', example: 'abc123xyzabc123xyzabc123xyz'),
                                    new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER')),
                                ]
                            )
                        )
                    ]
                ),
                // new OA\Response(
                //     response: 400,
                //     description: 'Invalid input data',
                //     content: [
                //         new OA\MediaType(
                //             mediaType: 'application/json',
                //             schema: new OA\Schema(
                //                 type: 'object',
                //                 properties: [
                //                     OA\new Property(property: 'message', type: 'string', example: 'Invalid input data'),
                //                 ]
                //             )
                //         )
                //     ]
                // ),
                // new OA\Response(
                //     response: 409,
                //     description: 'User already exists',
                //     content: [
                //         new OA\MediaType(
                //             mediaType: 'application/json',
                //             schema: new OA\Schema(
                //                 type: 'object',
                //                 properties: [
                //                     new OA\Property(property: 'message', type: 'string', example: 'User already exists'),
                //                 ]
                //             )
                //         )
                //     ]
                // )
            ]
        )
    ]


    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if ($user === null) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }
}
