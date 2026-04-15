<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Day 1 workaround: Get the first user or create one since auth is Day 2
            $author = $userRepository->findOneBy([]);
            
            if (!$author) {
                $author = new User();
                $author->setEmail('admin@symfoconnect.fr');
                $author->setUsername('admin');
                $author->setPassword('password'); // Not hashed yet, Day 2 task
                $author->setBio('Administrateur de SymfoConnect');
                $entityManager->persist($author);
            }

            $post->setAuthor($author);
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Votre post a été publié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
