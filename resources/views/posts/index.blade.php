@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            @include('common.errors')
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
                            

                            <!-- New Task Form -->
                                                        <form id="createPostForm" action="{{ url('post') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                @include('posts.post_form')
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-success" id="createPostButton" data-loading-text="Добавление..." autocomplete="off">
                                            <i class="fa fa-btn fa-plus"></i>Добавить
                                        </button>
                                    </div>
                                </div>
                            </form>
                                <!-- Task Name -->
                                <!-- Add Task Button -->
                            
                        </div>

                    </div>
                </div>
            </div>

            <!-- Current Tasks -->
            <!-- Button trigger modal -->
            <div id="postsAll">
            @if (count($posts) > 0)

                @foreach ($posts as $post)
                <div class="panel panel-default post" style="word-break: break-word;" id="post-{{ $post->id }}">
                  <div class="panel-heading"><strong>{{ $post->header }}</strong></div>
                  <div class="panel-body">
                          {!! nl2br(e($post->message)) !!}
                  <br>

                  @if (count($images) > 0 AND array_key_exists($post->id, $images))
                    <br>
                        @foreach ($images[$post->id] as $img)

                            <a href="{{ $img['img'] }}"><img src="{{ $img['thumbnail'] }}" alt="" class="img-thumbnail"></a>

                        @endforeach
                    <br>
                  @endif

                  <em>
                      @if (array_key_exists($post->id, $tags) AND !empty($tags[$post->id]))
                        <br>
                        Теги:
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

                      <button type="submit" class="btn btn-default btn-sm" onclick="event.preventDefault();
                               document.getElementById('edit-form-{{ $post->id }}').submit();">

                          <i class="fa fa-btn fa-trash"></i>Изменить</button>

                      <form id="edit-form-{{ $post->id }}" action="{{url('post/edit/' . $post->id)}}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                      </form>

<!--                      <button type="submit" id="delete-post-button-{{ $post->id }}" class="btn btn-default btn-sm delete-post-button" 
                              data-post-id="{{ $post->id }}" data-loading-text="Удаление..." autocomplete="off">
                          <i class="fa fa-btn fa-trash"></i>Удалить</button>

                      <form id="delete-post-form-{{ $post->id }}" action="{{url('post/' . $post->id)}}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                      </form> -->

                  </div>
                </div>

                @endforeach

            @endif
            </div>
            @if ($pages AND count($pages) > 1)
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        @if ($pages['buttons']['prev'] > 0)
                            <li>
                                <a href="{{ url('posts/' . $pages['buttons']['prev']) }}" aria-label="Previous">
                                  <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>            
                        @else
                            <li class="disabled">
                                <a href="{{ url('posts/' . $pages['buttons']['prev']) }}" aria-label="Previous">
                                  <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li> 
                        @endif
                      
                        @foreach ($pages['pages'] as $key => $value)
                          @if ($value === "this")
                              <li class="active"><a href="{{ url('posts/' . $key) }}">{{ $key }}</a></li>
                          @else                   
                             <li><a href="{{ url('posts/' . $key) }}">{{ $key }}</a></li>
                          @endif
                        @endforeach
                        
                        @if ($pages['buttons']['next'] > 0)
                            <li>
                                <a href="{{ url('posts/' . $pages['buttons']['next']) }}" aria-label="Next">
                                  <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>            
                        @else
                            <li class="disabled">
                                <a href="{{ url('posts/' . $pages['buttons']['next']) }}" aria-label="Next">
                                  <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li> 
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
@endsection
