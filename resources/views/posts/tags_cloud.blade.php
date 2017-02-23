<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Облако тегов</strong>
    </div>
    <div class="panel-body">
        @for ($i = 0; $i < count($tags_cloud); $i++)
        
          <a class="tag_button btn btn-default" role="button" href="{{ url('post/tag/' . $tags_cloud[$i]['name']) }}">{{ $tags_cloud[$i]['name'] }} <span class="badge">{{ $tags_cloud[$i]['count'] }}</span></a>
        @endfor
    </div>
    <div class="panel-footer">
        <button type="submit" id="" class="btn btn-default btn-sm" onclick="event.preventDefault();
                 document.getElementById('edit-tags-cloud').submit();">

            <i class="fa fa-btn fa-trash"></i>Изменить</button>

        <form id="edit-tags-cloud" action="{{ url('post/edit/tags') }}" method="GET" style="display: none;">
            {{ csrf_field() }}
        </form>

    </div>
</div>
