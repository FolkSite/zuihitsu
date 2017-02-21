@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Редактирование тегов</strong>
            </div>
            <div class="panel-body">
                    @if (isset($tags_cloud) AND count($tags_cloud) > 0)
                        @foreach ($tags_cloud as $tag)

                            <li class="list-group-item">
                                <span class="badge">{{ $tag['count'] }}</span>
                                <span role="button" class="badge"  onclick="event.preventDefault();
                                         document.getElementById('edit-tag-{{ $tag['id'] }}').submit();">Переименовать</span>

                                <span role="button" class="badge" onclick="event.preventDefault();
                                         document.getElementById('delete-tag-{{ $tag['id'] }}').submit();">Удалить</span>

                                 <form id="delete-tag-{{ $tag['id'] }}" action="{{url('post/edit/tag/' . $tag['id'])}}" method="POST" style="display: none;">
                                     {{ csrf_field() }}
                                     {{ method_field('DELETE') }}
                                 </form>

                                 <form id="edit-tag-{{ $tag['id'] }}" action="{{url('post/edit/tag/' . $tag['id'])}}" method="POST" style="display: none;">
                                     {{ csrf_field() }}
                                 </form>

                                <a class="" role="" href="{{ url('post/tag/' . $tag['name']) }}">{{ $tag['name'] }}</a>
                            </li>

                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
