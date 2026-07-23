<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin User
        User::firstOrCreate(
            ['email' => 'admin@pillarproperty.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Seed default branding settings
        Setting::set('company_name', 'Pillar Property Management', 'branding', 'string');
        Setting::set('support_email', 'support@pillarproperty.com', 'general', 'string');
        Setting::set('support_phone', '(555) 234-5678', 'general', 'string');
        Setting::set('maintenance_emergency_number', '(555) 911-0000', 'general', 'string');

        // Seed Owners
        $owner1 = Owner::create([
            'name' => 'Eleanor Vance',
            'email' => 'eleanor@vanceholdings.com',
            'phone' => '(555) 101-2020',
            'password' => Hash::make('password'),
            'portal_enabled_at' => now(),
        ]);

        $owner2 = Owner::create([
            'name' => 'Marcus Brody',
            'email' => 'marcus@brodyrealestate.com',
            'phone' => '(555) 303-4040',
            'password' => Hash::make('password'),
            'portal_enabled_at' => now(),
        ]);

        // Seed Properties
        $prop1 = Property::create([
            'owner_id' => $owner1->id,
            'name' => 'Oakwood Apartments',
            'address' => '742 Evergreen Terrace',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62704',
            'type' => 'multi_unit',
            'status' => 'active',
        ]);

        $prop2 = Property::create([
            'owner_id' => $owner1->id,
            'name' => 'Highland Luxury Home',
            'address' => '10880 Wilshire Blvd',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip' => '60611',
            'type' => 'single_family',
            'status' => 'active',
        ]);

        $prop3 = Property::create([
            'owner_id' => $owner2->id,
            'name' => 'Downtown Plaza Plaza',
            'address' => '500 N Michigan Ave',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip' => '60611',
            'type' => 'commercial',
            'status' => 'active',
        ]);

        // Seed Units
        $unit1 = Unit::create([
            'property_id' => $prop1->id,
            'name' => 'Unit 1A',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'sqft' => 950,
            'status' => 'occupied',
        ]);

        $unit2 = Unit::create([
            'property_id' => $prop1->id,
            'name' => 'Unit 1B',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'sqft' => 700,
            'status' => 'occupied',
        ]);

        $unit3 = Unit::create([
            'property_id' => $prop1->id,
            'name' => 'Unit 2A',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'sqft' => 1200,
            'status' => 'vacant',
        ]);

        $unit4 = Unit::create([
            'property_id' => $prop2->id,
            'name' => 'Main House',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'sqft' => 2800,
            'status' => 'occupied',
        ]);

        // Seed Tenants
        $tenant1 = Tenant::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@skynet-resistance.org',
            'phone' => '(555) 777-8888',
            'password' => Hash::make('password'),
            'portal_enabled_at' => now(),
        ]);

        $tenant2 = Tenant::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '(555) 444-5555',
            'password' => Hash::make('password'),
            'portal_enabled_at' => now(),
        ]);

        $tenant3 = Tenant::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '(555) 666-9999',
            'password' => Hash::make('password'),
            'portal_enabled_at' => now(),
        ]);

        // Seed Leases
        $lease1 = Lease::create([
            'unit_id' => $unit1->id,
            'tenant_id' => $tenant1->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'rent_amount' => 1800.00,
            'due_day' => 1,
            'security_deposit' => 1800.00,
            'status' => 'active',
        ]);

        $lease2 = Lease::create([
            'unit_id' => $unit2->id,
            'tenant_id' => $tenant2->id,
            'start_date' => '2026-02-01',
            'end_date' => '2027-01-31',
            'rent_amount' => 1400.00,
            'due_day' => 1,
            'security_deposit' => 1400.00,
            'status' => 'active',
        ]);

        $lease3 = Lease::create([
            'unit_id' => $unit4->id,
            'tenant_id' => $tenant3->id,
            'start_date' => '2025-09-01',
            'end_date' => '2026-08-31',
            'rent_amount' => 3200.00,
            'due_day' => 1,
            'security_deposit' => 3200.00,
            'status' => 'ending_soon',
        ]);

        // Seed Rent Payments
        RentPayment::create([
            'lease_id' => $lease1->id,
            'amount' => 1800.00,
            'due_date' => '2026-07-01',
            'paid_date' => '2026-07-01',
            'status' => 'paid',
            'method_note' => 'Zelle Transfer #98231',
        ]);

        RentPayment::create([
            'lease_id' => $lease1->id,
            'amount' => 1800.00,
            'due_date' => '2026-08-01',
            'paid_date' => null,
            'status' => 'upcoming',
            'method_note' => null,
        ]);

        RentPayment::create([
            'lease_id' => $lease2->id,
            'amount' => 1400.00,
            'due_date' => '2026-07-01',
            'paid_date' => '2026-07-05',
            'status' => 'late',
            'method_note' => 'Check #1042',
        ]);

        // Seed Vendors
        $vendor1 = Vendor::create([
            'name' => 'Apex Plumbing Solutions',
            'trade' => 'Plumber',
            'phone' => '(555) 888-1234',
            'email' => 'contact@apexplumbing.com',
        ]);

        $vendor2 = Vendor::create([
            'name' => 'Sparky Electricians Inc.',
            'trade' => 'Electrician',
            'phone' => '(555) 888-5678',
            'email' => 'info@sparkyelectric.com',
        ]);

        // Seed Maintenance Requests
        MaintenanceRequest::create([
            'unit_id' => $unit1->id,
            'tenant_id' => $tenant1->id,
            'vendor_id' => $vendor1->id,
            'category' => 'plumbing',
            'description' => 'Kitchen sink tap leaking persistently under cabinet.',
            'urgency' => 'routine',
            'status' => 'in_progress',
            'cost' => 150.00,
        ]);

        MaintenanceRequest::create([
            'unit_id' => $unit4->id,
            'tenant_id' => $tenant3->id,
            'vendor_id' => $vendor2->id,
            'category' => 'electrical',
            'description' => 'Main circuit breaker keeps tripping when air conditioner turns on.',
            'urgency' => 'urgent',
            'status' => 'assigned',
            'cost' => 300.00,
        ]);
    }
}
