<?php

namespace App\MessageHandler;

use App\Message\NewMessageNotification;
use App\Repository\MessageRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewMessageNotificationHandler
{
    private $messageRepository;
    private $mailer;

    public function __construct(MessageRepository $messageRepository, MailerInterface $mailer)
    {
        $this->messageRepository = $messageRepository;
        $this->mailer = $mailer;
    }

    public function __invoke(NewMessageNotification $notification)
    {
        $message = $this->messageRepository->find($notification->getMessageId());

        if (!$message) {
            return;
        }

        $email = (new Email())
            ->from('noreply@symfoconnect.com')
            ->to($message->getRecipient()->getEmail())
            ->subject('Nouveau message privé sur SymfoConnect')
            ->text(sprintf(
                "Bonjour %s,\n\nVous avez reçu un nouveau message de la part de @%s :\n\n%s\n\nConnectez-vous pour répondre !",
                $message->getRecipient()->getUsername(),
                $message->getSender()->getUsername(),
                $message->getContent()
            ));

        $this->mailer->send($email);
    }
}
