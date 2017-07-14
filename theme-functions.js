jQuery(document).ready(function () {

  jQuery(".user_tclap").click(function () {
    post_id = jQuery(this).attr("data-post_id")
    nonce = jQuery(this).attr("data-nonce")

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: myAjax.ajaxurl,
      data: {
        action: "my_user_tclap",
        post_id: post_id,
        nonce: nonce
      },
      success: function (response) {
        if (response.type == "success") {
          jQuery('.user_tclap[data-post_id="' + post_id + '"] > #tclap_counter').html(response.tclap_count);
        } else {
          alert("Your tclap could not be added");
        }
      }
    })

  })

})
