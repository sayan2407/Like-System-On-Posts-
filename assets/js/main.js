jQuery(document).ready( function() {

    jQuery('.save_like_system').click(function() {
        let isSystemEnable = jQuery('.like_enable').prop('checked');

        let favIcon = jQuery('input[name=fav_icon]:checked').val();

        let iconPosition = jQuery('input[name=display_like]:checked').val();


        let data = {
            action: 'like_system',
            isSystemEnable: isSystemEnable,
            favIcon: favIcon,
            iconPosition: iconPosition,
            url: display_likes['ajaxurl']
        };

        jQuery.ajax({
            url: display_likes['ajaxurl'],
            type: 'POST',
            data: data,
            beforeSend: function() {

            },
            success: function( response ) {

                if ( response ) {
                    location.reload();
                }

            }

        } )
    })

    
} )