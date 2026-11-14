{{-- Show/hide controls for service & industry images. Var: $model --}}
<label class="form-label">On the detail page, show:</label>
<div class="display-opts">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="show_banner" value="1" id="dshb" @checked(old('show_banner', $model->exists ? $model->show_banner : true))>
        <label class="form-check-label fw-semibold" for="dshb">Banner image at the top<small>Uses the Banner Image (or the Featured Image if no banner is uploaded).</small></label>
    </div>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="show_image" value="1" id="dshi" @checked(old('show_image', $model->exists ? $model->show_image : true))>
        <label class="form-check-label fw-semibold" for="dshi">Featured image inside the content<small>Hidden automatically when it is the same picture as the banner.</small></label>
    </div>
</div>
<div class="form-text mb-3">The Featured Image always shows on listing cards.</div>
