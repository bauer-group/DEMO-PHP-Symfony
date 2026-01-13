<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create schema for SQLite in-memory database
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }
    }

    public function testIndexPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('nav.navbar');
        $this->assertSelectorTextContains('h5', 'My Todos');
    }

    public function testNewTodoPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorTextContains('h5', 'Create New Todo');
    }

    public function testHealthCheckEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('database', $response);
        $this->assertArrayHasKey('timestamp', $response);
    }

    public function testApiListEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/todos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('todos', $response);
        $this->assertArrayHasKey('stats', $response);
        $this->assertIsArray($response['todos']);
    }

    public function testCreateAndDeleteTodo(): void
    {
        $client = static::createClient();

        // Go to new todo page
        $crawler = $client->request('GET', '/new');
        $this->assertResponseIsSuccessful();

        // Submit the form
        $form = $crawler->selectButton('Create Todo')->form([
            'todo[title]' => 'Test Todo Item',
            'todo[description]' => 'This is a test description',
            'todo[priority]' => '2',
        ]);
        $client->submit($form);

        // Should redirect to index
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Check the todo appears
        $this->assertSelectorTextContains('.todo-title', 'Test Todo Item');
    }
}
