@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Редактирование</strong>
                </div>

                <div class="panel-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{url('post/edit/' . $edit_post->id)}}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        @include('posts.post_form')
                        <!-- Add Task Button -->
                        
                        @if (count($images) > 0)
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                                    @foreach ($images as $img)

                                        <div class=" col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                               <a href="{{ $img['img'] }}"><img src="{{ $img['thumbnail'] }}" alt="" class=""></a>
                                               <br>
                                                <a href="#" class="btn btn-default btn-block" role="button" onclick="event.preventDefault();
                                                        document.getElementById('delete-img-{{ $img['id'] }}').submit();">Удалить</a>

                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                        @endif
                    
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-6">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-plus"></i>Сохранить
                                </button>
                                <button type="button" id="delete-post-button-{{ $edit_post->id }}" class="btn btn-danger" 
                                        data-post-id="{{ $edit_post->id }}" onclick="event.preventDefault();
                                                     document.getElementById('delete-post-form-{{ $edit_post->id }}').submit();">
                                    <i class="fa fa-btn fa-trash"></i>Удалить</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Цикл создает невидимые формы, которые потом используют кнопки "Удалить" под каждой кратинкий.
                        Если располагать их в форме поста то все кнопки перестают работать -->
                    @if (count($images) > 0)
                        @foreach ($images as $img)
                            <form id="delete-img-{{ $img['id'] }}" action="{{url('/post/edit/img/' . $img['id'])}}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                            </form>
                        @endforeach
                    @endif
                    
                    <form id="delete-post-form-{{ $edit_post->id }}" action="{{url('post/' . $edit_post->id)}}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                      </form>
            
                </div>
            </div>
        </div>
    </div>
@endsection
