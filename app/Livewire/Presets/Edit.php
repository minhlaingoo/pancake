<?php

namespace App\Livewire\Presets;

use App\Models\Preset;
use Livewire\Component;
use Illuminate\Support\Str;

class Edit extends Component
{
    public $preset;
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

    public function mount(Preset $preset)
    {
        $this->preset = $preset;
        $this->name = $preset->name;
        $this->version = $preset->version ?? '1.0';
        $this->description = $preset->description;
        $this->author = $preset->author;
        $this->status = $preset->status ?? 'Draft';
        $this->commands = $preset->commands ?? [];

        if (empty($this->commands)) {
            $this->addCommand();
        }
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
        // Format: {index}.{property} (Livewire 3 passes relative key)
        $parts = explode('.', $key);
        if (count($parts) < 2)
            return;

        $index = $parts[0];
        $property = $parts[1];

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
        $this->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'commands' => 'required|array|min:1',
            'commands.*.controller' => 'required',
            'commands.*.action' => 'required',
        ]);

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

        $this->preset->update([
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
            'commands' => $this->commands,
            'author' => $this->author ?? \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'status' => $this->status,
        ]);

        session()->flash('message', 'Preset updated successfully.');
        return redirect()->route('presets.index');
    }

    public function render()
    {
        return view('livewire.presets.edit');
    }
}
