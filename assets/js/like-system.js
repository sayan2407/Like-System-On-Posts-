jQuery(document).ready( function() {
    jQuery('.click_on_like').click( function() {

        console.log('likesystem ', likesystem);
        let userId = likesystem.user_id;
        let postId = likesystem.post_id;

        if ( userId == 0 ) {
            window.alert('Please login to like the post.');
        } else {
            let data = {
                url: likesystem.ajaxurl,
                action: 'like_dislike',
                postId: postId,
                userId: userId
            };

            jQuery.ajax({
                url: likesystem.ajaxurl,
                type: 'POST',
                data: data,
                success: function(response) {

                    jQuery('.num_of_likes').text(response.num_of_likes + ' likes');

                    if( response.value == "like" ) {
                        console.log('liked');
                    } else {
                        console.log('disliked');

                    }

                    // console.log('response ', response);
                    // console.log('response ', response['value']);

                }
            })
        }
        
    } )
} )