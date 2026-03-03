<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

interface FieldRendererInterface
{
    public function supports(Field $field): bool;

    public function render(Field $field): string;
}
