<div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
    @if($successMessage)
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Request Received!</h3>
            <p class="text-gray-600">Thanks for reaching out. A member of our team will review your property details and call you within 24 hours to discuss your free rental analysis.</p>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                    <input type="text" wire:model="name" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" wire:model="email" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                <input type="text" wire:model="phone" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Property Address</label>
                <input type="text" wire:model="property_address" placeholder="123 Main St, City, State" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                @error('property_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                    <select wire:model="property_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                        <option value="single_family">Single Family</option>
                        <option value="multi_unit">Multi-Unit (2-4)</option>
                        <option value="commercial">Commercial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Monthly Rent (or "Not sure")</label>
                    <input type="text" wire:model="current_rent" placeholder="$2,000" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Why are you considering switching managers?</label>
                <textarea wire:model="reason_for_switching" rows="3" placeholder="e.g., Current manager isn't responsive, looking for better reporting..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-900 text-white py-3 rounded-md font-bold hover:bg-blue-800 transition shadow-sm">
                Get My Free Analysis
            </button>
        </form>
    @endif
</div>
