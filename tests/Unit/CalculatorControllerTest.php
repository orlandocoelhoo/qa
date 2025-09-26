<?php

namespace ChallengeQA\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ChallengeQA\Controllers\CalculatorController;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class CalculatorControllerTest extends TestCase
{
    private CalculatorController $controller;
    private $requestFactory;
    private $responseFactory;

    protected function setUp(): void
    {
        $this->controller = new CalculatorController();
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    //SIMPLES INTEREST
    public function testSimpleInterestCalculation(): void
    {
        $requestData = [
            'principal' => 1000,
            'rate' => 5,
            'time' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->simpleInterest($request, $response);
        
        $this->assertEquals(200, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertTrue($responseBody['success']);
        $this->assertEquals('simple_interest', $responseBody['calculation_type']);
        $this->assertEquals(100, $responseBody['results']['interest']);
        $this->assertEquals(1100, $responseBody['results']['total_amount']);
    }

    public function testSimpleInterestMissingData(): void
    {
        $requestData = [
            'rate' => 5,
            'time' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->simpleInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Principal, rate, and time are required', $responseBody['message']);
    }

    public function testSimpleInterestStringValue(): void
    {
        $requestData = [
            'principal' => "cem",
            'rate' => 5,
            'time' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->simpleInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Invalid values', $responseBody['message']);
    }

    public function testSimpleInterestNegativeValue(): void
    {
        $requestData = [
            'principal' => -1000,
            'rate' => 5,
            'time' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->simpleInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Values ​​must be positive', $responseBody['message']);
    }

    // COMPOUND INTEREST
    public function testCompoundInterestCalculation(): void
    {
        $requestData = [
            'principal' => 200,
            'rate' => 1.8,
            'time' => 10,
            "compounding_frequency" => 12
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(200, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertTrue($responseBody['success']);
        $this->assertEquals('compound_interest', $responseBody['calculation_type']);
        $this->assertEquals(39.41, $responseBody['results']['interest']);
        $this->assertEquals(239.4, $responseBody['results']['total_amount']);
    }
    
    public function testCompoundInterestMissingData(): void
    {
        $requestData = [
            'rate' => 1.8,
            'time' => 10,
            "compounding_frequency" => 12
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Principal, rate, and time are required', $responseBody['message']);
    }

    public function testCompoundInterestStringValue(): void
    {
        $requestData = [
            'principal' => 'cem',
            'rate' => 1.8,
            'time' => 10,
            "compounding_frequency" => 12
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Invalid Values', $responseBody['message']);
    }

    public function testCompoundInterestNegativeValue(): void
    {
        $requestData = [
            'principal' => -200,
            'rate' => 1.8,
            'time' => 10,
            "compounding_frequency" => 12
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Values ​​must be positive', $responseBody['message']);
    }

    // INSTALMENT
    public function testInstalmentCalculation(): void
    {
        $requestData = [
            'principal' => 50,
            'rate' => 1.8,
            'installments' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->installmentSimulation($request, $response);
        
        $this->assertEquals(200, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertTrue($responseBody['success']);
        $this->assertEquals('installment_simulation', $responseBody['calculation_type']);
        $this->assertEquals(25.1, $responseBody['results']['installment_amount']);
        $this->assertEquals(50.11, $responseBody['results']['total_amount']);
        $this->assertEquals(0.113, $responseBody['results']['total_interest']);
        $this->assertEquals(25.1, $responseBody['results']['breakdown'][0]['installment_amount']);
        $this->assertEquals(25.1, $responseBody['results']['breakdown'][1]['installment_amount']);
    }

    public function testInstalmentMissingData(): void
    {
        $requestData = [
            'rate' => 1.8,
            'installments' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->installmentSimulation($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Principal, rate, and installments are required', $responseBody['message']);
    }

    public function testInstalmentStringValue(): void
    {
        $requestData = [
            'principal' => 'cem',
            'rate' => 1.8,
            'installments' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->installmentSimulation($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Invalid values', $responseBody['message']);
    }

    public function testInstalmentNegativeValue(): void
    {
        $requestData = [
            'principal' => -50,
            'rate' => 1.8,
            'installments' => 2
        ];

        $request = $this->requestFactory
            ->createServerRequest('POST', '/api/calculator/simple-interest')
            ->withHeader('Content-Type', 'application/json');
        
        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        $result = $this->controller->installmentSimulation($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertFalse($responseBody['success']);
        $this->assertEquals('Values ​​must be positive', $responseBody['message']);
    }


}