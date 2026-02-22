<?php

namespace App\Livewire\Charts;

use Livewire\Component;

class ChamberBarChart extends Component
{
    public array $dataset = [];
    public array $labels = [];

    public function mount()
    {
        $this->labels = $this->getLabels();

        $this->dataset = [
            [
                'label' => 'Volume in each chamber',
                'backgroundColor' => 'rgba(200,64,97,255)',
                'borderColor' => 'rgba(200,64,97,255)',
                'data' => $this->getRandomData(),
            ],
        ];
    }

    private function getLabels()
    {
        $labels = [];
        for ($i = 1; $i <= 4; $i++) {
            $labels[] = "Chamber $i";
        } 
        return $labels;
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
        return view('livewire.charts.chamber-bar-chart');
    }
}
