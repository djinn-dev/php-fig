<?php

declare(strict_types=1);

use DjinnDev\Psr17\ResponseFactory;
use DjinnDev\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactoryTest extends TestCase
{
    public function testCreateResponseIsResponseInterface(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, ResponseFactory::getInstance()->createResponse());
    }

    public function testCreateRequestParameters(): void
    {
        foreach (Response::STATUS_CODE_REASONS as $code => $reason)
        {
            $response = ResponseFactory::getInstance()->createResponse($code, $reason);
            $this->assertEquals($code, $response->getStatusCode());
            $this->assertEquals($reason, $response->getReasonPhrase());
        }
    }
}
