<div class="ai_images bottom-text">
    @if(!empty($imageArrays))
    @foreach($imageArrays as $key => $image)
    <a href="{{$image}}">
        <img src="{{$image}}" class="img-thumbnail" style="width: 100px; height: 100px; margin: 5px;" />
    </a>
    @endforeach
    @endif
</div>