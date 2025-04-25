<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class SmokeTest extends WebTestCase
{
    public function testApiDocUrlIsSuccessful(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/doc');
        self::assertResponseIsSuccessful();
    }

    // test if the api doc url is secure
    // public function testApiAccountUrlIsSecure(): void
    // {
    //     $client = self::createClient();
    //     $client->request('GET', '/api/account/me');
    //     self::assertResponseStatusCodeSame(401);
    // }

    public function testLoginRouteCanConnectAValidUser(): void
    {

        $client = self::createClient();
        $client->followRedirects(false);
        for ($i = 1; $i <= 5; $i++) {

            // request registration
            // $client->request('POST', '/api/registration', [], [],
            // [
            //     'content-Type' => 'application/json',
            // ],
            // json_encode([
            //     'firstname' => 'testfirstname',
            //     'lastname' => 'testlastname',
            //     'email' => 'test@test.test',
            //     'password' => 'password',
            // ], JSON_THROW_ON_ERROR));
            // $userRepository = static::getContainer()->get(UserRepository::class);
            // $testUser = $userRepository->findOneByEmail('email.'.$i.'@studi.fr');
        
            // $client->loginUser($testUser);
            // request login
            $client->request('POST', '/api/login', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'username' => 'email.'.$i.'@studi.fr',
                'password' => 'password'.$i,
            ]));

            $statusCode = $client->getResponse()->getStatusCode();
            // dd($statusCode);
            // add test
            self::assertResponseIsSuccessful();
            self::assertResponseStatusCodeSame(200);
            $this->assertEquals(200, $statusCode);

            $content = $client->getResponse()->getContent();
            $this->assertJson($content);
            $this->assertStringContainsString('apiToken', $content);
            $this->assertStringContainsString('roles', $content);
            $this->assertStringContainsString('user', $content);
        }
    }
}
