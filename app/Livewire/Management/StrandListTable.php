<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use App\Models\Strand;

#[Lazy]
class StrandListTable extends Component
{
    public string $search = '';

    public function placeholder()
    {
        return view('livewire.management.strand-list-table-placeholder');
    }

    #[On('strand-list-updated')]
    public function render()
    {
        $strands = Strand::orderBy('name')
            ->when($this->search !== '', function ($query) {
                $query->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->get();

        return view('livewire.management.strand-list-table', [
            'strands' => $strands,
        ]);
    }
}
