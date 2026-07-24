<?php

namespace App\Filament\Tenant\Pages;

use Filament\Schemas\Schema;
use App\Models\MaintenanceRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SubmitMaintenanceRequest extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected string $view = 'filament.tenant.pages.submit-maintenance-request';

    protected static ?string $navigationLabel = 'Submit Request';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->options([
                        'plumbing' => 'Plumbing', 'electrical' => 'Electrical',
                        'appliance' => 'Appliance', 'hvac' => 'HVAC', 'other' => 'Other',
                    ])->required(),
                Select::make('urgency')
                    ->options([
                        'routine' => 'Routine', 'urgent' => 'Urgent', 'emergency' => 'Emergency',
                    ])->default('routine')->required(),
                Textarea::make('description')->required()->rows(4)->label('Describe the issue'),
                FileUpload::make('photo_path')->image()->directory('tenant-maintenance-photos')->label('Upload Photo (Optional)'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $tenant = auth()->guard('tenant')->user();
        $activeLease = $tenant ? $tenant->activeLease : null;

        if (! $activeLease) {
            Notification::make()->title('No active lease found.')->danger()->send();

            return;
        }

        $data = $this->form->getState();

        MaintenanceRequest::create([
            'unit_id' => $activeLease->unit_id,
            'tenant_id' => $tenant->id,
            'category' => $data['category'],
            'urgency' => $data['urgency'],
            'description' => $data['description'],
            'photo_path' => $data['photo_path'] ?? null,
            'status' => 'submitted',
        ]);

        Notification::make()
            ->title('Request Submitted')
            ->body('Our team has been notified and will review your request shortly.')
            ->success()
            ->send();

        $this->form->fill(); // Reset form
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit Request')
                ->action('submit'),
        ];
    }
}
