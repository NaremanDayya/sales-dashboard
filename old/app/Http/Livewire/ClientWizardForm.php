<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\User;
use App\Notifications\NewClientNotification;

class ClientWizardForm extends Component
{
    use WithFileUploads;

    public $step = 1;

    public $company_name, $company_logo;
    public $address, $contact_person, $contact_position;
    public $phone, $interest_status;

    public function render()
    {
        return view('livewire.client-wizard-form');
    }

    public function next()
    {
        $rules = $this->getStepRules();

        $this->validate($rules);

        if ($this->step < 7) {
            $this->step++;
        } else {
            $this->submit();
        }
    }

    protected function getStepRules()
    {
        return match ($this->step) {
            1 => ['company_name' => 'required|string|max:255'],
            2 => ['company_logo' => 'nullable|image|max:2048'],
            3 => ['address' => 'required|string'],
            4 => ['contact_person' => 'required|string'],
            5 => ['contact_position' => 'required|string'],
            6 => ['phone' => 'required|string'],
            7 => ['interest_status' => 'required|in:interested,not_interested,pending'],
            default => [],
        };
    }

    public function submit()
    {
        $validated = [
            'company_name'      => $this->company_name,
            'address'           => $this->address,
            'contact_person'    => $this->contact_person,
            'contact_position'  => $this->contact_position,
            'phone'             => $this->phone,
            'interest_status'   => $this->interest_status,
        ];

        // Handle file upload
        if ($this->company_logo) {
            $validated['company_logo'] = $this->company_logo->store('logos', 'public');
        }

        // Add sales rep ID
        $validated['sales_rep_id'] = Auth::id();

        // Saudi phone number handling
        $cleanedNumber = $this->generateSaudiNumber($validated['phone']);
        $validated['saudi_number'] = $cleanedNumber;
        $validated['whatsapp_link'] = 'https://wa.me/' . ltrim($cleanedNumber, '+');

        // Save client
        $client = Client::create($validated);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewClientNotification($client));
        }

        session()->flash('success', 'Client added successfully.');
        return redirect()->route('sales-reps.clients.index', Auth::id());
    }

    private function generateSaudiNumber($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (Str::startsWith($digits, '05')) {
            $digits = '966' . substr($digits, 1);
        } elseif (Str::startsWith($digits, '5')) {
            $digits = '966' . $digits;
        } elseif (!Str::startsWith($digits, '966')) {
            $digits = '966' . ltrim($digits, '0');
        }

        return '+' . $digits;
    }
}
