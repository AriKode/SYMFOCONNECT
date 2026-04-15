<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository, \Doctrine\ORM\EntityManagerInterface $entityManager, \App\Repository\UserRepository $userRepository): Response
    {
        // Initializer for evaluation/demo purposes if DB is empty
        if ($userRepository->count([]) === 0) {
            $user = new \App\Entity\User();
            $user->setEmail('demo@symfoconnect.fr');
            $user->setUsername('Arikode');
            $user->setPassword('password');
            $user->setBio('Passionné de Symfony et de réseaux sociaux ! 🚀');
            $entityManager->persist($user);

            $post = new \App\Entity\Post();
            $post->setContent('Bienvenue sur SymfoConnect ! C\'est le début d\'une grande aventure. #Symfony7');
            $post->setAuthor($user);
            $entityManager->persist($post);
            
            $entityManager->flush();
        }

        $posts = $postRepository->findLatest(10);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
