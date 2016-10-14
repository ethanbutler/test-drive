<?php

/**
 * @package TestDrive
 * @version 1.0
 */
/*
Plugin Name: TestDrive
Author: Ethan Butler
Version: 1.0
Author URI: http://sqnts.xyz
*/

class TestDrive {

  function __construct($user_role){

    $this->user_role = $user_role;

    // Add Test User role.
    add_role( $this->user_role, ucwords(str_replace('_', ' ', $this->user_role) ), [
      'publish_posts' => true
    ]);

    // Redirect from permissions error page to edit posts page.
    add_action('init', function(){
      if(strpos($_SERVER['PHP_SELF'], '/wp-admin/index.php') !== false && $this->is_test_user()){
        wp_redirect( '/wp-admin/edit.php' );
        exit();
      }
    });

    //Removes posts once a user logs out.
    add_action('clear_auth_cookie', function(){
      if($this->is_test_user()){
        $user = wp_get_current_user();
        $posts_for_deletion = get_posts([
          'author' => $user->ID,
          'fields' => 'ids'
        ]);
        foreach($posts_for_deletion as $post_for_deletion){
          wp_delete_post($post_for_deletion, true);
        }
      }
    });
  }

  private function is_test_user(){
    $user = wp_get_current_user();
    return in_array($this->user_role, $user->roles);
  }

}

$TestDrive = new TestDrive('test_user');
