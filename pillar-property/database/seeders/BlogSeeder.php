<?php

namespace Database\Seeders;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Top 5 Real Estate Investment Trends for 2026',
                'excerpt' => 'Discover the emerging trends that are shaping the future of real estate investing. From smart homes to sustainable building materials, stay ahead of the curve.',
                'content' => '
                    <p>The real estate market is constantly evolving, and 2026 is shaping up to be a year of significant change. As technology advances and tenant expectations shift, property owners must adapt their strategies to remain competitive.</p>
                    <h2>1. The Rise of the Smart Property</h2>
                    <p>Tenants are no longer just looking for a roof over their heads; they want a connected living experience. Smart thermostats, keyless entry systems, and integrated security are becoming standard expectations rather than premium upgrades. Integrating these technologies not only attracts higher-quality tenants but also allows for more efficient property management.</p>
                    <h2>2. Sustainable and Eco-Friendly Materials</h2>
                    <p>Sustainability is moving from a buzzword to a core requirement. Properties that feature energy-efficient appliances, solar panels, and sustainable building materials often command higher rents and experience lower vacancy rates.</p>
                    <h2>3. Shift Towards Suburban and Secondary Markets</h2>
                    <p>While urban centers remain popular, there is a continued strong migration towards suburban and secondary markets where space and affordability are greater. Investors finding opportunities in these emerging markets are seeing excellent returns.</p>
                    <h2>Conclusion</h2>
                    <p>Staying informed about these trends is crucial for any real estate investor looking to maximize their ROI and build a future-proof portfolio.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'How to Screen Tenants Like a Pro',
                'excerpt' => 'A comprehensive guide to screening tenants to ensure you find reliable, responsible renters for your investment properties.',
                'content' => '
                    <p>Finding the right tenant is arguably the most critical aspect of property management. A bad tenant can cost you thousands in missed rent, legal fees, and property damage. Here is our professional approach to tenant screening.</p>
                    <h2>Start with a Clear Set of Criteria</h2>
                    <p>Before you even begin accepting applications, establish clear, legal, and non-discriminatory criteria. This typically includes a minimum credit score, income requirements (e.g., 3x the monthly rent), and a clean background check.</p>
                    <h2>The Application Process</h2>
                    <p>Require a comprehensive application that asks for employment history, previous landlord references, and consent for background and credit checks. Do not cut corners here.</p>
                    <h2>Verify Everything</h2>
                    <p>Trust, but verify. Call previous landlords and ask specific questions about the tenant\'s behavior, timeliness with rent, and the condition in which they left the property. Verify employment directly with the employer.</p>
                    <h2>Red Flags to Watch For</h2>
                    <ul>
                        <li>Gaps in employment or rental history.</li>
                        <li>Hesitancy to provide references or consent to checks.</li>
                        <li>Pushiness or an extreme rush to move in immediately.</li>
                    </ul>
                    <p>By implementing a rigorous screening process, you protect your investment and ensure a steady, reliable income stream.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'The Hidden Costs of DIY Property Management',
                'excerpt' => 'Managing properties yourself might seem like a money-saver, but the hidden costs in time, legal risks, and maintenance can quickly add up.',
                'content' => '
                    <p>Many new real estate investors choose to manage their own properties to save on management fees. While this can work for a single property, the hidden costs often outweigh the benefits as your portfolio grows.</p>
                    <h2>The Cost of Your Time</h2>
                    <p>Property management is not a passive job. From dealing with middle-of-the-night maintenance emergencies to tracking down late rent payments, the time commitment can be immense. Calculate what your time is worth—could it be better spent acquiring new properties?</p>
                    <h2>Legal and Compliance Risks</h2>
                    <p>Landlord-tenant laws are complex and constantly changing. A single misstep in eviction proceedings, security deposit handling, or fair housing compliance can result in lawsuits that wipe out years of profit.</p>
                    <h2>Maintenance Inefficiencies</h2>
                    <p>Professional property management companies have established relationships with reliable, vetted contractors and often receive volume discounts. DIY landlords often pay retail prices and struggle to find emergency repair services promptly.</p>
                    <h2>Higher Vacancy Rates</h2>
                    <p>Professional managers know how to market properties effectively, screen tenants quickly, and turn around vacant units in days, not weeks. Every day a unit sits empty is money lost.</p>
                    <p>Partnering with a professional property management firm like Pillar Property Management ensures your investment is truly passive.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Essential Fall Maintenance Checklist for Landlords',
                'excerpt' => 'Prepare your rental properties for the colder months with this essential fall maintenance checklist.',
                'content' => '
                    <p>As the weather cools down, it is crucial to prepare your rental properties for winter. Proactive fall maintenance can prevent costly emergency repairs when the freezing temperatures hit.</p>
                    <h2>1. Inspect and Service the HVAC System</h2>
                    <p>Before the first freeze, have a professional HVAC technician inspect and service the heating system. Replace air filters and ensure everything is running efficiently.</p>
                    <h2>2. Clean Gutters and Downspouts</h2>
                    <p>Falling leaves can quickly clog gutters, leading to water backing up and potentially damaging the roof or foundation. Ensure all gutters are clear and downspouts direct water away from the property.</p>
                    <h2>3. Check for Drafts and Seal Leaks</h2>
                    <p>Inspect windows and doors for drafts. Apply weatherstripping or caulk where necessary to improve energy efficiency and keep tenants comfortable.</p>
                    <h2>4. Inspect the Roof</h2>
                    <p>Look for missing or damaged shingles. A small leak in the fall can become a major disaster under the weight of winter snow and ice.</p>
                    <h2>5. Turn Off Exterior Faucets</h2>
                    <p>Disconnect hoses and shut off exterior water valves to prevent pipes from freezing and bursting.</p>
                    <p>Regular maintenance not only protects your physical asset but also shows your tenants that you care about their living conditions, leading to higher retention rates.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Understanding the Fair Housing Act',
                'excerpt' => 'A crucial overview of the Fair Housing Act and what every landlord needs to know to stay compliant and avoid discrimination.',
                'content' => '
                    <p>The Fair Housing Act (FHA) is a federal law that prohibits discrimination in the sale, rental, and financing of housing based on race, color, national origin, religion, sex, familial status, and disability. Understanding the FHA is non-negotiable for anyone in property management.</p>
                    <h2>What Constitutes Discrimination?</h2>
                    <p>Discrimination isn\'t always obvious. It can include:</p>
                    <ul>
                        <li>Refusing to rent to someone based on a protected class.</li>
                        <li>Setting different terms, conditions, or privileges for certain tenants.</li>
                        <li>Falsely denying that housing is available.</li>
                        <li>Advertising that indicates a preference or limitation based on a protected class.</li>
                    </ul>
                    <h2>Familial Status and Disability</h2>
                    <p>Two commonly misunderstood areas are familial status and disability. You cannot refuse to rent to families with children, nor can you impose unreasonable occupancy limits designed to exclude families. Regarding disabilities, landlords must allow reasonable accommodations, such as allowing a service animal even if there is a "no pets" policy.</p>
                    <h2>Best Practices for Compliance</h2>
                    <p>To ensure compliance, apply your screening criteria uniformly to every applicant. Keep meticulous records of all interactions and decisions. When in doubt, consult with a legal professional specializing in real estate law.</p>
                    <p>At Pillar Property Management, our team is rigorously trained in FHA compliance, protecting our owners from liability.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Maximizing Your Rental Property ROI',
                'excerpt' => 'Actionable strategies for increasing your rental income, reducing expenses, and maximizing your overall return on investment.',
                'content' => '
                    <p>Real estate investing is all about the numbers. Whether you own one single-family home or a portfolio of multi-unit buildings, maximizing your Return on Investment (ROI) should be a constant priority.</p>
                    <h2>Strategic Upgrades</h2>
                    <p>Not all renovations are created equal. Focus on upgrades that provide the highest return. Minor kitchen and bathroom remodels, fresh paint, and updated lighting fixtures often yield a significant increase in rental value compared to their cost.</p>
                    <h2>Reduce Turnover</h2>
                    <p>Tenant turnover is a major profit killer. You lose rent during the vacancy period, incur marketing costs, and have to pay for unit turns. Maximize retention by responding quickly to maintenance requests, keeping the property in excellent condition, and fostering a good landlord-tenant relationship.</p>
                    <h2>Implement Pet Policies</h2>
                    <p>Allowing pets can significantly increase your pool of potential tenants and allows you to charge pet rent or non-refundable pet fees, boosting your monthly income. Just ensure you have clear policies and require renter\'s insurance that covers pet liability.</p>
                    <h2>Regularly Review Rents</h2>
                    <p>Don\'t let your rents fall below market value. Conduct an annual market analysis and adjust your rents accordingly upon lease renewal. Small, regular increases are usually better received by tenants than sudden, large jumps.</p>
                    <p>By focusing on both increasing revenue and controlling expenses, you can significantly enhance the profitability of your real estate portfolio.</p>
                ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(25),
            ],
        ];

        foreach ($posts as $postData) {
            $postData['slug'] = Str::slug($postData['title']);
            Post::updateOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );
        }
    }
}
