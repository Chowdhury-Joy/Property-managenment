<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class RequestRentalAnalysis extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $property_address = '';
    public $property_type = 'single_family';
    public $current_rent = '';
    public $reason_for_switching = '';

    public $successMessage = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'phone' => 'nullable',
            'property_address' => 'required|min:5',
            'property_type' => 'required',
            'current_rent' => 'nullable',
            'reason_for_switching' => 'nullable|max:500',
        ];
    }

    public function submit()
    {
        $this->validate();

        Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'property_address' => $this->property_address,
            'property_type' => $this->property_type,
            'current_rent' => $this->current_rent,
            'reason_for_switching' => $this->reason_for_switching,
            'status' => 'new',
        ]);

        $this->successMessage = true;
        $this->reset(['name', 'email', 'phone', 'property_address', 'property_type', 'current_rent', 'reason_for_switching']);
    }

    public function render()
    {
        return view('livewire.request-rental-analysis');
    }
}
