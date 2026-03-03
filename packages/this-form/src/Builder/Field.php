<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

final class Field
{
    private mixed $value = null;

    private bool $required = false;

    private array $meta = [];

    private array $attributes = [];

    private bool $secure = false;

    private ?string $label = null;

    private bool $invalid = false;

    private bool $readonly = false;

    private ?string $errorMessage = null;

    /**
     * @var array<array-key, mixed>
     */
    private array $data = [];

    private FieldTypeEnum $type = FieldTypeEnum::TEXT;

    /**
     * @param array<FieldRendererInterface> $renderers
     */
    public function __construct(
        private readonly string $name,
        private string $requiredClass,
        private string $errorClass,
        private array $renderers = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FieldTypeEnum
    {
        return $this->type;
    }

    public function readonly(): self
    {
        $this->addMeta('readonly', true);

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function secure(): self
    {
        $this->secure = true;

        return $this;
    }

    public function value(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->secure ? null : $this->value;
    }

    public function required(): self
    {
        $this->required = true;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function addMeta(string $key, mixed $value): self
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function getMeta(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }

    public function attributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function text(): self
    {
        $this->type = FieldTypeEnum::TEXT;

        return $this;
    }

    public function password(): self
    {
        $this->type = FieldTypeEnum::PASSWORD;

        return $this;
    }

    public function textarea(): self
    {
        $this->type = FieldTypeEnum::TEXTAREA;

        return $this;
    }

    public function select(array $data): self
    {
        $this->type = FieldTypeEnum::SELECT;
        $this->data = $data;

        return $this;
    }

    public function checkbox(): self
    {
        $this->type = FieldTypeEnum::CHECKBOX;

        return $this;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public function radioGroup(array $data): self
    {
        $this->type = FieldTypeEnum::RADIO_GROUP;
        $this->data = $data;

        return $this;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public function checkboxGroup(array $data): self
    {
        $this->type = FieldTypeEnum::CHECKBOX_GROUP;
        $this->data = $data;

        return $this;
    }

    public function hidden(): self
    {
        $this->type = FieldTypeEnum::HIDDEN;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->addMeta('placeholder', $placeholder);

        return $this;
    }

    public function disabled(): self
    {
        $this->addMeta('disabled', true);

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function invalid(): self
    {
        $this->invalid = true;

        return $this;
    }

    public function isInvalid(): bool
    {
        return $this->invalid;
    }

    public function file(): self
    {
        $this->type = FieldTypeEnum::FILE;

        return $this;
    }

    public function multiple(): self
    {
        $this->addMeta('multiple', true);

        return $this;
    }

    public function getRequiredClass(): string
    {
        return $this->requiredClass;
    }

    public function getErrorClass(): string
    {
        return $this->errorClass;
    }

    public function button(): self
    {
        $this->type = FieldTypeEnum::BUTTON;

        return $this;
    }

    public function setErrorMessage(string $message): self
    {
        $this->errorMessage = $message;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function __toString(): string
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($this)) {
                return $renderer->render($this);
            }
        }

        return DefaultRenderer::render($this);
    }
}
