<?php

namespace App\Livewire\Charts;

use Livewire\Component;

class DailyChamberVolumeUsageChart extends Component
{
    public array $dataset = [];
    public array $labels = [];

    public function mount()
    {
        $this->labels = $this->getLabels();

        $this->dataset = [
            [
                'label' => 'Chamber A',
                'backgroundColor' => 'rgba(15,64,97,1)',
                'borderColor' => 'rgba(15,64,97,1)',
                'fill' => false,
                'tension' => 0.4,
                'data' => $this->getRandomData(),
            ],
            [
                'label' => 'Chamber B',
                'backgroundColor' => 'rgba(0,123,255,1)',
                'borderColor' => 'rgba(0,123,255,1)',
                'fill' => false,
                'tension' => 0.4,
                'data' => $this->getRandomData(),
            ],
            [
                'label' => 'Chamber C',
                'backgroundColor' => 'rgba(40,167,69,1)',
                'borderColor' => 'rgba(40,167,69,1)',
                'fill' => false,
                'tension' => 0.4,
                'data' => $this->getRandomData(),
            ],
            [
                'label' => 'Chamber D',
                'backgroundColor' => 'rgba(255,193,7,1)',
                'borderColor' => 'rgba(255,193,7,1)',
                'fill' => false,
                'tension' => 0.4,
                'data' => $this->getRandomData(),
            ],
        ];
    }

    private function getLabels()
    {
        $labels = [];
        for ($i = 0; $i < 12; $i++) {
            $labels[] = now()->subDays($i)->format('d');
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
        return view('livewire.charts.daily-chamber-volume-usage-chart');
    }
}
