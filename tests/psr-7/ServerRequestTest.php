<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use DjinnDev\Psr17\UploadedFileFactory;
use DjinnDev\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestTest extends TestCase
{
    private array $paramsArray = ['foo' => 'bar'];
    private array $additionalParamsArray = ['biz' => 'baz'];

    public function testConstruct(): void
    {
        $request = new ServerRequest();
        $this->assertInstanceOf(ServerRequest::class, $request);
    }


    public function testServerParamsMethods(): void
    {
        $serverRequest = new ServerRequest();
        $this->assertEquals([], $serverRequest->getServerParams());

        $serverRequest = new ServerRequest($this->paramsArray);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($this->paramsArray, $serverRequest->getServerParams());
    }

    public function testCookieParamsMethods(): void
    {
        $serverRequest = new ServerRequest();
        $this->assertEquals([], $serverRequest->getCookieParams());

        $serverRequest = $serverRequest->withCookieParams($this->paramsArray);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withCookieParams($this->paramsArray));
        $this->assertEquals($this->paramsArray, $serverRequest->getCookieParams());
        $this->assertNotEquals($serverRequest, $serverRequest->withCookieParams($this->paramsArray + $this->additionalParamsArray));
    }

    public function testQueryParamsMethods(): void
    {
        $serverRequest = new ServerRequest();
        $this->assertEquals([], $serverRequest->getQueryParams());

        $serverRequest = $serverRequest->withQueryParams($this->paramsArray);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withQueryParams($this->paramsArray));
        $this->assertEquals($this->paramsArray, $serverRequest->getQueryParams());
        $this->assertNotEquals($serverRequest, $serverRequest->withQueryParams($this->paramsArray + $this->additionalParamsArray));
    }

    public function testUploadedFilesMethods(): void
    {
        $uploadedFiles = [
            UploadedFileFactory::getInstance()->createUploadedFile(
                StreamFactory::getInstance()->createStream(),
            ),
        ];

        $serverRequest = new ServerRequest();
        $this->assertEquals([], $serverRequest->getUploadedFiles());

        $serverRequest = $serverRequest->withUploadedFiles($uploadedFiles);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withUploadedFiles($uploadedFiles));
        $this->assertEquals($uploadedFiles, $serverRequest->getUploadedFiles());

        $uploadedFiles[] = UploadedFileFactory::getInstance()->createUploadedFile(
            StreamFactory::getInstance()->createStream(),
        );
        $this->assertNotEquals($serverRequest, $serverRequest->withUploadedFiles($uploadedFiles));

        $this->expectException(InvalidArgumentException::class);
        $uploadedFiles[] = 'test';
        $serverRequest->withUploadedFiles($uploadedFiles);
    }

    public function testParsedBodyMethods(): void
    {
        $serverRequest = new ServerRequest();
        $this->assertEquals(null, $serverRequest->getParsedBody());

        $serverRequest = $serverRequest->withParsedBody($this->paramsArray);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withParsedBody($this->paramsArray));
        $this->assertEquals($this->paramsArray, $serverRequest->getParsedBody());
        $this->assertNotEquals($serverRequest, $serverRequest->withParsedBody($this->paramsArray + $this->additionalParamsArray));

        $paramsObject = json_decode(json_encode($this->paramsArray));
        $this->assertNotEquals($serverRequest, $serverRequest->withParsedBody($paramsObject));

        $serverRequest = $serverRequest->withParsedBody($paramsObject);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withParsedBody($paramsObject));
        $this->assertEquals($paramsObject, $serverRequest->getParsedBody());

        $paramsObject = json_decode(json_encode($this->paramsArray + $this->additionalParamsArray));
        $this->assertNotEquals($serverRequest, $serverRequest->withParsedBody($paramsObject));
        $this->assertNotEquals($serverRequest, $serverRequest->withParsedBody($this->paramsArray));

        $this->expectException(InvalidArgumentException::class);
        $serverRequest->withParsedBody('test');
    }

    public function testAttributesMethods(): void
    {
        $attribute = array_key_first($this->paramsArray);
        $value = $this->paramsArray[$attribute];

        $serverRequest = new ServerRequest();
        $this->assertEquals([], $serverRequest->getAttributes());
        $this->assertEquals(null, $serverRequest->getAttribute($attribute));
        $this->assertEquals($value, $serverRequest->getAttribute($attribute, $value));

        $serverRequest = $serverRequest->withAttribute($attribute, $value);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($serverRequest, $serverRequest->withAttribute($attribute, $value));
        $this->assertEquals($this->paramsArray, $serverRequest->getAttributes());
        $this->assertEquals($value, $serverRequest->getAttribute($attribute));
        $this->assertNotEquals($attribute, $serverRequest->getAttribute($attribute, $attribute));
        $this->assertNotEquals($serverRequest, $serverRequest->withoutAttribute($attribute));

        $serverRequest = $serverRequest->withoutAttribute($attribute);
        $this->assertEquals($serverRequest, $serverRequest->withoutAttribute($attribute));
        $this->assertEquals([], $serverRequest->getAttributes());
        $this->assertEquals(null, $serverRequest->getAttribute($attribute));
        $this->assertEquals($value, $serverRequest->getAttribute($attribute, $value));
    }
}
