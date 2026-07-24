<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Livewire\Component;

class ContactForm extends Component
{
    public $first_name = '';

    public $last_name = '';

    public $email = '';

    public $message = '';

    public $successMessage = false;

    protected function rules()
    {
        return [
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'email' => 'required|email',
            'message' => 'required|min:10|max:2000',
        ];
    }

    public function submit()
    {
        $this->validate();

        ContactMessage::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'message' => $this->message,
            'status' => 'new',
        ]);

        $this->successMessage = true;
        $this->reset(['first_name', 'last_name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
