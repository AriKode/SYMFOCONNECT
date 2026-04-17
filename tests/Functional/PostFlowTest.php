<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostFlowTest extends WebTestCase
{
    public function testHomepageIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    public function testCreatePostRedirectsToLoginWhenNotConnected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/post/nouveau');

        $this->assertResponseRedirects('/login');
    }

    public function testConnectedUserCanAccessPostForm(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // On suppose qu'un utilisateur existe en base (si on utilise des fixtures)
        // Sinon on en crée un temporairement ou on mock
        $testUser = $userRepository->findOneByEmail('user@test.com');
        if (!$testUser) {
            $this->markTestSkipped('Utilisateur de test non trouvé.');
        }

        $client->loginUser($testUser);
        $client->request('GET', '/post/nouveau');

        $this->assertResponseIsSuccessful();
    }

    public function testApiPostsReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
