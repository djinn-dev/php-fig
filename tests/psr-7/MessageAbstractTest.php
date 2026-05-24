<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use DjinnDev\Psr7\MessageAbstract;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;

final class MessageAbstractTest extends TestCase
{
    private function getTestClass(): MessageAbstract
    {
        return new class () extends MessageAbstract {};
    }

    public function testProtocolVersionMethods(): void
    {
        $version = '2';

        $testClass = $this->getTestClass();
        $this->assertInstanceOf(MessageInterface::class, $testClass->withProtocolVersion($version));

        $testClass = $testClass->withProtocolVersion($version);
        $this->assertEquals($testClass, $testClass->withProtocolVersion($version));
        $this->assertEquals($version, $testClass->getProtocolVersion());

        $testClass = $testClass->withProtocolVersion($version);
        $this->assertNotEquals($testClass, $testClass->withProtocolVersion('1.1'));

        $this->expectException(InvalidArgumentException::class);
        $testClass->withProtocolVersion('0.9');

        $this->expectException(InvalidArgumentException::class);
        $testClass->withProtocolVersion('foo');
    }

    public function testHeaderMethods(): void
    {
        $header = 'foo';
        $valueString = 'bar';
        $valueArray = [$valueString];

        $testClass = $this->getTestClass();

        $this->assertInstanceOf(MessageInterface::class, $testClass->withHeader($header, $valueString));
        $this->assertInstanceOf(MessageInterface::class, $testClass->withHeader($header, $valueArray));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertEquals($testClass, $testClass->withHeader($header, $valueString));
        $this->assertNotEquals($valueString, $testClass->getHeader($header));

        $testClass = $testClass->withHeader($header, $valueArray);
        $this->assertEquals($testClass, $testClass->withHeader($header, $valueArray));
        $this->assertEquals($valueArray, $testClass->getHeader($header));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertEquals($testClass, $testClass->withHeader($header, $valueArray));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withHeader($header, 'baz'));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withHeader('baz', $valueString));

        $this->expectException(InvalidArgumentException::class);
        $testClass->withHeader($header, false);

        $this->expectException(InvalidArgumentException::class);
        $testClass->withHeader('?', 'test');

        $this->expectException(InvalidArgumentException::class);
        $testClass->withHeader('test', PHP_EOL);
    }

    public function testWithAddedHeaderMethod(): void
    {
        $header = 'foo';
        $valueString = 'bar';
        $valueArray = [$valueString];

        $testClass = $this->getTestClass();

        $this->assertInstanceOf(MessageInterface::class, $testClass->withAddedHeader($header, $valueString));
        $this->assertInstanceOf(MessageInterface::class, $testClass->withAddedHeader($header, $valueArray));

        $testClass = $testClass->withAddedHeader($header, $valueString);
        $this->assertEquals($testClass, $testClass->withAddedHeader($header, $valueString));

        $testClass = $testClass->withAddedHeader($header, $valueArray);
        $this->assertEquals($testClass, $testClass->withAddedHeader($header, $valueArray));

        $this->assertEquals($testClass->withAddedHeader($header, $valueString), $testClass->withAddedHeader($header, $valueArray));

        $testClass = $testClass->withAddedHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withAddedHeader($header, [$valueString, $valueString]));

        $testClass = $testClass->withAddedHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withAddedHeader($header, $header));

        $testClass = $testClass->withAddedHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withAddedHeader($valueString, $valueString));

        $this->expectException(InvalidArgumentException::class);
        $testClass->withAddedHeader($header, false);
    }

    public function testWithoutHeaderMethod(): void
    {
        $header = 'foo';
        $valueString = 'bar';

        $testClass = $this->getTestClass();

        $this->assertInstanceOf(MessageInterface::class, $testClass->withoutHeader($header));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertInstanceOf(MessageInterface::class, $testClass->withoutHeader($header));

        $this->assertNotEquals($testClass, $testClass->withoutHeader($header));
        $testClass = $testClass->withoutHeader($header);
        $this->assertEquals($testClass, $testClass->withoutHeader($header));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertEquals($testClass, $testClass->withoutHeader($header . $valueString));

        $testClass = $testClass->withHeader($header, $valueString);
        $this->assertNotEquals($testClass, $testClass->withoutHeader($header));
    }

    public function testWithBody(): void
    {
        $body = StreamFactory::getInstance()->createStream();

        $testClass = $this->getTestClass();

        $this->assertInstanceOf(MessageInterface::class, $testClass->withBody($body));

        $testClass = $testClass->withBody($body);
        $this->assertEquals($testClass, $testClass->withBody($body));

        $testClass = $testClass->withBody($body);
        $body = StreamFactory::getInstance()->createStream();
        $this->assertNotEquals($testClass, $testClass->withBody($body));
    }
}
