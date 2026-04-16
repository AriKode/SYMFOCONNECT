<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SocialController extends AbstractController
{
    #[Route('/follow/{username}', name: 'app_follow')]
    #[IsGranted('ROLE_USER')]
    public function follow(string $username, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $userToFollow = $userRepository->findOneBy(['username' => $username]);
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$userToFollow) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        if ($userToFollow === $currentUser) {
            $this->addFlash('danger', 'Vous ne pouvez pas vous suivre vous-même.');
            return $this->redirectToRoute('app_profile', ['username' => $username]);
        }

        if ($currentUser->isFollowing($userToFollow)) {
            $currentUser->unfollow($userToFollow);
            $this->addFlash('info', "Vous ne suivez plus $username.");
        } else {
            $currentUser->follow($userToFollow);
            
            // Create notification
            $notification = new Notification();
            $notification->setRecipient($userToFollow);
            $notification->setType('follow');
            $notification->setContent($currentUser->getUsername() . ' a commencé à vous suivre !');
            $entityManager->persist($notification);

            $this->addFlash('success', "Vous suivez maintenant $username !");
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_profile', ['username' => $username]);
    }

    #[Route('/like/{id}', name: 'app_post_like')]
    #[IsGranted('ROLE_USER')]
    public function like(Post $post, EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($post->isLikedByUser($currentUser)) {
            $post->removeLike($currentUser);
        } else {
            $post->addLike($currentUser);
        }

        $entityManager->flush();

        return $this->redirect($this->generateUrl('app_home') . '#post-' . $post->getId());
    }

    #[Route('/feed', name: 'app_feed')]
    #[IsGranted('ROLE_USER')]
    public function feed(PostRepository $postRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $following = $currentUser->getFollowing();

        if ($following->isEmpty()) {
            return $this->render('social/feed.html.twig', [
                'posts' => [],
                'message' => 'Vous ne suivez personne pour le moment. Découvrez des profils pour remplir votre fil !'
            ]);
        }

        // Get posts from users followed by the current user
        $posts = $postRepository->createQueryBuilder('p')
            ->where('p.author IN (:following)')
            ->setParameter('following', $following)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('social/feed.html.twig', [
            'posts' => $posts,
        ]);
    }
}
