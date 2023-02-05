<?php
namespace Wintex\SimpleApiBundle\Controller;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    #[Route('/{entity}', name: 'generic_get', methods: ["GET"])]
    public function getAll(string $entityClass, Request $request) : array
    {
        $repository = $this->em->getRepository($entityClass);

        $entities = $repository->findAll();

        return $entities;
    }

    #[Route('/{entity}/{id}', name: 'generic_get_one', methods: ["GET"])]
    public function getOne(string $entityClass, string|int $id) : object
    {
        $repository = $this->em->getRepository($entityClass);

        $entity = $repository->find($id);

        if ($entity == null)
            throw new HttpException(404, "Entity with id {$id} not found!");

        return $entity;
    }

    #[Route('/{entity}', name: 'generic_create', methods: ["POST"])]
    public function create(string $entity, string $entityClass, Request $request) : array
    {
        $data = json_decode($request->getContent(), true);
        $hydrator = new ArrayHydrator($this->em);
        $newEntity = $hydrator->hydrate($entityClass, $data);

        $this->em->persist($newEntity);
        $this->em->flush();


        return ["message" => "Successfully created {$entity} with id " . $newEntity->getId()];
    }

    #[Route('/{entity}/{id}', name: 'generic_delete', methods: ["DELETE"], requirements: ["id" => '\d+'])]
    public function delete(string $entityClass, string $entity, int|string $id) : array
    {
        $repository = $this->em->getRepository($entityClass);

        $entityToDelete = $repository->find($id);

        if ($entity == null)
            throw new HttpException(404, "Entity with id {$id} not found!");

        $this->em->remove($entityToDelete);
        $this->em->flush();

        return ["message" => "Successfully deleted {$entity} with id " . $id];
    }

    #[Route('/{entity}/{method}', name: "repository_method", methods: ["GET"], requirements: ["method" => '\D+'], priority: 2)]
    public function repositoryMethod(string $entityClass, string $method, Request $request) : array | object
    {
        $repository = $this->em->getRepository($entityClass);

        
        try {
            $callable = $this->getParameter('wintex_simple_api.entity_definitions')[$entityClass]['routes'][$method]['repository_method'];
            $result = $repository->$callable($request->query->all());
        } catch (Exception $ex) {
            throw new HttpException(400, "Problem occured while calling method {{$method}}");
        }

        return $result;
    }
}