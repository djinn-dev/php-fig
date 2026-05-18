<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Uri\Rfc3986\Uri as RfcUri;

use function explode;
use function fopen;
use function is_string;

class Factory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri))
        {
            $uri = $this->getUriFromString($uri);
        }

        if (!($uri instanceof UriInterface))
        {
            throw new InvalidArgumentException('Uri must be a string or an instance of UriInterface');
        }

        $request = new Request();
        $request = $request->withMethod($method);
        $request = $request->withUri($uri);

        return $request;
    }

    /**
     * @inheritDoc
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response();
        return $response->withStatus($code, $reasonPhrase);
    }

    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_string($uri))
        {
            $uri = $this->getUriFromString($uri);
        }

        if (!($uri instanceof UriInterface))
        {
            throw new InvalidArgumentException('Uri must be a string or an instance of UriInterface');
        }

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withMethod($method);
        $serverRequest = $serverRequest->withUri($uri);
        return $serverRequest->withServerParams($serverParams);
    }

    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $resource = @fopen('php://temp', 'r+');
        if ($resource === false)
        {
            throw new RuntimeException('Unable to open temporary stream.');
        }

        $stream = new Stream();
        $stream = $stream->withResource($resource);
        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $resource = @fopen($filename, $mode);
        if ($resource === false)
        {
            throw new RuntimeException('Unable to open temporary stream.');
        }

        $stream = new Stream();
        return $stream->withResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return (new Stream())->withResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function createUploadedFile(StreamInterface $stream, ?int $size = null, int $error = \UPLOAD_ERR_OK, ?string $clientFilename = null, ?string $clientMediaType = null): UploadedFileInterface
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function createUri(string $uri = ''): UriInterface
    {
        throw new \Exception('Not implemented');
    }

    private function getUriFromString(string $uri): UriInterface
    {
        $parsedUri = new RfcUri($uri);

        $uri = new Uri();

        $uri = $uri->withScheme($parsedUri->getScheme());

        $userInfo = $parsedUri->getUserInfo();
        if ($userInfo !== null)
        {
            $uri = $uri->withUserInfo(...explode(':', $userInfo));
        }

        $host = $parsedUri->getHost();
        if ($host !== null)
        {
            $uri = $uri->withHost($host);
        }

        $uri = $uri->withPort($parsedUri->getPort());

        $uri = $uri->withPath($parsedUri->getPath());

        $query = $parsedUri->getQuery();
        if ($query !== null)
        {
            $uri = $uri->withQuery($query);
        }

        $fragment = $parsedUri->getFragment();
        if ($fragment !== null)
        {
            $uri = $uri->withFragment($fragment);
        }

        return $uri;
    }
}
