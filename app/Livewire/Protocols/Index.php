<?php

namespace App\Livewire\Protocols;

use App\Models\Protocol;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 10;

    #[Computed]
    public function predefinedProtocols()
    {
        return [
            [
                'name' => 'Protocol 1',
                'title' => 'THIOL-MALEIMIDE CONJUGATION',
                'description' => 'This protocol provides a comprehensive framework for implementing secure and efficient data transfer mechanisms across distributed systems.',
                'img_url' => 'https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw',
            ],
            [
                'name' => 'Protocol 2',
                'title' => 'SORTASE ENZYME CONJUGATION',
                'description' => 'A lightweight protocol designed for real-time communication between client and server applications with minimal overhead and latency.',
                'img_url' => 'https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw',
            ],
            [
                'name' => 'Protocol 3',
                'title' => 'LYSINE-NHS ESTER CONJUGATION',
                'description' => 'An advanced protocol that enables secure end-to-end encryption for sensitive data transmission across untrusted networks.',
                'img_url' => 'https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw',
            ],
        ];
    }

    #[Computed]
    public function protocols()
    {
        return Protocol::paginate($this->perPage);
    }

    public function createProcess($id)
    {
        return to_route('protocols.processing', [
            'protocol' => $id,
            'uid' => 'proc_' . uniqid()
        ]);
    }

    public function delete($id)
    {
        Protocol::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.protocols.index', [
            'predefinedProtocols' => $this->predefinedProtocols,
            'protocols' => $this->protocols,
        ]);
    }
}
