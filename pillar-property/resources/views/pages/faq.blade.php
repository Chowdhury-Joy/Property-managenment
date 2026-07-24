<x-layouts.public title="FAQ | Pillar Property Management" metaDescription="Frequently Asked Questions about our property management services.">
    <section class="py-24 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">Frequently Asked Questions</h1>
            <p class="text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                Got questions? We've got answers. Find out everything you need to know about our services.
            </p>
        </div>
    </section>

    <section class="py-24 bg-gray-50" x-data="{ activeAccordion: null }">
        <div class="max-w-3xl mx-auto px-6">
            
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-8 py-6 text-left flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-gray-900 text-lg tracking-tight">How do you screen potential tenants?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': activeAccordion === 1}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="activeAccordion === 1" x-collapse style="display:none;">
                        <div class="px-8 pb-6 text-gray-600 leading-relaxed">
                            We perform a comprehensive background check on every adult applicant. This includes a credit check, criminal history review, eviction history, employment verification, and references from past landlords.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full px-8 py-6 text-left flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-gray-900 text-lg tracking-tight">How are maintenance requests handled?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': activeAccordion === 2}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="activeAccordion === 2" x-collapse style="display:none;">
                        <div class="px-8 pb-6 text-gray-600 leading-relaxed">
                            Tenants can submit maintenance requests 24/7 through their dedicated portal. For emergencies, we have a 24-hour hotline. We use trusted, vetted contractors for all repairs.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-8 py-6 text-left flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-gray-900 text-lg tracking-tight">When do I get paid each month?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': activeAccordion === 3}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="activeAccordion === 3" x-collapse style="display:none;">
                        <div class="px-8 pb-6 text-gray-600 leading-relaxed">
                            Rents are due on the 1st of the month. Once cleared, we initiate direct deposits to our owners by the 10th of the month, along with a detailed monthly statement.
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</x-layouts.public>
