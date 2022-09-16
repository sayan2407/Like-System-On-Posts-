<?php
/**
 * Plugin Name: Marked as a favorite post
 * lugin URI:        https://thecodingjobs.online/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Sayan Pal
 * Author URI:        https://thecodingjobs.online/
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       likeposts
 * Domain Path:       /likeposts
 */

 //Hooks

 add_action( 'admin_menu', 'add_new_menu' );
 add_action( 'admin_enqueue_scripts', 'admin_custom_scripts', 10, 1 );
 add_action( 'wp_enqueue_scripts', 'frontend_custom_scripts' );

 add_action( 'wp_ajax_like_system', 'saved_like_system_info' );
 add_action( 'wp_ajax__nopriv_like_system', 'saved_like_system_info' );
 

 add_action( 'wp_ajax_like_dislike', 'save_like_dislike' );
 add_action( 'wp_ajax__nopriv_like_dislike', 'save_like_dislike' );

 add_action( 'admin_init', 'custom_admin_changes' );

 add_filter( 'the_content', 'custom_like' );

 function save_like_dislike() {
    $post_id = $_POST['postId'];
    $user_id = $_POST['userId'];

    $is_user_liked = get_user_meta( $user_id, $post_id, true );

    if ( empty( $is_user_liked ) )
       $is_user_liked = 0;

    $num_of_likes = get_post_meta( $post_id, 'post_likes', true );

    if ( empty( $num_of_likes ) )
       $num_of_likes = 0;
    
    $result = [];
    if ( $is_user_liked ) {
         $num_of_likes-=1;
         $result['value'] = 'dislike';
        update_user_meta( $user_id, $post_id, 0 );


     } else {

         $num_of_likes+=1;
         $result['value'] = 'like';
        update_user_meta( $user_id, $post_id, 1 );


     }
     $result['num_of_likes'] = $num_of_likes;
     update_post_meta( $post_id, 'post_likes', $num_of_likes );

     json_encode( $result );

     wp_send_json($result);

    //  echo $result;
     die();

 }

 function custom_like( $content ) {

    global $post;
    $user_id = get_current_user_id();
     $post_likes = get_post_meta( $post->ID, 'post_likes', true );
     if( empty( $post_likes ) ) {
         $post_likes = 0;
     }
     $is_like_enable = get_option('is_like_system_enabled');
     error_log('is_like_enable ' . $is_like_enable);

     if( $is_like_enable == "true") {

         $like_position = get_option( 'like_icon_position' );
         $like_icon = get_option( 'like_icon' );
         $like_url = "";
         if( $like_icon == "0" ) {
             $like_url = plugin_dir_url(__FILE__).'assets/images/before_like.png';
         } else {
            $like_url = plugin_dir_url(__FILE__).'assets/images/like_before.png';

         }

         ob_start();

         error_log('like_position ' . $like_position);

         if( $like_position == "3" || $like_position == "1" ) {
             ?>
              <div class="like_position" style="display:flex; justify-content: flex-end">
                <img class="click_on_like" float="right" style="width:30px; height:30px" src="<?php echo $like_url; ?>">
                <span class="num_of_likes"><?php _e( $post_likes. ' likes', 'likeposts'); ?></span>
             </div>
             <?php             
         } else if ( $like_position == "4" || $like_position == "0" ) {
            ?>
              <div class="like_position" style="display:flex; justify-content: flex-start">
                <img class="click_on_like" float="left" style="width:30px; height:30px" src="<?php echo $like_url; ?>">
                <span class="num_of_likes"><?php _e( $post_likes. ' likes', 'likeposts'); ?></span>


             </div>
         <?php  
         } else {
            ?>
               <div class="like_position" style="display:flex; alignt-items: center; justify-content: center;">
               <img class="click_on_like" float="center" style="width:30px; height:30px" src="<?php echo $like_url; ?>">
               <span class="num_of_likes"><?php _e( $post_likes. ' likes', 'likeposts'); ?></span>


            </div>
         <?php  
         }

     } else {
         error_log('else');
         return $content;
     }

     if( $like_position == "0" || $like_position == "1" || $like_position == "1" )
         return ob_get_clean().$content;
     else
          return $content . ob_get_clean();

 }
 function custom_admin_changes() {

     add_option( 'is_like_system_enabled', false );
     add_option( 'like_icon', 0 );
     add_option( 'like_icon_position', 0 );

 }

 function saved_like_system_info() {
     $isEnable = $_POST['isSystemEnable'];
     $fav_icon = $_POST['favIcon'];
     $icon_position = $_POST['iconPosition'];

     update_option( 'is_like_system_enabled', $isEnable );
     update_option( 'like_icon', $fav_icon );
     update_option( 'like_icon_position', $icon_position );

     echo 1;
     die();
 }


 function add_new_menu() {

     add_submenu_page( 
         'options-general.php', __('Favorite Posts', 'likeposts'), __('Favorite Posts', 'likeposts'), 'manage_options', 'favorite-posts', 'custom_favorite_post_page', 2 
        );
 }


 function custom_favorite_post_page() {

    $isEnable = get_option( 'is_like_system_enabled' );
    // error_log( 'isEnable ' . $isEnable);
    $fav_icon = get_option( 'like_icon' );
    error_log( 'fav_icon ' . $fav_icon);

    if( empty( $fav_icon ) ) {
        error_log('insert1');
        $fav_icon = '0';
    }
    $icon_position = get_option( 'like_icon_position' );
    error_log( 'icon_position ' . $icon_position);


    if( empty( $icon_position ) ) {
        error_log('insert2');

        $icon_position = '0';
    }

    ob_start();
    ?>
        <div class="container custom_container">
            <h2> <?php _e(' Welcome To THe Favorite Posts ', 'likeposts'); ?> </h2><br><br><hr>

            <div class="form-check form-switch custom_enable_like">

                <label class="form-check-label" for="flexSwitchCheckDefault"><strong><?php _e('Enable Like system on every posts', 'likeposts'); ?></strong></label>  

                <input class="form-check-input like_enable" type="checkbox" role="switch" id="flexSwitchCheckDefault" 
                <? if($isEnable  == 'true')  echo "checked"; ?>>

            </div><br><hr>

            <h3><?php _e('Select the icon', 'likeposts'); ?></h3>

            <input type="radio" id="fav_icon_love" name="fav_icon" value="0" <? if( $fav_icon == '0' ) echo 'checked'; ?>>
            <label for="fav_icon"><img src="<?php echo plugin_dir_url(__FILE__).'assets/images/before_like.png'; ?>"></label><br><br>
            <input type="radio" id="fav_icon_like" name="fav_icon" value="1" <? if( $fav_icon == '1' ) echo 'checked'; ?>>
            <label for="fav_icon_like"> <img src="<?php echo plugin_dir_url(__FILE__).'assets/images/like_before.png'; ?>"> </label><br><br>
            <hr>

            <h3><?php _e('Where do you want to display like icon?', 'likeposts'); ?></h3>

            <input type="radio" id="left_below_of_featured" name="display_like" value="0" <? if( $icon_position == '0' ) echo 'checked'; ?>>
            <label for="left_below_of_featured"> <?php _e( 'At left below of the featured image', 'likeposts' ); ?> </label><br>

            <input type="radio" id="right_below_of_featured" name="display_like" value="1" <? if( $icon_position == '1' ) echo 'checked'; ?>>
            <label for="right_below_of_featured"> <?php _e( 'At right below of the featured image', 'likeposts' ); ?> </label><br>

            <input type="radio" id="middle_below_of_featured" name="display_like" value="2" <? if( $icon_position == '2' ) echo 'checked'; ?>>
            <label for="middle_below_of_featured"> <?php _e( 'At middle below of the featured image', 'likeposts' ); ?> </label><br>


            <input type="radio" id="right_below_of_content" name="display_like" value="3" <? if( $icon_position == '3' ) echo 'checked'; ?>>
            <label for="right_below_of_content"> <?php _e( 'At right below of the content', 'likeposts' ); ?> </label><br>

            <input type="radio" id="left_below_of_content" name="display_like" value="4" <? if( $icon_position == '4' ) echo 'checked'; ?>>
            <label for="left_below_of_content"> <?php _e( 'At left below of the content', 'likeposts' ); ?> </label><br>

            <input type="radio" id="middle_below_of_content" name="display_like" value="5" <? if( $icon_position == '5' ) echo 'checked'; ?>>
            <label for="middle_below_of_content"> <?php _e( 'At middle below of the content', 'likeposts' ); ?> </label><br><br><hr>

            <button type="button" class="btn btn-primary save_like_system"><?php _e( 'Save Changes', 'likeposts'); ?></button>


          
        </div>
    <?
    
    echo ob_get_clean();

   
 }

 function frontend_custom_scripts() {

    global $post;

    $user_id = get_current_user_id();
    $post_id = $post->ID;

    wp_enqueue_style( 'custom-css', plugin_dir_url(__FILE__).'assets/css/style.css' );
    wp_enqueue_script( 'custom-js', plugin_dir_url(__FILE__).'assets/js/main.js' );

    wp_enqueue_script( 'like-js', plugin_dir_url(__FILE__).'assets/js/like-system.js' );

    wp_localize_script( 'like-js', 'likesystem', [
        'ajaxurl'   =>  admin_url('admin-ajax.php'),
        'post_id'   =>  $post_id,
        'user_id'   =>  $user_id
    ] );

  
 }

 function admin_custom_scripts( $hook ) {

    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css' );


    wp_enqueue_script( 'bootstrap-js1', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js', [ 'jquery'], '', true );

    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js', [ 'jquery'], '', true );

    wp_enqueue_style( 'custom-css', plugin_dir_url(__FILE__).'assets/css/style.css' );
    wp_enqueue_script( 'custom-js', plugin_dir_url(__FILE__).'assets/js/main.js' );
    wp_localize_script( 'custom-js', 'display_likes', [
        'ajaxurl'  =>  admin_url('admin-ajax.php')
    ]);
 }