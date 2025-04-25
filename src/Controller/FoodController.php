<?php

namespace App\Controller;

use App\Entity\Food as EntityClass;
use App\Repository\FoodRepository as Repository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/food', name: 'app_api_food_')]
final class FoodController extends AbstractController
{
    private const ID_ROUTE = '/{id}';
    public function __construct(
        private EntityManagerInterface $manager,
        private Repository $repository,
        private UrlGeneratorInterface $urlGenerator,
        private SerializerInterface $serializer
    ) {}

    #[Route(name: 'new', methods: ['POST'])]
    #[
        OA\Post(
            path: '/api/food',
            summary: 'Register a new food',
            requestBody: new OA\RequestBody(
                required: true,
                description: 'Food registration data',
                content: [
                    new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'title', type: 'string', example: 'My food'),
                                new OA\Property(property: 'description', type: 'string', example: 'food description'),
                                new OA\Property(property: 'price', type: 'integer', example: '15'),
                            ]
                        )
                    )
                ]
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Food registered successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(type: 'string', example: 'Food resource created with 168 id'),
                        )
                    ]
                )
            ]
        )
    ]
    public function new(Request $request): JsonResponse
    {
        $entity = $this->serializer->deserialize($request->getContent(), EntityClass::class, 'json');
        $entity->setCreatedAt(new DateTimeImmutable());
        // Tell Doctrine you want to (eventually) save the food (no queries yet)
        $this->manager->persist($entity);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();

        $location = $this->urlGenerator->generate(
            'app_api_food_show',
            ['id' => $entity->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
        return new JsonResponse(['message' => "Food resource created with {$entity->getId()} id"], Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route(self::ID_ROUTE, name: 'show', methods: 'GET')]
    #[
        OA\Get(
            path: '/api/food/{id}',
            summary: 'Show food by id',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Food ID',
                    schema: new OA\Schema(type: 'integer')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Food found successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: '168'),
                                    new OA\Property(property: 'title', type: 'string', example: 'My food'),
                                    new OA\Property(property: 'description', type: 'string', example: 'food description'),
                                    new OA\Property(property: 'price', type: 'integer', example: '15'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2023-10-01T12:00:00Z'),
                                    new OA\Property(property: 'category', type: 'array', items: new OA\Items(type: 'string', example: 'Category 1')),
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
        $entity = $this->repository->findOneBy(['id' => $id]);
        if ($entity) {
            return new JsonResponse(
                $this->serializer->serialize($entity, 'json'),
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
            path: '/api/food/{id}',
            summary: 'Edit food by id',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Food ID',
                    schema: new OA\Schema(type: 'integer')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Food updated successfully'
                )
            ]
        )
    ]
    public function edit(int $id): Response
    {
        $entity = $this->repository->findOneBy(['id' => $id]);
        if (!$entity) {
            throw $this->createNotFoundException("No Food found for {$id} id");
        }
        $entity->setTitle('Fried Rice updated');
        $this->manager->flush();
        return $this->redirectToRoute('app_api_food_show', ['id' => $entity->getId()]);
    }

    #[Route(self::ID_ROUTE, name: 'delete', methods: ['DELETE'])]
    #[
        OA\Delete(
            path: '/api/food/{id}',
            summary: 'Delete food by id',
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
                    description: 'Food deleted successfully',
                    content: [
                        new OA\MediaType(
                            mediaType: 'application/json',
                            schema: new OA\Schema(type: 'string', example: 'Food resource deleted with 168 id'),
                        )
                    ]
                )
            ]
        )
    ]
    public function delete(int $id): Response
    {
        $entity = $this->repository->findOneBy(['id' => $id]);
        if (!$entity) {
            throw $this->createNotFoundException("No Food found for {$id} id");
        }
        $this->manager->remove($entity);
        $this->manager->flush();
        return $this->json(
            ['message' => "Food resource deleted with {$id} id"],
            Response::HTTP_OK,
        );
    }
}
