<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?int $navigationSort = 1; // Puts it at the top of the sidebar

    public ?array $data = [];

    public function mount(): void
    {
        // Load existing settings into the form
        $this->form->fill([
            'company_name' => Setting::get('company_name', 'Pillar Property Management'),
            'logo' => Setting::get('logo'),
            'favicon' => Setting::get('favicon'),
            'primary_color' => Setting::get('primary_color', '#1e3a8a'), // Default blue-900
            'contact_phone' => Setting::get('contact_phone', '(555) 123-4567'),
            'contact_email' => Setting::get('contact_email', 'hello@pillarpm.demo'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Branding & Theme')
                    ->description('These settings control the look and feel of the public site and admin portals.')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        FileUpload::make('logo')
                            ->image()
                            ->directory('settings/logos')
                            ->maxSize(2048)
                            ->hint('Recommended: Transparent PNG, max width 400px'),
                        FileUpload::make('favicon')
                            ->image()
                            ->directory('settings/favicons')
                            ->maxSize(1024)
                            ->hint('Recommended: 32x32 or 64x64 PNG'),
                        ColorPicker::make('primary_color')
                            ->hex()
                            ->required()
                            ->hint('Changes the primary brand color across the site.'),
                    ])->columns(2),

                Section::make('Contact Information')
                    ->description('Displayed in the public footer and contact pages.')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save each setting to the database
        foreach ($data as $key => $value) {
            // Only update if the value is not null (prevents wiping out existing files if not re-uploaded)
            if ($value !== null) {
                Setting::set($key, $value, 'general', 'string');
            }
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->body('Your rebranding changes are now live.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),
        ];
    }
}
