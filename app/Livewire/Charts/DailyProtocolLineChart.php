<?php

namespace App\Livewire\Charts;

use Livewire\Component;

class DailyProtocolLineChart extends Component
{
    public array $dataset = [];
    public array $labels = [];

    public function mount()
    {
        $this->labels = $this->getLabels();

        $this->dataset = [
            [
                'label' => 'Daily Total Protocol Made',
                'backgroundColor' => 'rgba(200,30,97,255)',
                'borderColor' => 'rgba(200,30,97,255)',
                'data' => $this->getRandomData(),
            ],
        ];
    }

    private function getLabels()
    {
        $labels = [];
        for ($i = 0; $i < 12; $i++) {
            $labels[] = now()->subDay($i)->format('d');
        } 
        return array_reverse($labels);
    }

    private function getRandomData()
    {
        $data = [];
        for ($i = 0; $i < count($this->getLabels()); $i++) {
            $data[] = rand(10, 100);
        }
        return $data;
    }
    public function render()
    {
        
        return view('livewire.charts.daily-protocol-line-chart');
    }
}
