<?php

namespace App\Livewire\Admin\Users;

use App\Enum\Can;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read Collection|User[] $users
 * @property-read array $headers
 */
class Index extends Component
{
    public ?string $search = null;
    public array $search_permissions = [];
    public Collection $permissionsToSearch;
    public bool $search_trash = false;
    public string $sortDirection = 'asc';
    public string $sortColumnBy = 'id';

    public function mount(): void
    {
        $this->authorize(Can::BE_AN_ADMIN->value);
        $this->filterPermissions();
    }

    public function render():View
    {
        return view('livewire.admin.users.index');
    }

    #[Computed]
    public function users(): Collection
    {
        $this->validate(['search_permissions' => 'exists:permissions,id']);

        return User::query()
            ->when(
                $this->search,
                fn (Builder $query) => $query->where(
                    DB::raw('lower(name)'),
                    'like',
                    '%' . strtolower($this->search) . '%'
                )->orWhere(
                    'email',
                    'like',
                    '%' . strtolower($this->search) . '%'
                )
            )
            ->when($this->search_permissions,
                fn (Builder $q) => $q->whereHas('permissions', function (Builder $query) {
                    $query->whereIn('id', $this->search_permissions);
                })
            )
            ->when(
                $this->search_trash,
                fn (Builder $query) => $query->onlyTrashed()
            )
            ->orderBy($this->sortColumnBy, $this->sortDirection)
            ->get();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'permissions', 'label' => 'Permissions']
        ];
    }

    #[Computed]
    public function filterPermissions(?string $value = null): void
    {
         $this->permissionsToSearch =  Permission::query()
            ->when($value, fn (Builder $query) => $query->where('name', 'like', "%{$value}%"))
            ->orderBy('key')
            ->get();
    }
}
