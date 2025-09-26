<?php

namespace ChallengeQA\Tests\Unit;

use ChallengeQA\Config\Database;
use PHPUnit\Framework\TestCase;
use ChallengeQA\Controllers\UserController;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class UserControllerTest extends TestCase
{
    private UserController $controller;
    private $requestFactory;
    private $responseFactory;

    protected function setUp(): void
    {
        $this->controller = new UserController();
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    public function testRegister(): void
    {
        //DELETE FROM `challenge_qa`.`users`
        $this->deleteUsersAll();

        $requestData = [
            'email' => 'teste@qa.com',
            'password' => '2£e@IhFccj/N15j'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/register')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertTrue($responseBody['success']);
        $this->assertEquals('User registered successfully', $responseBody['message']);
    }

    public function testRegisterInvalidEmail(): void
    {
        $requestData = [
            'email' => 'teste',
            'password' => '2£e@IhFccj/N15j'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/register')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Email invalid', $responseBody['message']);
    }

    public function testRegisterEmailExists(): void
    {
        $requestData = [
            'email' => 'teste',
            'password' => '2£e@IhFccj/N15j'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/register')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Email already exists', $responseBody['message']);
    }

    public function testRegisterEmailExistsPasswordChange(): void
    {
        $requestData = [
            'email' => 'teste',
            'password' => '2£e@IhFccj/N15A'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/register')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Email already exists', $responseBody['message']);
    }

    public function testRegisterWeekPassword(): void
    {
        $requestData = [
            'email' => 'teste@qa.com',
            'password' => '123'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/register')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Password to weak', $responseBody['message']);
    }

    public function testLoginWithExistentUser(): void
    {
        $requestData = [
            'email' => 'teste@qa.com',
            'password' => '2£e@IhFccj/N15j'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/login')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertTrue($responseBody['success']);
        $this->assertEquals('Login successful', $responseBody['message']);
    }

    public function testLoginWithNonExistentUser(): void
    {
        $requestData = [
            'email' => 'nonexistent@example.com',
            'password' => 'anypassword'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/login')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        if ($result->getStatusCode() === 404) {
            $this->assertFalse($responseBody['success']);
            $this->assertEquals('User not found', $responseBody['message']);
        }
    }

    public function testLoginWithPasswordInvalid(): void
    {
        $requestData = [
            'email' => 'teste@qa.com',
            'password' => 'anypassword'
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/user/login')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Password is incorrect', $responseBody['message']);
    }

    private function deleteUsersAll(): void
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('DELETE FROM `challenge_qa`.`users`');
        $stmt->executeQuery();
    }
}