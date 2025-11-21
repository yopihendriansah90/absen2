<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->label('Hari')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ])
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Masuk')
                    ->seconds(false)
                    ->time('H:i')
                    ->required(),

                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Pulang')
                    ->seconds(false)
                    // format 24 jam
                    ->format('H:i')
                    // ->displayFormat('H:i')
                    ->required(),

                Forms\Components\Toggle::make('is_wfh')
                    ->label('WFH?')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                // nomor urut
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),

                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->formatStateUsing(fn (?int $state): string => match ((int) $state) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                        default => 'Tidak ditemukan',

                    })
                    ->sortable(),
                // Format H:i akan menampilkan 07:00 (24 jam)
                TextColumn::make('start_time')
                    ->label('Jam Masuk')
                    ->suffix(' WIB')
                    ->time('H:i'),
                TextColumn::make('end_time')
                    ->label('Jam Pulang')
                    ->suffix(' WIB')
                    ->time('H:i'),
                TextColumn::make('is_wfh')
                    ->label('WFH?')
                    ->formatStateUsing(fn (?bool $state): string => $state ? 'Ya' : 'Tidak'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Jadwal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
