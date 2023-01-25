<?php
namespace Wintex\SimpleApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/{entity}', name: 'generic_get', methods: ["GET"])]
    public function getAll(string $entityClass, ManagerRegistry $doctrine) : JsonResponse
    {
        $repository = $doctrine->getRepository($entityClass);

        $entities = $repository->findAll();

        return $this->json($entities);
    }

    #[Route('/{entity}/{id}', name: 'generic_get_one', methods: ["GET"])]
    public function getOne(string $entityClass, string|int $id, ManagerRegistry $doctrine) : JsonResponse
    {
        $repository = $doctrine->getRepository($entityClass);

        $entity = $repository->find($id);

        if ($entity == null)
            throw new HttpException(404, "Entity with id {$id} not found!");

        return $this->json($entity);
    }

    #[Route('/{entity}', name: 'generic_create', methods: ["POST"])]
    public function create(string $entity, string $entityClass, Request $request, EntityManagerInterface $entityManager) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hydrator = new ArrayHydrator($entityManager);
        $newEntity = $hydrator->hydrate($entityClass, $data);

        $entityManager->persist($newEntity);
        $entityManager->flush();

        return $this->json(["message" => "Successfully created {$entity} with id " . $newEntity->getId()]);
    }

    #[Route('/{entity}/{id}', name: 'generic_delete', methods: ["DELETE"])]
    public function delete(string $entityClass, string $entity, int|string $id, ManagerRegistry $doctrine) : JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository($entityClass);

        $entityToDelete = $repository->find($id);

        $entityManager->remove($entityToDelete);
        $entityManager->flush();

        return $this->json(["message" => "Successfully deleted {$entity} with id " . $id]);
    }

    #[Route('/{entity}/{repoMethod}', name: "repository_method", requirements: ["repoMethod" => "@.+"], methods: ["GET"], priority: 100)]
    public function repositoryMethod(string $entityClass, string $repoMethod, ManagerRegistry $doctrine, Request $request) : JsonResponse
    {
        $manager = $doctrine->getManager();
        $repository = $doctrine->getRepository($entityClass);
        $repoMethod = ltrim($repoMethod, '@');

        try {
            $result = $repository->$repoMethod($request->query->all());
        } catch (Exception $ex) {
            throw new HttpException(400, $ex->getMessage());
        }

        return $this->json($result);
    }
}