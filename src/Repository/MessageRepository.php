<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
// name of the method should be understandable
    /**
     * @param string $status
     * @return mixed
     */
    public function findByStatus(string $status): mixed
    {
        $qb = $this->createQueryBuilder('m');

// usage of sprintf method leads to SQL injection issue
            $qb
                ->where('m.status = :status')
                ->setParameter('status', $status);

        return $qb->getQuery()->execute();
    }
}
