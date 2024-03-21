<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\User\App\Enums\UserStatus;
use Modules\User\App\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort  = 1;



    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->maxLength(255)
                            ->required(),

                        Select::make('role')
                            ->label(__('Role'))
                            ->relationship('roles', 'name')
                            ->required()
                            ->native(false),


                        TextInput::make('first_name')
                            ->label(__('First name'))
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('last_name')
                            ->label(__('Last name'))
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('email')
                            ->label(__('Email address'))
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options(UserStatus::class)
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?User $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?User $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
