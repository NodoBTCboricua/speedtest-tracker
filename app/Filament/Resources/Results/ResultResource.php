<?php

namespace App\Filament\Resources\Results;

use App\Filament\Resources\Results\Pages\ListResults;
use App\Filament\Resources\Results\Schemas\ResultForm;
use App\Filament\Resources\Results\Tables\ResultTable;
use App\Models\Result;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-table';

    protected static \UnitEnum|string|null $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('results.title');
    }

    public static function getModelLabel(): string
    {
        return __('results.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('results.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ResultForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ResultTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResults::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'data->interface->externalIp', 'data->server->name', 'data->server->id'];
    }
}
