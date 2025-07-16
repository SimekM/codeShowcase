<?php

declare(strict_types=1);

namespace App\Components;

use Nette;

class PopupSystem extends Nette\Application\UI\Control
{
    public function __construct(
        private \App\Core\ModelCore $model_core
    ) {}

    public function render(): void
    {
        $this->template->addFilter('t', function ($key) {
            return $this->model_core->model_language->t($key, 'cs');
        });
        $this->template->setFile(__DIR__ . '/layout.latte');
        $this->template->render();
    }
}
