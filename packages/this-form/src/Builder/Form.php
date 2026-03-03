<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

final readonly class Form
{
    /**
     * @param array<FieldRendererInterface> $fieldRenderers
     * @param array<FormRendererInterface> $formRenderers
     */
    public function __construct(
        private ?object $object = null,
        private string $requiredClass = 'required',
        private string $errorClass = 'error',
        private array $fieldRenderers = [],
        private array $formRenderers = [],
    ) {
    }

    public function field(string $name): Field
    {
        $field = new Field($name, $this->requiredClass, $this->errorClass, $this->fieldRenderers);

        if ($this->object) {
            if (property_exists($this->object, $name)) {
                $field->value((string) $this->object->{$name});
            }
        }

        return $field;
    }

    public function open(string $action = '', string $method = 'POST', bool $multipart = false, array $options = []): string
    {
        foreach ($this->formRenderers as $renderer) {
            if ($renderer->supports($this)) {
                return $renderer->render($this);
            }
        }

        $tag = "<form action=\"$action\" method=\"$method\"";

        if ($multipart) {
            $tag .= "enctype=\"multipart/form-data\"";
        }

        foreach ($options as $key => $value) {
            $tag .= " $key=\"$value\"";
        }

        return $tag . '>';
    }

    public function submitButton(string $title): Field
    {
        return (new Field('submit', $this->requiredClass, $this->errorClass, $this->fieldRenderers))
            ->button()
            ->value($title)
            ->addMeta('type', 'submit')
        ;
    }

    public function resetButton(string $title): Field
    {
        return (new Field('reset', $this->requiredClass, $this->errorClass, $this->fieldRenderers))
            ->button()
            ->value($title)
            ->addMeta('type', 'reset')
        ;
    }

    public function close(): string
    {
        return '</form>';
    }
}
