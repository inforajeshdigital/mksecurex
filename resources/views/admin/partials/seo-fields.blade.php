{{-- Full SEO panel. Vars: $model (with ->seo), $url (public URL), $defaultTitle, $defaultDesc --}}
@php($seo = $model->seo)
@php($v = fn ($k, $d = '') => old("seo.$k", $seo?->$k ?? $d))
<div class="row g-4">
    <div class="col-xl-7">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">SEO Meta Title</label>
                <input type="text" name="seo[meta_title]" value="{{ $v('meta_title') }}" class="form-control" data-counter="60" placeholder="{{ $defaultTitle ?? '' }}">
            </div>
            <div class="col-12">
                <label class="form-label">SEO Meta Description</label>
                <textarea name="seo[meta_description]" class="form-control" rows="3" data-counter="160" placeholder="{{ $defaultDesc ?? '' }}">{{ $v('meta_description') }}</textarea>
            </div>
            <div class="col-md-6"><label class="form-label">Focus Keyword</label><input type="text" name="seo[focus_keyword]" value="{{ $v('focus_keyword') }}" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Secondary Keywords <small class="text-muted">(comma separated)</small></label><input type="text" name="seo[secondary_keywords]" value="{{ $v('secondary_keywords') }}" class="form-control"></div>
            <div class="col-12">
                <label class="form-label">Canonical URL <small class="text-muted">(leave empty for self-referencing)</small></label>
                <input type="url" name="seo[canonical_url]" value="{{ $v('canonical_url') }}" class="form-control" placeholder="{{ $url ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Meta Robots</label>
                <input type="hidden" name="seo[robots_index]" value="0">
                <div class="form-check form-switch form-check-inline"><input class="form-check-input" type="checkbox" name="seo[robots_index]" value="1" id="ri" @checked($v('robots_index', true))><label class="form-check-label" for="ri">Index</label></div>
                <input type="hidden" name="seo[robots_follow]" value="0">
                <div class="form-check form-switch form-check-inline"><input class="form-check-input" type="checkbox" name="seo[robots_follow]" value="1" id="rf" @checked($v('robots_follow', true))><label class="form-check-label" for="rf">Follow</label></div>
                <div class="form-text">Noindex pages are excluded from the XML sitemap.</div>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3"><i class="fa-brands fa-facebook text-primary me-1"></i> Open Graph</h6>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">OG Title</label><input type="text" name="seo[og_title]" value="{{ $v('og_title') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">OG Type</label>
                <select name="seo[og_type]" class="form-select">@foreach(['website', 'article', 'product', 'profile'] as $t)<option @selected($v('og_type', 'website') === $t)>{{ $t }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">OG URL</label><input type="url" name="seo[og_url]" value="{{ $v('og_url') }}" class="form-control" placeholder="auto"></div>
            <div class="col-12"><label class="form-label">OG Description</label><textarea name="seo[og_description]" rows="2" class="form-control">{{ $v('og_description') }}</textarea></div>
            <div class="col-md-6">@include('admin.partials.image-field', ['name' => 'seo_og_image', 'label' => 'OG Image (Facebook / WhatsApp / LinkedIn share)', 'value' => $seo?->og_image, 'size' => '1200x630'])</div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3"><i class="fa-brands fa-x-twitter me-1"></i> Twitter Card</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Card Type</label>
                <select name="seo[twitter_card]" class="form-select">@foreach(['summary_large_image', 'summary'] as $t)<option @selected($v('twitter_card', 'summary_large_image') === $t)>{{ $t }}</option>@endforeach</select></div>
            <div class="col-md-8"><label class="form-label">Twitter Title</label><input type="text" name="seo[twitter_title]" value="{{ $v('twitter_title') }}" class="form-control"></div>
            <div class="col-12"><label class="form-label">Twitter Description</label><textarea name="seo[twitter_description]" rows="2" class="form-control">{{ $v('twitter_description') }}</textarea></div>
            <div class="col-md-6">@include('admin.partials.image-field', ['name' => 'seo_twitter_image', 'label' => 'Twitter Image', 'value' => $seo?->twitter_image, 'size' => '1200x630'])</div>
        </div>

        <hr class="my-4">
        <label class="form-label">Custom Schema Markup (JSON-LD)</label>
        <textarea name="seo[schema_markup]" rows="6" class="form-control code-area" placeholder='{"@@context":"https://schema.org","@type":"Service", ...}'>{{ $v('schema_markup') }}</textarea>
        <div class="form-text">Paste valid JSON only (without &lt;script&gt; tags). Organization, Breadcrumb and Service schema are generated automatically.</div>
    </div>

    <div class="col-xl-5">
        <div class="position-sticky" style="top:84px">
            <label class="form-label">Google Search Preview</label>
            <div class="serp mb-3" data-seo-preview data-default-title="{{ $defaultTitle ?? '' }}" data-default-desc="{{ $defaultDesc ?? '' }}">
                <div class="s-url">{{ parse_url(url('/'), PHP_URL_HOST) }} <span>› {{ trim(parse_url($url ?? url('/'), PHP_URL_PATH) ?? '', '/') ?: '' }}</span></div>
                <div class="s-title"></div>
                <div class="s-desc"></div>
            </div>
            <label class="form-label">Social Share Preview</label>
            <div class="social-card mb-3">
                <div class="sc-img" style="{{ $seo?->og_image ? "background-image:url('".img_url($seo->og_image)."')" : '' }}">@unless($seo?->og_image)<i class="fa-regular fa-image fa-2x"></i>@endunless</div>
                <div class="sc-body"><div class="sc-host">{{ parse_url(url('/'), PHP_URL_HOST) }}</div><div class="sc-title"></div><div class="sc-desc"></div></div>
            </div>
            <label class="form-label">SEO Recommendations</label>
            <ul class="list-unstyled seo-score" data-seo-tips></ul>
            <div class="form-text">Recommendations only — they never block publishing.</div>
        </div>
    </div>
</div>
