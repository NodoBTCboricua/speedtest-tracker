<?php

namespace App\Filament\Resources\PingTargets;

use App\Filament\Resources\PingTargets\Pages\CreatePingTarget;
use App\Filament\Resources\PingTargets\Pages\EditPingTarget;
use App\Filament\Resources\PingTargets\Pages\ListPingTargets;
use App\Filament\Resources\PingTargets\Schemas\PingTargetForm;
use App\Filament\Resources\PingTargets\Tables\PingTargetTable;
use App\Models\PingTarget;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PingTargetResource extends Resource
{
    protected static ?string $model = PingTarget::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-broadcast';

    protected static \UnitEnum|string|null $navigationGroup = 'Monitor de Ping';

    public static function getNavigationLabel(): string
    {
        return __('ping.ping_targets');
    }

    public static function getModelLabel(): string
    {
        return __('ping.ping_target');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ping.ping_targets');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(PingTargetForm::schema());
    }

    public static function table(Table $table): Table
    {
        return PingTargetTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPingTargets::route('/'),
            'create' => CreatePingTarget::route('/create'),
            'edit' => EditPingTarget::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'host'];
    }
}
