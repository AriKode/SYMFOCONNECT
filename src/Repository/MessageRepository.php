<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retourne la liste des utilisateurs avec qui l'utilisateur actuel a une conversation
     */
    public function findConversations(User $user): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('DISTINCT u')
           ->from(User::class, 'u')
           ->where('u.id != :userId')
           ->andWhere($qb->expr()->orX(
               $qb->expr()->andX('m.sender = :user', 'm.recipient = u'),
               $qb->expr()->andX('m.sender = u', 'm.recipient = :user')
           ))
           ->setParameter('user', $user)
           ->setParameter('userId', $user->getId())
           ->orderBy('m.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Retourne l'historique des messages entre deux utilisateurs
     */
    public function findHistory(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where($this->getEntityManager()->getExpressionBuilder()->orX(
                $this->getEntityManager()->getExpressionBuilder()->andX('m.sender = :user1', 'm.recipient = :user2'),
                $this->getEntityManager()->getExpressionBuilder()->andX('m.sender = :user2', 'm.recipient = :user1')
            ))
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
