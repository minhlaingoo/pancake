<?php

namespace App\Livewire\Protocols;

use Livewire\Component;

class Run extends Component
{
    public $current_step = 1;
    public $steps = [
        [
            'title' => 'Protocol 1',
            'step' => [
                ['title' => 'Step 1 of Protocol 1', 'is_finished' => false],
                ['title' => 'Step 2 of Protocol 1', 'is_finished' => true],
                ['title' => 'Step 3 of Protocol 1', 'is_finished' => false],
            ],
        ],
        [
            'title' => 'Protocol 2',
            'step' => [
                ['title' => 'Step 1 of Protocol 2', 'is_finished' => true],
                ['title' => 'Step 2 of Protocol 2', 'is_finished' => true],
                ['title' => 'Step 3 of Protocol 2', 'is_finished' => false],
                ['title' => 'Step 4 of Protocol 2', 'is_finished' => true],
            ],
        ],
        [
            'title' => 'Protocol 3',
            'step' => [
                ['title' => 'Step 1 of Protocol 3', 'is_finished' => false],
                ['title' => 'Step 2 of Protocol 3', 'is_finished' => false],
                ['title' => 'Step 3 of Protocol 3', 'is_finished' => true],
            ],
        ],
        [
            'title' => 'Protocol 4',
            'step' => [
                ['title' => 'Step 1 of Protocol 4', 'is_finished' => true],
                ['title' => 'Step 2 of Protocol 4', 'is_finished' => true],
                ['title' => 'Step 3 of Protocol 4', 'is_finished' => true],
                ['title' => 'Step 4 of Protocol 4', 'is_finished' => false],
            ],
        ],
        [
            'title' => 'Protocol 5',
            'step' => [
                ['title' => 'Step 1 of Protocol 5', 'is_finished' => false],
                ['title' => 'Step 2 of Protocol 5', 'is_finished' => true],
                ['title' => 'Step 3 of Protocol 5', 'is_finished' => false],
            ],
        ],
        [
            'title' => 'Protocol 6',
            'step' => [
                ['title' => 'Step 1 of Protocol 6', 'is_finished' => false],
                ['title' => 'Step 2 of Protocol 6', 'is_finished' => true],
                ['title' => 'Step 3 of Protocol 6', 'is_finished' => true],
                ['title' => 'Step 4 of Protocol 6', 'is_finished' => true],
            ],
        ],
        [
            'title' => 'Protocol 7',
            'step' => [
                ['title' => 'Step 1 of Protocol 7', 'is_finished' => false],
                ['title' => 'Step 2 of Protocol 7', 'is_finished' => false],
                ['title' => 'Step 3 of Protocol 7', 'is_finished' => true],
            ],
        ],
    ];

    public function setStep($step)
    {
        $this->current_step = $step;
    }

    public function render()
    {
        return view('livewire.protocols.run');
    }
}
