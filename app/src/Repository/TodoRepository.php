<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Todo>
 *
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    public function save(Todo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Todo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Todo[]
     */
    public function findAllOrderedByPriorityAndDate(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.completed', 'ASC')
            ->addOrderBy('t.priority', 'DESC')
            ->addOrderBy('t.dueDate', 'ASC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Todo[]
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.completed = :completed')
            ->setParameter('completed', false)
            ->orderBy('t.priority', 'DESC')
            ->addOrderBy('t.dueDate', 'ASC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Todo[]
     */
    public function findCompleted(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.completed = :completed')
            ->setParameter('completed', true)
            ->orderBy('t.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Todo[]
     */
    public function findOverdue(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.completed = :completed')
            ->andWhere('t.dueDate < :today')
            ->setParameter('completed', false)
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.completed = :completed')
            ->setParameter('completed', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCompleted(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.completed = :completed')
            ->setParameter('completed', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array{pending: int, completed: int, overdue: int, total: int}
     */
    public function getStatistics(): array
    {
        $pending = $this->countPending();
        $completed = $this->countCompleted();
        $overdue = count($this->findOverdue());

        return [
            'pending' => $pending,
            'completed' => $completed,
            'overdue' => $overdue,
            'total' => $pending + $completed,
        ];
    }
}
