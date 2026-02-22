<?php

namespace App\Livewire\Charts;

use Livewire\Component;

class SamplePieChart extends Component
{
    public array $dataset = [];
    public array $labels = [];

    public function mount()
    {
        $this->labels = $this->getLabels();

        $this->dataset = [
            [
                'label' => 'Volume in each chamber',
                'data' => $this->getRandomData(),
                // ✅ OPTIONAL: Define inline if needed
                // 'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
            ],
        ];
    }

    private function getLabels(): array
    {
        return [
            'Chamber 1',
            'Chamber 2',
            'Chamber 3',
            'Chamber 4',
        ];
    }

    private function getRandomData(): array
    {
        return array_map(fn () => rand(10, 100), $this->labels);
    }

    public function render()
    {
        return view('livewire.charts.sample-pie-chart');
    }
}
