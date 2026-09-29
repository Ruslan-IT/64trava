<?php

namespace App\Filament\Pages;

use App\Models\FooterSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageFooter extends Page
{
    protected static ?string $navigationLabel = 'Футер';

    protected static ?string $title = 'Футер';

    protected static ?string $slug = 'footer';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public ?array $data = [];

    public function mount(): void
    {
        $record = FooterSetting::current();

        $this->form->fill([
            'logo' => $record->logo,
            'description' => $record->description,
            'contact_title' => $record->contact_title,
            'contact_label' => $record->contact_label,
            'contact_url' => $record->contact_url,
            'groups' => $record->groups,
            'socials' => $record->socials,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->model(FooterSetting::current())
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Логотип и описание')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Логотип')
                            ->image()
                            ->disk('public')
                            ->directory('images/footer')
                            ->visibility('public')
                            ->imagePreviewHeight('90')
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Текст под логотипом')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Свяжитесь с нами')
                    ->schema([
                        TextInput::make('contact_title')
                            ->label('Заголовок')
                            ->maxLength(255),

                        TextInput::make('contact_label')
                            ->label('Текст ссылки')
                            ->maxLength(255),

                        TextInput::make('contact_url')
                            ->label('URL')
                            ->maxLength(2048)
                            ->helperText('Для почты укажите mailto:info@example.ru'),
                    ]),

                Section::make('Группы ссылок')
                    ->schema([
                        Repeater::make('groups')
                            ->label('Группы')
                            ->reorderable()
                            ->addActionLabel('Добавить группу')
                            ->itemLabel(fn (array $state): string => filled($state['title'] ?? null) ? $state['title'] : 'Без заголовка')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Заголовок')
                                    ->maxLength(255),

                                Repeater::make('links')
                                    ->label('Ссылки')
                                    ->reorderable()
                                    ->defaultItems(3)
                                    ->addActionLabel('Добавить ссылку')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('url')
                                            ->label('URL')
                                            ->required()
                                            ->maxLength(2048),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),

                Section::make('Социальные сети')
                    ->schema([
                        TextInput::make('socials.telegram')
                            ->label('Telegram')
                            ->maxLength(2048),

                        TextInput::make('socials.vk')
                            ->label('VK')
                            ->maxLength(2048),

                        TextInput::make('socials.youtube')
                            ->label('YouTube')
                            ->maxLength(2048),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Сохранить')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $logo = $state['logo'] ?? null;

        if (is_array($logo)) {
            $logo = $logo[0] ?? null;
        }

        FooterSetting::current()->update([
            'logo' => $logo,
            'description' => $state['description'] ?? '',
            'contact_title' => $state['contact_title'] ?? '',
            'contact_label' => $state['contact_label'] ?? '',
            'contact_url' => $state['contact_url'] ?? '',
            'groups' => array_values($state['groups'] ?? []),
            'socials' => [
                'telegram' => $state['socials']['telegram'] ?? '#',
                'vk' => $state['socials']['vk'] ?? '#',
                'youtube' => $state['socials']['youtube'] ?? '#',
            ],
        ]);

        Notification::make()
            ->success()
            ->title('Футер сохранён')
            ->send();
    }
}
