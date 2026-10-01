<div
    class="product-detail-prices"
    @if(!empty($fallback)) data-price-fallback @endif
    @if(!empty($live))
        x-data
        x-init="document.querySelector('[data-price-fallback]')?.remove()"
    @endif
>
    @if(\App\Models\Product::hasPublicValue($priceSource->old_price))
        <span class="product-detail-old-price">
            {{ $priceSource->old_price ? $priceSource->old_price . 'p' : '' }}
        </span>
    @endif

    <span class="product-detail-current-price">
        @if($priceSource->price === null)
            Цена не указана
        @else
            {{ $priceSource->price }} р
        @endif
    </span>
</div>
