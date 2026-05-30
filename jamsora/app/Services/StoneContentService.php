<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use Illuminate\Support\Str;

class StoneContentService
{
    /** @var array<string, array<string, string>> */
    private array $templates = [
        'diamond' => [
            'what_is' => 'Diamonds are crystalline carbon formed under extreme heat and pressure, prized for unmatched brilliance and hardness (10 on the Mohs scale).',
            'history' => 'Diamonds have symbolized eternal love for centuries, from Indian mines to modern ethical sourcing and lab-grown innovation.',
            'benefits' => 'Exceptional sparkle, durability for daily wear, strong resale value, and timeless appeal for engagement and heirloom jewelry.',
            'uses' => 'Engagement rings, wedding bands, earrings, pendants, and statement pieces where maximum light return is desired.',
            'quality_factors' => 'Cut, color, clarity, and carat weight (the 4Cs) determine beauty and value. Certification from GIA or IGI adds trust.',
            'care' => 'Clean with warm soapy water and a soft brush. Store separately to avoid scratching other gems. Annual prong checks are recommended.',
            'buying_guide' => 'Choose certified stones, prioritize cut for brilliance, and match metal (platinum or gold) to lifestyle and skin tone.',
        ],
        'moissanite' => [
            'what_is' => 'Moissanite is a lab-created silicon carbide gem with diamond-like fire and excellent durability (9.25 Mohs).',
            'history' => 'Naturally rare moissanite was discovered in meteorites; today’s jewelry uses responsibly grown crystals for sustainable sparkle.',
            'benefits' => 'More fire than diamond at a fraction of the cost, ethical production, and excellent hardness for everyday rings.',
            'uses' => 'Engagement rings, solitaires, earrings, and travel jewelry where brilliance and budget both matter.',
            'quality_factors' => 'Color grade, cut precision, and size consistency. Premium cuts maximize light performance.',
            'care' => 'Clean with mild soap and water. Moissanite resists scratching but protect settings from hard impacts.',
            'buying_guide' => 'Compare colorless vs near-colorless grades, confirm lifetime warranty policies, and pair with durable settings.',
        ],
        'ruby' => [
            'what_is' => 'Ruby is red corundum, colored by chromium, and one of the four precious gemstones alongside sapphire, emerald, and diamond.',
            'history' => 'Rubies adorned royalty from Burma to Europe; pigeon-blood reds from Myanmar remain the most coveted.',
            'benefits' => 'Rich color symbolism (passion, courage), excellent hardness (9 Mohs), and strong presence in fine jewelry.',
            'uses' => 'Engagement rings, cocktail rings, earrings, and anniversary gifts celebrating July birthstones.',
            'quality_factors' => 'Color saturation, clarity, cut, carat, and origin. Untreated stones command premium prices.',
            'care' => 'Avoid ultrasonic cleaners on fracture-filled stones. Warm water and soft cloth are safest.',
            'buying_guide' => 'Request lab reports for treatments, prefer vivid red with good transparency, and secure protective settings.',
        ],
        'emerald' => [
            'what_is' => 'Emerald is green beryl, valued for lush color. Natural inclusions (“jardin”) are common and accepted.',
            'history' => 'Cleopatra’s mines to Colombian Muzo deposits—emeralds have defined luxury for millennia.',
            'benefits' => 'Distinctive green hue, May birthstone status, and unmistakable character in bespoke designs.',
            'uses' => 'Rings, pendants, and earrings where color makes a statement; often set with diamonds as accents.',
            'quality_factors' => 'Color tone, clarity eye-cleanliness, cut proportions, and treatment disclosure (oil/resin).',
            'care' => 'Avoid heat and harsh chemicals. Clean gently; re-oiling may be needed over years for some stones.',
            'buying_guide' => 'Choose protective settings (bezels/halo), understand enhancement types, and prioritize color over flawless clarity.',
        ],
        'sapphire' => [
            'what_is' => 'Sapphire is corundum in every color except red (ruby). Blue sapphire is the most iconic variety.',
            'history' => 'Royal engagement traditions and Kashmir blues define sapphire’s prestige in fine jewelry.',
            'benefits' => 'Hardness (9 Mohs), vast color range, and excellent value versus diamond for colored-stone rings.',
            'uses' => 'Engagement rings, eternity bands, earrings, and custom birthstone pieces (September).',
            'quality_factors' => 'Hue, saturation, clarity, cut, carat, and heat-treatment status on certificates.',
            'care' => 'Warm soapy water is ideal. Sapphires tolerate daily wear but protect from sharp blows on prongs.',
            'buying_guide' => 'Insist on certification for untreated stones, match undertone to skin, and consider teal or padparadscha for uniqueness.',
        ],
    ];

    public function ensureCategoryContent(Category $category): void
    {
        $key = $this->stoneKey($category->name);

        if (empty($category->seo_content)) {
            $category->seo_content = $this->seoBlurb($category->name, $key);
        }

        if (empty($category->stone_content)) {
            $category->stone_content = $this->stoneSections($category->name, $key);
        }

        if (empty($category->meta_title)) {
            $category->meta_title = "Shop {$category->name} Gemstones | Jamsora";
        }

        if (empty($category->meta_description)) {
            $category->meta_description = "Browse certified {$category->name} gemstones with transparent specs, IGI add-on, and secure checkout at Jamsora.";
        }

        $category->saveQuietly();

        if ($category->faqs()->count() < 10) {
            $this->seedFaqs($category, $key);
        }
    }

