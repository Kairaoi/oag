<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;

class AdminDashboard extends Page
{
    protected static ?string $title = 'Admin Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected function getTableQuery()
    {
        // Customize this query to fetch records from your database
        return \App\Models\User::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\Text::make('Name')->sortable(),
            Tables\Columns\Text::make('Email'),
            // Add more columns as needed
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\CreateAction::make(),
            // Add other actions like edit, delete if necessary
        ];
    }
}
