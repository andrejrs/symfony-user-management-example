(function($) {
    "use strict"; 

    let userGroupId = $("#user-group").attr("data-user-group-id");
    var onRemoveClick = (e) => {

        let li = $(e.currentTarget).parents("li");
        let userId = $(e.currentTarget).attr("data-user-id");

        if (!confirm('Are you sure you want to remove this user from group?')) {
            return false;
        }

        $.ajax({
            url: "/api/users/"+userId+"/removegroup/"+userGroupId,
            method: "GET",
            success: function(data) {
                li.remove();
                if ($(".users > li").length === 0) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.error.message);
              console.log('error', xhr);
            }
          });
    };

    $('.removeUserFromGroup').on("click", onRemoveClick);
  


  })(jQuery); 
  