    public function ensureProductFaqs(Product $product): void
    {
        if ($product->faqs()->count() >= 5) {
            return;
        }

        $stone = $product->stone_type ?: $product->category?->name ?: 'gemstone';
        $questions = [
            "Is this {$product->name} natural or lab-grown?",
            "What certification is included with this {$stone}?",
            "Can I add IGI certification to this item?",
            "What is the estimated delivery time?",
            "How should I care for this {$stone} jewelry?",
        ];
        $answers = [
            'Specifications and certificate details on this page describe origin and treatments. Contact us for gemologist verification.',
            $product->certification
                ? "This piece includes {$product->certification->name} documentation (ref. {$product->certification->certificate_number})."
                : 'A certificate may be available on request or via the optional IGI add-on at checkout.',
            $product->igi_available
                ? 'Yes—select “Add IGI Certification” on the product page for an additional fee and extended lead time.'
                : 'IGI add-on is not available for this SKU.',
            $product->deliveryRange(false).' (add '.config('jamsora.igi.extra_delivery_days', 7).' days if IGI is selected).',
            'Use mild soap, soft brush, and separate storage. Avoid harsh chemicals and ultrasonic cleaning unless advised.',
        ];

        foreach ($questions as $i => $question) {
            Faq::firstOrCreate(
                [
                    'faqable_type' => Product::class,
                    'faqable_id' => $product->id,
                    'question' => $question,
                ],
                [
                    'answer' => $answers[$i] ?? 'Please contact Jamsora support for details.',
                    'sort_order' => $i,
                    'status' => 'active',
                ]
            );
        }
    }

    private function seedFaqs(Category $category, string $key): void
    {
        $name = $category->name;
        $base = [
            "What is {$name}?",
            "Is {$name} good for engagement rings?",
            "How durable is {$name}?",
            "How do I clean {$name} jewelry?",
            "What should I look for when buying {$name}?",
            "Does Jamsora offer certified {$name}?",
            "What is the difference between {$name} and diamond?",
            "How long does {$name} shipping take?",
            "Can I add IGI certification to {$name} orders?",
            "Are your {$name} stones treated?",
            "What metal pairs best with {$name}?",
            "Do you offer custom {$name} settings?",
            "What is your return policy on {$name}?",
            "How are {$name} prices calculated?",
            "Is {$name} ethically sourced?",
        ];

        foreach ($base as $i => $question) {
            Faq::firstOrCreate(
                [
                    'faqable_type' => Category::class,
                    'faqable_id' => $category->id,
                    'question' => $question,
                ],
                [
                    'answer' => $this->faqAnswer($question, $name, $key),
                    'sort_order' => $i,
                    'status' => 'active',
                ]
            );
        }
    }

    private function faqAnswer(string $question, string $name, string $key): string
    {
        $t = $this->templates[$key] ?? $this->templates['sapphire'];

        if (str_contains($question, 'What is')) {
            return $t['what_is'];
        }
        if (str_contains($question, 'engagement')) {
            return "{$name} is popular for engagement rings when you want distinctive color and excellent hardness. Our buying guide helps you choose secure settings.";
        }
        if (str_contains($question, 'durable')) {
            return $t['quality_factors'].' Hardness makes '.$name.' suitable for rings with proper setting design.';
        }
        if (str_contains($question, 'clean')) {
            return $t['care'];
        }
        if (str_contains($question, 'buying')) {
            return $t['buying_guide'];
        }
        if (str_contains($question, 'certified')) {
            return 'Yes—many SKUs ship with lab documents. You may also add IGI certification at checkout on eligible products.';
        }
        if (str_contains($question, 'diamond')) {
            return "{$name} offers different color and value than diamond. Compare fire, price, and personal style preferences.";
        }
        if (str_contains($question, 'shipping')) {
            return 'Most '.$name.' orders ship in 10–14 business days; IGI add-on extends delivery by 7 days.';
        }
        if (str_contains($question, 'IGI')) {
            return 'Select IGI on the product page (+$'.config('jamsora.igi.fee', 100).') to receive independent grading with extended lead time.';
        }
        if (str_contains($question, 'treated')) {
            return 'Treatment status is listed per product and on certificates when provided. We disclose enhancements transparently.';
        }

        return "Our gemologists can help you choose the right {$name}. Browse filters on this page or contact support.";
    }

    private function stoneKey(string $name): string
    {
        $slug = Str::slug($name);

        foreach (array_keys($this->templates) as $key) {
            if (str_contains($slug, $key)) {
                return $key;
            }
        }

        return 'sapphire';
    }

    private function stoneSections(string $name, string $key): array
    {
        $t = $this->templates[$key] ?? $this->templates['sapphire'];

        return [
            'what_is' => str_replace('Sapphire', $name, $t['what_is']),
            'history' => $t['history'],
            'benefits' => $t['benefits'],
            'uses' => $t['uses'],
            'quality_factors' => $t['quality_factors'],
            'care' => $t['care'],
            'buying_guide' => $t['buying_guide'],
        ];
    }

    private function seoBlurb(string $name, string $key): string
    {
        $t = $this->templates[$key] ?? $this->templates['sapphire'];

        return "<p>Discover hand-selected {$name} at Jamsora—{$t['what_is']} Explore {$name} rings, loose stones, and custom-ready gems with clear specifications and optional IGI certification.</p>";
    }
}
