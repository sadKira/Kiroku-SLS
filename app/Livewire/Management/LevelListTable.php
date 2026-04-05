<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use App\Models\InstructionalLevel;

#[Lazy]
class LevelListTable extends Component
{
    public string $search = '';

    public function placeholder()
    {
        return view('livewire.management.level-list-table-placeholder');
    }

    #[On('level-list-updated')]
    public function render()
    {
        $levels = InstructionalLevel::orderBy('name')
            ->when($this->search !== '', function ($query) {
                $query->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->get();

        return view('livewire.management.level-list-table', [
            'levels' => $levels,
        ]);
    }
}
