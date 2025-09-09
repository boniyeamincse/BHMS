<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CmsController extends Controller
{
    public function index()
    {
        $sections = [
            'about' => CmsContent::where('slug', 'about')->where('locale', config('app.locale', 'en'))->first(),
            'services' => CmsContent::where('slug', 'services')->where('locale', config('app.locale', 'en'))->first(),
            'pricing' => CmsContent::where('slug', 'pricing')->where('locale', config('app.locale', 'en'))->first(),
            'faq' => CmsContent::where('slug', 'faq')->where('locale', config('app.locale', 'en'))->first(),
            'landing_text' => CmsContent::where('slug', 'landing-text')->where('locale', config('app.locale', 'en'))->first(),
        ];

        return view('saas.cms.index', compact('sections'));
    }

    public function edit($section)
    {
        $content = CmsContent::where('slug', $section)->where('locale', config('app.locale', 'en'))->first();

        if (!$content) {
            // Create new content if it doesn't exist
            $content = new CmsContent([
                'slug' => $section,
                'locale' => config('app.locale', 'en'),
                'status' => 'draft'
            ]);
        }

        return view('saas.cms.edit', compact('content', 'section'));
    }

    public function update(Request $request, $section)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $content = CmsContent::where('slug', $section)->where('locale', config('app.locale', 'en'))->first();

        if (!$content) {
            // Create new content
            $validatedData['slug'] = $section;
            $validatedData['locale'] = config('app.locale', 'en');
            $validatedData['type'] = 'page';
            $validatedData['created_by'] = Auth::id();
            $content = CmsContent::create($validatedData);
        } else {
            // Update existing content
            $validatedData['updated_by'] = Auth::id();
            $validatedData['version'] = ($content->version ?? 0) + 1;
            $content->update($validatedData);
        }

        return redirect()->route('saas.cms.index')
            ->with('success', 'Content updated successfully.');
    }

    public function landing()
    {
        try {
            // Optimized: Single query to fetch all required sections
            $locale = config('app.locale', 'en');
            $sectionSlugs = ['about', 'services', 'pricing', 'faq', 'landing-text'];

            $cmsSections = CmsContent::published()
                ->byLocale($locale)
                ->whereIn('slug', $sectionSlugs)
                ->get()
                ->keyBy('slug');

            $sections = [];
            foreach ($sectionSlugs as $slug) {
                $sections[$slug] = $cmsSections->get($slug);
            }

            return view('landing', compact('sections'));
        } catch (\Exception $e) {
            // Fallback to individual queries if optimized version fails
            $sections = [
                'about' => CmsContent::published()->byLocale(config('app.locale', 'en'))->where('slug', 'about')->first(),
                'services' => CmsContent::published()->byLocale(config('app.locale', 'en'))->where('slug', 'services')->first(),
                'pricing' => CmsContent::published()->byLocale(config('app.locale', 'en'))->where('slug', 'pricing')->first(),
                'faq' => CmsContent::published()->byLocale(config('app.locale', 'en'))->where('slug', 'faq')->first(),
                'landing_text' => CmsContent::published()->byLocale(config('app.locale', 'en'))->where('slug', 'landing-text')->first(),
            ];

            return view('landing', compact('sections'));
        }
    }
}