<?php

namespace App\Livewire\Presets;

use App\Models\Preset;
use Livewire\Component;
use Illuminate\Support\Str;

class Create extends Component
{
    public $name;
    public $description;
    public $version = '1.0';
    public $author;
    public $status = 'Draft';
    public $selectedStepIndex = 0;
    public $commands = [];

    public $controllerCommands = [
        'tec' => [
            'setpoint' => 'float',
            'enable' => 'int',
        ],
        'stirrer' => [
            'speed' => 'int',
            'stop' => 'string',
        ],
        'microvalve' => [
            'set' => 'string',
            'open' => 'int',
            'close' => 'int',
        ],
        'pump' => [
            'init' => 'string',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'string',
        ],
    ];

    public function mount()
    {
        $this->addCommand();
    }

    public function addCommand()
    {
        $this->commands[] = [
            'controller' => '',
            'action' => '',
            'value' => '',
            'type' => 'string',
            'delay' => 5,
            'retry_count' => 0,
            'timeout' => 30
        ];
        $this->selectedStepIndex = count($this->commands) - 1;
    }

    public function selectStep($index)
    {
        $this->selectedStepIndex = $index;
    }

    public function updatedCommands($value, $key)
    {
        // Format: commands.{index}.{property}
        $parts = explode('.', $key);
        if (count($parts) < 3)
            return;

        $index = $parts[1];
        $property = $parts[2];

        if ($property === 'controller') {
            $this->commands[$index]['action'] = '';
            $this->commands[$index]['type'] = 'string';
            $this->commands[$index]['value'] = '';
        }

        if ($property === 'action') {
            $controller = $this->commands[$index]['controller'];
            $action = $value;

            if (isset($this->controllerCommands[$controller][$action])) {
                $this->commands[$index]['type'] = $this->controllerCommands[$controller][$action];
            }
        }
    }

    public function removeCommand($index)
    {
        unset($this->commands[$index]);
        $this->commands = array_values($this->commands);

        if ($this->selectedStepIndex >= count($this->commands)) {
            $this->selectedStepIndex = max(0, count($this->commands) - 1);
        }
    }

    public function save()
    {
        \Illuminate\Support\Facades\Log::info('Preset save method called', [
            'name' => $this->name,
            'commands_count' => count($this->commands)
        ]);
        $this->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'commands' => 'required|array|min:1',
            'commands.*.controller' => 'required',
            'commands.*.action' => 'required',
        ]);

        // Sanitize and cast values
        foreach ($this->commands as $index => $command) {
            $type = $command['type'] ?? 'string';
            $value = $command['value'];

            if ($type === 'int') {
                $this->commands[$index]['value'] = (int) $value;
            } elseif ($type === 'float') {
                $this->commands[$index]['value'] = (float) $value;
            } elseif ($type === 'bool') {
                $this->commands[$index]['value'] = (bool) $value;
            }
        }

        Preset::create([
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
            'commands' => $this->commands,
            'author' => $this->author ?? \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'status' => $this->status,
        ]);

        session()->flash('message', 'Preset created successfully.');
        return redirect()->route('presets.index');
    }

    public function render()
    {
        return view('livewire.presets.create');
    }
}
