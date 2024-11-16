<div class="ai_videos bottom-text">
    @if(!empty($videoArrays))
    @foreach($videoArrays as $key => $video)
    <a href="{{$video}}">
        <img src="{{$video}}" class="img-thumbnail" style="width: 100px; height: 100px; margin: 5px;" />
    </a>
    @endforeach
    @endif
</div>