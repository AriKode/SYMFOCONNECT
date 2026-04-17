<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Message\NewMessageNotification;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_messages')]
    public function index(MessageRepository $messageRepository): Response
    {
        $conversations = $messageRepository->findConversations($this->getUser());

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/{username}', name: 'app_message_show')]
    public function show(
        User $recipient,
        MessageRepository $messageRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus
    ): Response {
        if ($recipient === $this->getUser()) {
            return $this->redirectToRoute('app_messages');
        }

        $messages = $messageRepository->findHistory($this->getUser(), $recipient);

        // Marquer comme lus
        foreach ($messages as $message) {
            if ($message->getRecipient() === $this->getUser() && !$message->isRead()) {
                $message->setIsRead(true);
            }
        }
        $entityManager->flush();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setRecipient($recipient);
            
            $entityManager->persist($message);
            $entityManager->flush();

            // Notification asynchrone
            $bus->dispatch(new NewMessageNotification($message->getId()));

            return $this->redirectToRoute('app_message_show', ['username' => $recipient->getUsername()]);
        }

        return $this->render('message/show.html.twig', [
            'recipient' => $recipient,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
}
