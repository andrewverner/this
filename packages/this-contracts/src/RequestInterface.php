<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Contracts;

interface RequestInterface
{
    public function setMethod(RequestMethodsEnum $method): self;

    public function getMethod(): ?RequestMethodsEnum;

    public function setUri(string $uri): self;

    public function getUri(): ?string;

    public function setBody(string $body): self;

    public function getBody(): ?string;

    public function setHeaders(array $headers): self;

    public function getHeader(string $name): ?string;

    public function getHeaders(): array;

    public function setScheme(string $scheme): self;

    public function getScheme(): ?string;

    public function setHost(string $host): self;

    public function getHost(): ?string;

    public function setPort(int $port): self;

    public function getPort(): ?int;

    public function setPath(string $path): self;

    public function getPath(): ?string;

    public function setQueryString(string $queryString): self;

    public function getQueryString(): ?string;

    public function getQueryParameter(string $name): ?string;

    public function getQuery(): array;

    public function setGet(array $get): self;

    public function get(): array;

    public function setPost(array $post): self;

    public function post(): array;

    public function setFiles(array $files): self;

    public function files(): array;

    public function setAttributes(array $attributes): self;

    public function getAttribute(string|int $key, $default = null): mixed;

    public function getAttributes(): array;

    public function setCookies(array $cookies): self;

    public function cookies(): array;

    public function setServer(array $server): self;

    public function server(): array;

    public function freeze(): void;

    public function setPathParameters(array $parameters): self;

    public function getPathParameter(string $name, $default = null): ?string;

    public function getPathParameters(): array;

    public function getBodyParameter(string $name, $default = null): ?string;

    public function getBodyParameters(): array;

    public function isPost(): bool;
}
