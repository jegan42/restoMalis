<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/restaurant', name: 'app_api_restaurant_')]
final class RestaurantController extends AbstractController
{
    private const ID_ROUTE = '/{id}';
    public function __construct(
        private EntityManagerInterface $manager,
        private RestaurantRepository $repository,
        private UrlGeneratorInterface $urlGenerator,
        private SerializerInterface $serializer
    ) {}

    #[Route(name: 'new', methods: ['POST'])]
    #[
        OA\Post(
            path: '/api/restaurant',
            summary: 'Register a new restaurant',
            requestBody: new OA\RequestBody(
                required: true,
                description: 'Restaurant registration data',
                content: [
                    new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            type: 'object',
                            required: ['name', 'description', 'amOpenningTime', 'pmOpenningTime', 'maxGuest'],
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'My Restaurant'),
                                new OA\Property(property: 'description', type: 'string', example: 'A nice place'),
                                new OA\Property(property: 'amOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '12:00')),
                                new OA\Property(property: 'pmOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '18:00')),
                                new OA\Property(property: 'maxGuest', type: 'integer', example: 50),
                            ]
                        )
                    )
                ]
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Restaurant registered successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: '168'),
                                    new OA\Property(property: 'name', type: 'string', example: 'My Restaurant'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Restaurant description'),
                                    new OA\Property(property: 'amOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '12:00')),
                                    new OA\Property(property: 'pmOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '18:00')),
                                    new OA\Property(property: 'maxGuest', type: 'integer', example: '6'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-01-01T00:00:00+00:00'),
                                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2025-01-01T00:00:00+00:00'),
                                    new OA\Property(property: 'pictures', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'picture 1'))),
                                    new OA\Property(property: 'menus', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'menu 1'))),
                                    new OA\Property(property: 'bookings', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'booking 1'))),
                                ]
                            )
                        )
                    ]
                )
            ]
        )
    ]
    public function new(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
    
            // Sécuriser l'initialisation des champs vides
            $data['pictures'] ??= [];
            $data['menus'] ??= [];
            $data['bookings'] ??= [];

            $restaurant = $this->serializer->deserialize(json_encode($data), Restaurant::class, 'json');

            // sans securisation des champs vides
            // $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json');
            $now = new \DateTimeImmutable();
            $restaurant->setAmOpeningTime($data['amOpeningTime']);
            $restaurant->setPmOpeningTime($data['pmOpeningTime']);
            $restaurant->setCreatedAt($now);
            $restaurant->setUpdatedAt($now);
    
            $this->manager->persist($restaurant);
            $this->manager->flush();
    
            $responseData = $this->serializer->serialize($restaurant, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_restaurant_show',
                ['id' => $restaurant->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
    
            return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => 'An error occurred during user registration',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[
        OA\Get(
            path: '/api/restaurant/{id}',
            summary: 'Show restaurant by id',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Restaurant ID',
                    schema: new OA\Schema(type: 'integer')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Restaurant found successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: '168'),
                                    new OA\Property(property: 'name', type: 'string', example: 'My Restaurant'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Restaurant description'),
                                    new OA\Property(property: 'amOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '12:00')),
                                    new OA\Property(property: 'pmOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '18:00')),
                                    new OA\Property(property: 'maxGuest', type: 'integer', example: '6'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'pictures', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'picture 1'))),
                                    new OA\Property(property: 'menus', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'menu 1'))),
                                    new OA\Property(property: 'bookings', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'booking 1'))),
                                ]
                            )
                        )
                    ]
                ),
                new OA\Response(
                    response: 404,
                    description: 'Restaurant not found',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'message', type: 'string', example: 'Restaurant not found'),
                                ]
                            )
                        )
                    ]
                )
            ]
        )
    ]
    public function show(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $responseData = $this->serializer->serialize($restaurant, 'json');
            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND,
        );
    }


    #[Route(self::ID_ROUTE, name: 'edit', methods: ['PUT'])]
    #[
        OA\Put(
            path: '/api/restaurant/{id}',
            summary: 'Edit restaurant by id',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Restaurant ID',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: '168'),
                            new OA\Property(property: 'name', type: 'string', example: 'My Restaurant'),
                            new OA\Property(property: 'description', type: 'string', example: 'Restaurant description'),
                            new OA\Property(property: 'amOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '12:00')),
                            new OA\Property(property: 'pmOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '18:00')),
                            new OA\Property(property: 'maxGuest', type: 'integer', example: '6'),
                            new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                        ]
                    )
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Restaurant updated successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: '168'),
                                    new OA\Property(property: 'name', type: 'string', example: 'My Restaurant'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Restaurant description'),
                                    new OA\Property(property: 'amOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '12:00')),
                                    new OA\Property(property: 'pmOpenningTime', type: 'array', items: new OA\Items(type: 'string', example: '18:00')),
                                    new OA\Property(property: 'maxGuest', type: 'integer', example: '6'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'pictures', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'picture 1'))),
                                    new OA\Property(property: 'menus', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'menu 1'))),
                                    new OA\Property(property: 'bookings', type: 'array', items: new OA\Items(type: 'array', items: new OA\Items(type: 'string', example: 'booking 1'))),
                                ]
                            )
                        )
                    ]
                ),
                new OA\Response(
                    response: 404,
                    description: 'Restaurant not found',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'message', type: 'string', example: 'Restaurant not found'),
                                ]
                            )
                        )
                    ]
                )
            ]
        )
    ]
    public function edit(int $id, Request $request): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]);
            $restaurant->setUpdatedAt(new DateTimeImmutable());
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

    #[Route(self::ID_ROUTE, name: 'delete', methods: ['DELETE'])]
    #[
        OA\Delete(
            path: '/api/restaurant/{id}',
            summary: 'Delete restaurant by id',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Restaurant ID',
                    schema: new OA\Schema(type: 'integer')
                )
            ],
            responses: [
                new OA\Response(
                    response: 204,
                    description: 'Restaurant deleted successfully',
                ),
                new OA\Response(
                    response: 404,
                    description: 'Restaurant not found',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'message', type: 'string', example: 'Restaurant not found'),
                                ]
                            )
                        )
                    ]
                )
            ]
        )
    ]
    public function delete(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $this->manager->remove($restaurant);
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