<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace App\This\Core\Request;

use This\Contracts\RequestInterface;
use This\Contracts\RequestMethodsEnum;

final class Request implements RequestInterface
{
    private ?RequestMethodsEnum $method = null;

    private ?string $uri = null;

    private ?string $body = null;

    private array $headers = [];

    private ?string $scheme = null;

    private ?string $host = null;

    private ?int $port = null;

    private ?string $path = null;

    private ?string $queryString = null;

    private array $get = [];

    private array $post = [];

    private array $files = [];

    private array $attributes = [];

    private array $cookies = [];

    private array $server = [];

    private bool $freeze = false;

    private array $bodyAttributes = [];

    private array $pathAttributes = [];

    private array $queryAttributes = [];

    public function setMethod(RequestMethodsEnum $method): self
    {
        $this->checkFreeze();
        $this->method = $method;

        return $this;
    }

    public function getMethod(): ?RequestMethodsEnum
    {
        return $this->method;
    }

    public function setUri(string $uri): self
    {
        $this->checkFreeze();
        $this->uri = $uri;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setBody(string $body): self
    {
        $this->checkFreeze();
        $this->body = $body;

        $this->parseBody();

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setHeaders(array $headers): self
    {
        $this->checkFreeze();
        $this->headers = $headers;

        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setScheme(string $scheme): self
    {
        $this->checkFreeze();
        $this->scheme = $scheme;

        return $this;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setHost(string $host): self
    {
        $this->checkFreeze();
        $this->host = $host;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setPort(int $port): self
    {
        $this->checkFreeze();
        $this->port = $port;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPath(string $path): self
    {
        $this->checkFreeze();
        $this->path = $path;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setQueryString(string $queryString): self
    {
        $this->checkFreeze();
        $this->queryString = $queryString;

        parse_str($queryString, $this->queryAttributes);

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    public function getQueryParameter(string $name, $default = null): ?string
    {
        return $this->queryAttributes[$name] ?? $default;
    }

    public function getQuery(): array
    {
        return $this->queryAttributes;
    }

    public function setGet(array $get): self
    {
        $this->checkFreeze();
        $this->get = $get;

        return $this;
    }

    public function get(): array
    {
        return $this->get;
    }

    public function setPost(array $post): self
    {
        $this->checkFreeze();
        $this->post = $post;

        return $this;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function setFiles(array $files): self
    {
        $this->checkFreeze();
        $this->files = $files;

        return $this;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function setAttributes(array $attributes): RequestInterface
    {
        $this->checkFreeze();
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttribute(string|int $key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setCookies(array $cookies): self
    {
        $this->checkFreeze();
        $this->cookies = $cookies;

        return $this;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function setServer(array $server): self
    {
        $this->checkFreeze();
        $this->server = $server;

        return $this;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function freeze(): void
    {
        $this->freeze = true;
    }

    public function setPathParameters(array $parameters): self
    {
        $this->pathAttributes = $parameters;

        return $this;
    }

    public function getPathParameter(string $name, $default = null): ?string
    {
        return $this->pathAttributes[$name] ?? $default;
    }

    public function getPathParameters(): array
    {
        return $this->pathAttributes;
    }

    public function getBodyParameter(string $name, $default = null): ?string
    {
        return $this->bodyAttributes[$name] ?? $default;
    }

    public function getBodyParameters(): array
    {
        return $this->bodyAttributes;
    }

    private function checkFreeze(): void
    {
        if ($this->freeze) {
            throw new \RuntimeException(message: 'Request is freeze and can not be modified.');
        }
    }

    private function parseBody(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? null;

        if ($method === 'GET' || $method === 'POST') {
            return;
        }

        $headers = headers_list();

        if (isset($headers['Content-Type'])) {
            if (str_contains($headers['Content-Type'], 'application/json')) {
                $this->bodyAttributes = json_decode($this->body, true) ?: [];

                return;
            }

            parse_str($this->body, $this->bodyAttributes);
        }
    }

    public function isPost(): bool
    {
        return $this->post !== [];
    }
}
