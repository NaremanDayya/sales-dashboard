<div class="card">
    <div class="h-100">
    <img class="card-img-top" src="{{$classroom->cover_image_url}}" >
    <div class="card-body ">
        <h5 class="card-title"> {{ $classroom->name }}</h5>
        <p class="card-text"> {{ $classroom->section }}- {{ $classroom->room }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ $classroom->url }}" class="btn btn-sm btn-primary">{{ __('View') }}</a>
            {{ $slot }}
        </div>
    </div>
    </div>
</div>
