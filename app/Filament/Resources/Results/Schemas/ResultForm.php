<?php

namespace App\Filament\Resources\Results\Schemas;

use App\Helpers\Number;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class ResultForm
{
    public static function schema(): array
    {
        return [
            Grid::make(['default' => 1, 'md' => 5])
                ->columnSpan('full')
                ->schema([
                    // Left column: stacked sections
                    Grid::make(['default' => 1])
                        ->schema([
                            Section::make(__('results.result_overview'))->schema([
                                TextInput::make('id')
                                    ->label(__('general.id')),
                                TextInput::make('created_at')
                                    ->label(__('general.created_at'))
                                    ->afterStateHydrated(function (TextInput $component, $state) {
                                        $component->state(Carbon::parse($state)
                                            ->timezone(config('app.display_timezone'))
                                            ->format(config('app.datetime_format')));
                                    }),
                                TextInput::make('download')
                                    ->label(__('general.download'))
                                    ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : '')),
                                TextInput::make('upload')
                                    ->label(__('general.upload'))
                                    ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : '')),
                                TextInput::make('ping')
                                    ->label(__('general.ping'))
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                TextInput::make('data.packetLoss')
                                    ->label(__('results.packet_loss'))
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '').' %'),
                            ])->columns(2)->columnSpan('full'),

                            Section::make(__('general.download_latency'))
                                ->schema([
                                    TextInput::make('data.download.latency.jitter')->label(__('general.jitter'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.download.latency.high')->label(__('general.high'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.download.latency.low')->label(__('general.low'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.download.latency.iqm')->label(__('results.iqm'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                ])
                                ->columns(2)
                                ->collapsed()
                                ->columnSpan('full'),

                            Section::make(__('general.upload_latency'))
                                ->schema([
                                    TextInput::make('data.upload.latency.jitter')->label(__('general.jitter'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.upload.latency.high')->label(__('general.high'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.upload.latency.low')->label(__('general.low'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.upload.latency.iqm')->label(__('results.iqm'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                ])
                                ->columns(2)
                                ->collapsed()
                                ->columnSpan('full'),

                            Section::make(__('results.ping_details'))
                                ->schema([
                                    TextInput::make('data.ping.jitter')->label(__('general.jitter'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.ping.low')->label(__('general.low'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                    TextInput::make('data.ping.high')->label(__('general.high'))
                                        ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                                ])
                                ->columns(2)
                                ->collapsed()
                                ->columnSpan('full'),

                            Textarea::make('data.message')
                                ->label(__('general.message'))
                                ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow">Error Messages</a>'))
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(['md' => 3]),

                    // Right column: Server & Metadata
                    Section::make(__('results.server_&_metadata'))->schema([
                        Placeholder::make('service')
                            ->label(__('results.service'))
                            ->content(fn (Result $result): string => $result->service->getLabel()),
                        Placeholder::make('server_name')
                            ->label(__('results.server_name'))
                            ->content(fn (Result $result): ?string => $result->server_name),
                        Placeholder::make('server_id')
                            ->label(__('results.server_id'))
                            ->content(fn (Result $result): ?string => $result->server_id),
                        Placeholder::make('isp')
                            ->label(__('results.isp'))
                            ->content(fn (Result $result): ?string => $result->isp),
                        Placeholder::make('server_location')
                            ->label(__('results.server_location'))
                            ->content(fn (Result $result): ?string => $result->server_location),
                        Placeholder::make('server_host')
                            ->label(__('results.server_host'))
                            ->content(fn (Result $result): ?string => $result->server_host),
                        Placeholder::make('comment')
                            ->label(__('general.comment'))
                            ->content(fn (Result $result): ?string => $result->comments),
                        Checkbox::make('scheduled')
                            ->label(__('results.scheduled')),
                        Checkbox::make('healthy')
                            ->label(__('general.healthy')),
                    ])->columns(1)->columnSpan(['md' => 2]),
                ]),
        ];
    }
}
