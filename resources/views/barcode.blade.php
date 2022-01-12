<div class="barcode">
    <p class="name">{{$product->name}}</p>
    <p class="price">Price: {{$product->price}}</p>
    {!! DNS1D::getBarcodeHTML("101", "C128",1.4,22) !!}
    <p class="pid">{{$product->id}}</p>
</div>