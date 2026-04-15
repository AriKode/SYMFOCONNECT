<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de l'utilisateur principal Arikode
        $arikode = new User();
        $arikode->setEmail('contact@arikode.fr');
        $arikode->setUsername('Arikode');
        $arikode->setPassword('password'); // Sera hashé au Jour 2
        $arikode->setBio('Développeur passionné et créateur de SymfoConnect. 👨‍💻');
        $manager->persist($arikode);

        // Création d'autres utilisateurs pour peupler le réseau
        $users = [];
        $names = ['Sophie', 'Lucas', 'Emma', 'Thomas', 'Julie'];
        
        foreach ($names as $name) {
            $user = new User();
            $user->setEmail(strtolower($name) . '@example.com');
            $user->setUsername($name);
            $user->setPassword('password');
            $user->setBio("Bonjour, je suis $name ! Ravi d'être sur SymfoConnect.");
            $manager->persist($user);
            $users[] = $user;
        }

        // Ajout d'Arikode à la liste pour les posts
        $allUsers = array_merge([$arikode], $users);

        // Création de quelques posts variés
        $postsData = [
            ['Arikode', 'Bienvenue sur la version Alpha de SymfoConnect ! 🚀'],
            ['Sophie', 'Quel beau design, j\'adore les effets de transparence ! 😍'],
            ['Arikode', 'Le Jour 1 se termine bien, les entités et le formulaire sont fonctionnels.'],
            ['Lucas', 'Est-ce qu\'on pourra bientôt uploader des images ?'],
            ['Emma', 'Je viens de créer mon premier post ! #HelloSymfony'],
            ['Thomas', 'Le mode sombre est vraiment reposant pour les yeux.'],
            ['Julie', 'Salut tout le monde ! Quelqu\'un a une bonne ressource pour apprendre Twig ?'],
            ['Arikode', 'N\'oubliez pas de tester le formulaire de création de post !'],
        ];

        foreach ($postsData as [$username, $content]) {
            $post = new Post();
            $post->setContent($content);
            
            // Trouver l'entité User correspondante
            foreach ($allUsers as $u) {
                if ($u->getUsername() === $username) {
                    $post->setAuthor($u);
                    break;
                }
            }
            
            // Décaler légèrement les dates de création pour le tri
            $post->setCreatedAt(new \DateTimeImmutable('-' . (count($postsData) - array_search([$username, $content], $postsData)) . ' hours'));
            
            $manager->persist($post);
        }

        $manager->flush();
    }
}
