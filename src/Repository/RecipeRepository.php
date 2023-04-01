<?php

namespace App\Repository;

use App\Data\SearchRecipe;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function add(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMostPopularRecipes(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.likes', 'DESC')
            ->addOrderBy('r.createdAt', 'DESC')
            ->addOrderBy('r.recipeScore', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findSearch(SearchRecipe $searchRecipe): array
    {
        $query = $this->createQueryBuilder('r');

        if (!empty($searchRecipe->name)) {
            $query = $query
                ->andWhere('r.name LIKE :name')
                ->setParameter('name', "%{$searchRecipe->name}%");
        }

        if (!empty($searchRecipe->difficulty)) {
            $query = $query
                ->andWhere('r.difficulty = :difficulty')
                ->setParameter('difficulty', $searchRecipe->difficulty);
        }

        if (!empty($searchRecipe->recipeCost)) {
            $query = $query
                ->andWhere('r.recipeCost = :recipeCost')
                ->setParameter('recipeCost', $searchRecipe->recipeCost);
        }

        if (!empty($searchRecipe->thematic)) {
            $query = $query
                ->andWhere('r.thematic = :thematic')
                ->setParameter('thematic', $searchRecipe->thematic);
        }

        if (!empty($searchRecipe->category)) {
            $query = $query
                ->andWhere('r.category = :category')
                ->setParameter('category', $searchRecipe->category);
        }

        return $query->getQuery()->getResult();
    }
}
