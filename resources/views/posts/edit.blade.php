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
                    <form action="{{url('post/edit/' . $post->id)}}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <!-- Task Name -->
                        <div class="form-group">
                            <label for="post-header" class="col-sm-2 control-label">Заголовок</label>

                            <div class="col-sm-10">
                                <input type="text" name="header" id="post-name" class="form-control" value="{{ $post->header }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="post-message" class="col-sm-2 control-label">Пост</label>

                            <div class="col-sm-10">
                                <textarea name="message" id="post-message" class="form-control" value="" rows="5">{{ $post->message }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="post-tags" class="col-sm-2 control-label">Теги</label>

                            <div class="col-sm-10">
                                <input type="text" name="tags" id="post-tags" class="form-control" value="{{ $post->tags }}">
                            </div>
                        </div>

                        <!-- Add Task Button -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-6">
                                <button type="submit" class="btn btn-default">
                                    <i class="fa fa-btn fa-plus"></i>Сохранить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
