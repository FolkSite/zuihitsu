<div class="form-group">
    <label for="post-header" class="col-sm-2 control-label">Заголовок</label>

    <div class="col-sm-10">
        <input type="text" name="header" id="post-name" class="form-control" value="@if (isset($edit_post)){{ $edit_post->header }}@endif">
    </div>
</div>
<div class="form-group">
    <label for="post-message" class="col-sm-2 control-label">Пост</label>

    <div class="col-sm-10">
        <textarea name="message" id="post-message" class="form-control" value="" rows="5">@if (isset($edit_post)){{ $edit_post->message }}@endif</textarea>
    </div>
</div>
<div class="form-group">
    <label for="post-tags" class="col-sm-2 control-label">Теги</label>

    <div class="col-sm-10">
        <input type="text" name="tags" id="post-tags" class="form-control" value="@if (isset($edit_tags)){{ $edit_tags }}@endif">
    </div>
</div>

<div class="form-group">
    <label for="post-files" class="col-sm-2 control-label">Файл</label>

    <div class="col-sm-10">
        <input type="file" name="images" value="" id="post-files">
    </div>
</div>
