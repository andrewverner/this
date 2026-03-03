<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\Form\Builder;

interface FormRendererInterface
{
    public function supports(Form $form): bool;

    public function render(Form $form): string;
}
