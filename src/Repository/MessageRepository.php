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

    public function findConversations(User $user): array
    {
        // On récupère tous les messages où l'utilisateur est soit expéditeur soit destinataire
        $messages = $this->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $users = [];
        foreach ($messages as $message) {
            $otherUser = ($message->getSender() === $user) ? $message->getRecipient() : $message->getSender();
            if (!isset($users[$otherUser->getId()])) {
                $users[$otherUser->getId()] = $otherUser;
            }
        }

        return array_values($users);
    }

    /**
     * Retourne l'historique des messages entre deux utilisateurs
     */
    public function findHistory(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.recipient = :user2) OR (m.sender = :user2 AND m.recipient = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur
     */
    public function countUnreadMessages(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.recipient = :user')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
