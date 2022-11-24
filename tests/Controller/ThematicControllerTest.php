<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Repository\UserRepository;

class ThematicControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?UserRepository $userRepository = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->urlGenerator = static::getContainer()->get('router.default');
    }

    public function testAdminAccess(): void
    {
        $userId = 10;
        /* get user from database */
        $testUser = $this->userRepository->find($userId);
        /* login testUser */
        $this->client->loginUser($testUser);
        /* simulate a request that user tries to create a new thematic */
        $this->client->request('GET', $this->urlGenerator->generate('app_thematic_new'));
        /* assert that user can't access to create a new thematic */
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
