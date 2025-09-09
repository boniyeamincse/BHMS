<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit') }} {{ ucfirst(str_replace('-', ' ', $section)) }}
            </h2>
            <a href="{{ route('saas.cms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to CMS
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Messages -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('saas.cms.update', $section) }}">
                    @method('PATCH')
                    @csrf

                    <div class="p-6 space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Content Information</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title *</label>
                                    <input type="text" name="title" value="{{ old('title', $content->title ?? '') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Content *</label>
                                    <textarea name="content" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your content here...">{{ old('content', $content->content ?? '') }}</textarea>
                                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Publication Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input id="published" name="status" value="published" type="radio" {{ old('status', $content->status ?? '') === 'published' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <label for="published" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="text-green-600">Published</span> - Content is live on the website
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="draft" name="status" value="draft" type="radio" {{ old('status', $content->status ?? '') === 'draft' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <label for="draft" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="text-yellow-600">Draft</span> - Content is saved but not live
                                    </label>
                                </div>
                            </div>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- SEO Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Information (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">SEO Title</label>
                                    <input type="text" name="seo_title" value="{{ old('seo_title', $content->seo_title ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Custom meta title (optional)">
                                    <p class="mt-1 text-xs text-gray-500">Leave empty to use the page title</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">SEO Keywords</label>
                                    <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $content->seo_keywords ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="keyword1, keyword2, keyword3">
                                    <p class="mt-1 text-xs text-gray-500">Comma-separated keywords</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">SEO Description</label>
                                <textarea name="seo_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Brief description for search engines">{{ old('seo_description', $content->seo_description ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Maximum 500 characters. Leave empty for auto-generated description</p>
                            </div>
                        </div>

                        <!-- Current Version Info -->
                        @if($content && $content->exists)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Version Information</h4>
                            <div class="text-sm text-gray-600 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <strong>Version:</strong> {{ $content->version ?? 1 }}
                                </div>
                                <div>
                                    <strong>Created:</strong> {{ $content->created_at?->format('M d, Y H:i') }}
                                </div>
                                <div>
                                    <strong>Last Updated:</strong> {{ $content->updated_at?->format('M d, Y H:i') }}
                                </div>
                            </div>
                            @if($content->updated_by)
                            <div class="text-sm text-gray-600 mt-2">
                                <strong>Last Modified By:</strong> {{ $content->updater?->name ?? 'Unknown' }}
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-3 text-right">
                        <a href="{{ route('saas.cms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>