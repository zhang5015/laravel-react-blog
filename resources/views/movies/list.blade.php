@extends('layouts.app')

@section('title', '案例欣赏')

@section('content')
<div class="container" style="margin-bottom:20px">
    <div class="row">
        <div class="col-md-7 col-md-offset-1">
          @if(count($movies))
            @foreach ( $movies as $movie)
              <div class="z-movie-horizontal">
                <div class="row">
                  <div class="col-xs-8">
                      <a href="{{ route('movies.show', $movie->id) }}"><p class="z-title">{{ $movie->title }}</p></a>
					  <p class="z-info hidden-xs">发表于 {{ $movie->created_at_date }} · 最后访问 {{ $movie->updated_at_diff }}</p></span>
                      <p class="z-intro">{{ $movie->content }}</p>
                      <div class="hidden-xs">
                        @if(count($movie->tags))
                          @foreach($movie->tags as $tag)
                            <span class="label label-info" style="font-size:11px;padding:1px 5px">{{ $tag->name }}</span>
                          @endforeach
                        @endif
                      </div>
                  </div>
                  <div class="col-xs-4" style="padding-left:0">
                    <a href="{{ route('movies.show', $movie->id) }}"><img src="{{ $movie->cover or '/default.jpg' }}" class="img-responsive z-cover" alt="imax1"></a>
                  </div>
                </div>
              </div>
            @endforeach
            {{ $movies->links() }}
          @else
            <div class="alert alert-warning" role="alert" style="margin-top:20px">sorry, no movies!</div>
          @endif
        </div>
        <div class="col-md-3">
          <div class="">

          </div>
        </div>
    </div>
</div>
@endsection
