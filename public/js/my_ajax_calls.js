/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    /*
     * на данный момент страницы просто обновляется при добавлении поста
    $('#createPostButton').click(function(){
        
        var formData = new FormData($('#createPostForm')[0]);
        var $btn = $(this).button('loading');
        
        $.ajax({
            type: "POST",
            processData: false,
            contentType: false,
            url: "/post",
            data:  formData,
            })
            .done(function( data ) {
                console.log(formData);
                console.log(data);
                $('#createPostForm')[0].reset();

                var div = document.createElement('div');
                div.innerHTML = data;
                $('#postsAll')[0].prepend(div);

                $btn.button('reset')
            })
            .fail(function(jqXHR, textStatus) {
                alert( "Ошибка: " + textStatus );
                $btn.button('reset');
            });

    })
    */
   
    $('.delete-post-button').click(function(){
        
        var id = $(this).data("post-id"),
            formID = "#delete-post-form-" + id,
            action = "post/" + id,
            post = "#post-" + id;
       
        var formData = new FormData($(formID)[0]);
        
        console.log($(this));
        
        var $btn = $(this).button('loading');
        
        $.ajax({
            type: "POST",
            processData: false,
            contentType: false,
            url: action,
            data:  formData 
            })
            .done(function( data ) {
                  if (data === "true") {
                      $(post).remove();
                  }
            })                  
            .fail(function(jqXHR, textStatus) {
                alert( "Ошибка: " + textStatus );
                $btn.button('reset');
            });
        
    })
    
})
