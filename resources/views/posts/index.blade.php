@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#newPost">
                Создать пост
            </button>
            <br>
            <br>
            @if (isset($tags_cloud) AND count($tags_cloud) > 0)
                    @include('posts.tags_cloud')
            @endif
            <div class="modal fade" id="newPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Новый пост</h4>
                        </div>

                        <div class="modal-body">
                            <!-- Display Validation Errors -->
                            @include('common.errors')

                            <!-- New Task Form -->
                            <form action="{{ url('post') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                @include('posts.post_form')
                                <!-- Task Name -->
                                <!-- Add Task Button -->
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-plus"></i>Добавить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Tasks -->
            <!-- Button trigger modal -->
            @if (count($posts) > 0)

                @foreach ($posts as $post)
                <div class="panel panel-default" style="word-break: break-word;">
                  <div class="panel-heading"><strong>{{ $post->header }}</strong></div>
                  <div class="panel-body">
                          {!! nl2br(e($post->message)) !!}
                  <br>

                  @if (count($images) > 0 AND array_key_exists($post->id, $images))
                        @foreach ($images[$post->id] as $img)

                            <a href="{{ $img['img'] }}"><img src="{{ $img['thumbnail'] }}" alt="" class="img-thumbnail"></a>

                        @endforeach
                  @endif

                  <br>
                  <em>Теги:
                      @if (array_key_exists($post->id, $tags))

                          @for ($i = 0; $i < count($tags[$post->id]); $i++)

                            <a href="{{url('post/tag/' . $tags[$post->id][$i])}}">{{ $tags[$post->id][$i] }}</a>
                                @if (($i + 1) < count($tags[$post->id]))
                                    /
                                @endif

                          @endfor
                      @endif
                  </em>
                  </div>
                  <div class="panel-footer">
                      
                      <button type="submit" id="{{ $post->id }}" class="btn btn-default btn-sm" onclick="event.preventDefault();
                               document.getElementById('edit-form-{{ $post->id }}').submit();">

                          <i class="fa fa-btn fa-trash"></i>Изменить</button>

                      <form id="edit-form-{{ $post->id }}" action="{{url('post/edit/' . $post->id)}}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                      </form>

                      <button type="submit" id="delete-post-{{ $post->id }}" class="btn btn-default btn-sm" onclick="event.preventDefault();
                               document.getElementById('delete-form').submit();">
                          <i class="fa fa-btn fa-trash"></i>Удалить</button>

                      <form id="delete-form" action="{{url('post/' . $post->id)}}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                      </form>

                  </div>
                </div>

                @endforeach
            @endif
        </div>
    </div>
@endsection
