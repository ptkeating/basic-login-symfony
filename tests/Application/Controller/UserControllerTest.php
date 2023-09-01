<?php

namespace App\Tests\Application\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\Factory;
use App\Tests\Traits\GeneratesInvalidUserFields;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{

    use GeneratesInvalidUserFields;

    /**
     * @var \App\Entity\User
     */
    private $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    protected function createUnauthenticatedClient(): KernelBrowser
    {
        return static::createClient(['HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json']);
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected function createAuthenticatedClient(): KernelBrowser
    {
        $client = $this->createUnauthenticatedClient();
        $container = static::getContainer();
        $password = $this->faker->password(16, 63);
        /** @var \Symfony\Component\PasswordHasher\UserPasswordHasherInterface */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $this->loggedInUser = UserFactory::createOne(['password' => $passwordHasher->hashPassword(new User(), $password)]);
        return $this->loginAndModifyClient($client, $this->loggedInUser->getEmail(), $password);
    }

    /**
     * Login a user and return a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected function loginAndModifyClient(KernelBrowser $client, $email = 'email', $password = 'password'): KernelBrowser
    {
        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $email,
            'password' => $password,
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testThatUserCreationRejectsInvalidData(): void
    {
        $client = $this->createUnauthenticatedClient();
        $validData = ['email' => $this->faker->email(), 'full_name' => $this->faker->name(), 'password' => $this->faker->password(7, 63)];
        $invalidData = $validData;
        // $controller = $this->createController();
        foreach (['full_name', 'email', 'password'] as $fieldName) {
            foreach (UserFactory::INVALID_FIELD_TYPES as $typeOfInvalidity) {
                $invalidData[$fieldName] = $this->{'generateInvalid' . snake_to_studly_case($fieldName) . 'Field'}($typeOfInvalidity);
                $this->makeInvalidRequestAndProcessResponse('/api/register', $invalidData, $fieldName, $client);
                $invalidData = $validData;
            }
        }
    }

    // /**
    //  * Test that the addition of another field results in a 415
    //  */
    // public function testThatUserCreationRejectsInvalidObjects(): void
    // {
    //     $client = $this->createUnauthenticatedClient();
    //     $invalidData = ['email' => $this->faker->email(), 'full_name' => $this->faker->name(), 'name' => 'Harlin Walsh', 'raw_password' => 'fashkjlgsfdakl312456#'];
    //     $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($invalidData));
    //     $response = $client->getResponse();
    //     // if ($response->getStatusCode() != 422) {
    //     //     var_dump($invalidData);
    //     //     echo 'Invalid field should be ' . $fieldName . PHP_EOL;
    //     // }
    //     $this->assertResponseStatusCodeSame(415);
    //     $this->assertSame('application/json', $response->headers->get('Content-Type'));
    //     // Test that response is not empty
    //     $this->assertNotEmpty($response->getContent());
    //     $jsonResponse = json_decode($response->getContent());
    // }

    public function testThatUserCreationRequiresAUniqueEmailAddress(): void
    {
        $client = $this->createUnauthenticatedClient();
        $user = UserFactory::createOne();
        // var_dump($user->getId());
        $invalidData = ['email' => $user->getEmail(), 'full_name' => $this->faker->name(), 'password' => $this->faker->password(12, 63)];
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($invalidData));
        $this->assertResponseIsUnprocessable();
        $response = $client->getResponse();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test that response is not empty
        $this->assertNotEmpty($response->getContent());
        $jsonResponse = json_decode($response->getContent());
        $this->assertNotEmpty($jsonResponse->detail);
        $this->assertStringContainsString('email', $jsonResponse->detail);
    }

    public function testThatUserCreationPersistsTheCorrectData(): void
    {
        $client = $this->createUnauthenticatedClient();
        $validData = ['email' => $this->faker->email(), 'full_name' => $this->faker->name(), 'password' => $this->faker->password(16, 63)];
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($validData));
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test that response is not empty
        $this->assertNotEmpty($response->getContent());
        $jsonResponse = json_decode($response->getContent());
        $this->assertNotEmpty($jsonResponse->data);
        $this->assertNotEmpty($jsonResponse->data->id);
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var \App\Repository\UserRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findBy(['email' => $validData['email'], 'full_name' => $validData['full_name']]);
        $this->assertNotEmpty($user);
    }

    public function testThatLoginWorks()
    {
        $client = $this->createUnauthenticatedClient();
        $container = static::getContainer();
        $password = $this->faker->password(16, 63);
        $unauthenticatedUser = UserFactory::createOne();
        /** @var \Symfony\Component\PasswordHasher\UserPasswordHasherInterface */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $user = UserFactory::createOne(['password' => $passwordHasher->hashPassword(new User(), $password)]);
        $this->assertNotEmpty($user->getId());

        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['username' => $unauthenticatedUser->getEmail(), 'password' => $password]));
        $this->assertResponseStatusCodeSame(401);

        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['username' => $user->getEmail(), 'password' => $this->faker->password(13)]));
        $this->assertResponseStatusCodeSame(401);

        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $user->getEmail(),
            'password' => $password,
        ]));
        $this->assertResponseIsSuccessful();
    }

    public function testThatShowReturnsTheCorrectUser()
    {
        // create a logged in user
        $client = $this->createAuthenticatedClient();
        $unauthenticatedUser = UserFactory::createOne();
        /** @var \Symfony\Component\PasswordHasher\UserPasswordHasherInterface */
        $client->request('GET', '/api/users/' . $unauthenticatedUser->getId());
        // cannot retrieve details for another user
        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/api/users/' . $this->loggedInUser->getId());
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertNotEmpty($response->getContent());
        $jsonResponse = json_decode($response->getContent());
        $this->assertNotEmpty($jsonResponse->data);
        $this->assertEquals($this->loggedInUser->getEmail(), $jsonResponse->data->email);
    }

    public function testThatEditRejectsAForbiddenUser()
    {
        $client = $this->createUnauthenticatedClient();
        $container = static::getContainer();
        $password = $this->faker->password(16, 63);
        /** @var \Symfony\Component\PasswordHasher\UserPasswordHasherInterface */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $user = UserFactory::createOne(['password' => $passwordHasher->hashPassword(new User(), $password)]);
        $forbiddenUser = UserFactory::createOne(['password' => $passwordHasher->hashPassword(new User(), $password)]);
        $client = $this->loginAndModifyClient($client, $forbiddenUser->getEmail(), $password);
        foreach (['PUT', 'PATCH'] as $method) {
            $client->request($method, '/api/users/' . $user->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));
            // cannot retrieve details for another user
            $this->assertResponseStatusCodeSame(403);
        }
    }

    public function testThatEditRejectsInvalidData()
    {
        $client = $this->createAuthenticatedClient();
        $user = $this->loggedInUser;
        $validData = ['full_name' => $this->faker->name(), 'house_number' => null, 'street_address' => null, 'city' => null, 'postcode' => null];
        $invalidData = $validData;
        // test full name field separately as any empty fields are ignored during update, so the empty field invalidity for full_name is not checked
        $fieldSize = 128;
        $fieldName = 'full_name';
        $invalidData[$fieldName] = str_pad($this->faker->text($fieldSize), $fieldSize, '0');
        $this->makeInvalidRequestAndProcessResponse('/api/users/' . $user->getId(), $invalidData, $fieldName, $client, 'PATCH');
        $invalidData = $validData;
        foreach (['house_number', 'street_address', 'city', 'postcode'] as $fieldName) {
            foreach (UserFactory::INVALID_FIELD_TYPES as $typeOfInvalidity) {
                $invalidData[$fieldName] = $this->{'generateInvalid' . snake_to_studly_case($fieldName) . 'Field'}($typeOfInvalidity);
                $this->makeInvalidRequestAndProcessResponse('/api/users/' . $user->getId(), $invalidData, $fieldName, $client, 'PATCH');
                $invalidData = $validData;
            }
        }
    }

    public function testThatEditPersistsValidData()
    {
        $client = $this->createAuthenticatedClient();
        $user = $this->loggedInUser;
        $untouchedData = ['full_name' => null, 'house_number' => null, 'street_address' => null, 'city' => null, 'postcode' => null];
        $touchedData = $untouchedData;
        $validString = 'validstring';
        $propertiesToFindUser = ['email' => $user->getEmail()];
        foreach (['full_name', 'house_number', 'street_address', 'city', 'postcode'] as $fieldName) {
            $propertiesToFindUser[$fieldName] = $validString;
            $touchedData[$fieldName] = $validString;
            $client->request('PATCH', '/api/users/' . $user->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($touchedData));
            $response = $client->getResponse();
            $this->assertResponseIsSuccessful();
            $this->assertSame('application/json', $response->headers->get('Content-Type'));
            // Test that response is not empty
            $this->assertNotEmpty($response->getContent());
            $jsonResponse = json_decode($response->getContent());
            $this->assertNotEmpty($jsonResponse->data);
            $this->assertEquals($validString, $jsonResponse->data->{$fieldName});
            $touchedData = $untouchedData;
        }
        // now test that all fields in the database have been updated
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var \App\Repository\UserRepository */
        $userRepository = $entityManager->getRepository(User::class);
        // if all fields are updated in the DB, then we will find a user with a specific email and all other properties set to $validString
        $user = $userRepository->findOneBy(['email' => $user->getEmail()]);
        $this->assertNotEmpty($user);
        foreach ($propertiesToFindUser as $propName => $propValue) {
            $this->assertEquals($propValue, $user->{'get' . snake_to_studly_case($propName)}());
        }
    }

    protected function makeInvalidRequestAndProcessResponse(string $route, array $invalidData, string $fieldName, KernelBrowser $client, string $method = 'POST')
    {
        $client->request($method, $route, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($invalidData));
        $response = $client->getResponse();
        // if ($response->getStatusCode() != 422) {
        //     var_dump($invalidData);
        //     echo 'Invalid field should be ' . $fieldName . PHP_EOL;
        // }
        $this->assertResponseIsUnprocessable();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test that response is not empty
        $this->assertNotEmpty($response->getContent());
        $jsonResponse = json_decode($response->getContent());
        $this->assertNotEmpty($jsonResponse->detail);
        $this->assertStringContainsString($fieldName, strtolower($jsonResponse->detail));
    }


    // protected function createController(): UserController
    // {
    //     return new UserController();
    // }
}
