<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolSettingResource\Pages;
use App\Models\SchoolSetting;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SchoolSettingResource extends Resource
{
    protected static ?string $model = SchoolSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('school_name')
                    ->required(),
                TextInput::make('radius_meters')
                    ->numeric()
                    ->label('Radius (Meter)')
                    ->default(50)
                    ->helperText('Jarak toleransi lokasi dari titik pusat'),
                Grid::make(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->required()
                            ->numeric(),
                        TextInput::make('longitude')
                            ->required()
                            ->numeric(),
                    ]),
                Placeholder::make('Tutorial')
                    ->content('Buka Google Maps, klik kanan pada lokasi sekolah, copy angka Lat/Long dan paste di sini.'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_name')
                    ->label('Nama Sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('radius_meters')
                    ->label('Radius (Meter)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolSettings::route('/'),
            'create' => Pages\CreateSchoolSetting::route('/create'),
            'edit' => Pages\EditSchoolSetting::route('/{record}/edit'),
        ];
    }
}
