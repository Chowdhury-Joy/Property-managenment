<x-layouts.public title="Contact">
    <section class="py-16 max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Get In Touch</h1>
        <p class="text-gray-600 mb-8">Have questions? We'd love to hear from you.</p>
        <div class="text-lg bg-white p-8 rounded-lg shadow-sm max-w-md mx-auto">
            <p class="font-semibold mb-2">Phone: {{ \App\Models\Setting::get('contact_phone', '(555) 123-4567') }}</p>
            <p class="font-semibold">Email: {{ \App\Models\Setting::get('contact_email', 'hello@pillarpm.demo') }}</p>
        </div>
    </section>
</x-layouts.public>
