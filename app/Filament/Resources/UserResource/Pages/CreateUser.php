<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\Concerns\GuardsAssignableUserRole;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use GuardsAssignableUserRole;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->guardAssignableRoleInData($data);
    }
}

