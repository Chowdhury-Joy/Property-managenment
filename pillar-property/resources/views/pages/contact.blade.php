<x-layouts.public title="Contact Us | Pillar Property Management" metaDescription="Get in touch with Pillar Property Management for inquiries, support, or partnership opportunities.">
    <section class="py-24 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">Contact Us</h1>
            <p class="text-xl text-gray-500 leading-relaxed">
                We're here to help. Reach out to our team with any questions about our property management services.
            </p>
        </div>
    </section>

    <section class="py-24 bg-gray-50">
        <div class="max-w-5xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16">
            
            <!-- Contact Info -->
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-8 tracking-tight">Get in Touch</h3>
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-[var(--brand-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-1">Phone</h4>
                            <p class="text-gray-600">{{ \App\Models\Setting::get('contact_phone', '(555) 123-4567') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-[var(--brand-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-1">Email</h4>
                            <p class="text-gray-600">{{ \App\Models\Setting::get('contact_email', 'hello@pillarpm.demo') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-[var(--brand-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-1">Office</h4>
                            <p class="text-gray-600">123 Property Way, Suite 100<br>Real Estate City, ST 12345</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            @livewire('contact-form')

        </div>
    </section>
</x-layouts.public>
