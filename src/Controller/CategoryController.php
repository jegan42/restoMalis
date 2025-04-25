<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/category', name: 'app_api_category_')]
final class CategoryController extends AbstractController
{
    private const ID_ROUTE = '/{id}';
    public function __construct(private EntityManagerInterface $manager, private CategoryRepository $repository)
    {
    }

    #[Route('/', name: 'new', methods: ['POST'])]
    public function new(): Response
    {
        $category = new Category();
        $category->setTitle('Dessert');
        $category->setCreatedAt(new DateTimeImmutable());
        // Tell Doctrine you want to (eventually) save the category (no queries yet)
        $this->manager->persist($category);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();
        return $this->json(
            ['message' => "Category resource created with {$category->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route(self::ID_ROUTE, name: 'show', methods: 'GET')]
    public function show(int $id): Response
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
        }
        return $this->json(
            ['message' => "A Category was found : {$category->getTitle()} for {$category->getId()} id"]
        );
    }

    #[Route(self::ID_ROUTE, name: 'edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
        }
        $category->setTitle('Dessert updated');
        $this->manager->flush();
        return $this->redirectToRoute('app_api_category_show', ['id' => $category->getId()]);
    }
    
    #[Route(self::ID_ROUTE, name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
        }
        $this->manager->remove($category);
        $this->manager->flush();
        return $this->json(
            ['message' => "Category resource deleted with {$id} id"],
            Response::HTTP_OK,
        );
    }
}
