<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

delete_option( 'jb_social_previews_twitter_on' );
delete_option( 'jb_social_previews_twitter_use_large' );
delete_option( 'jb_social_previews_twitter_username' );
delete_option( 'jb_social_previews_facebook_on' );
delete_option( 'jb_social_previews_image_url' );