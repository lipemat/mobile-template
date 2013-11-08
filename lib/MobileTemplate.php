<?php

        /**
         * Main Class for the Mobile Template Plugin
         * 
         * @author Mat Lipe
         * @since 11.7.13
         */
class MobileTemplate {
       public $theme_dir;
       public $parent_theme_dir;
       public $desktop_test_mode = false;
       public $mobile_folder_exists = false;
       
       
       /**
        * @since 10.14.13
        */
       function __construct(){
           global $gMobileTemplate;
           
           $this->mobile = new MobileTemplateMobileDetect;   
           $gMobileTemplate['theme_dir'] = $this->theme_dir = get_stylesheet_directory();
           $gMobileTemplate['parent_theme_dir'] = $this->parent_theme_dir = get_template_directory();

           //Setup proper theme device folder
           $this->setDeviceFolder();
           
           //the comments template
           
           
           add_action('after_setup_theme', array( $this, 'includeMobileFunctions' ) ); 
           add_action('admin_menu', array( $this, 'addSettingsPage' ) );
           add_action('admin_init', array( $this, 'setupSettings') );
           add_action('wp', array( $this, 'initMobileTheme' ) );
   
       }

       
       /**
        * Setup proper theme's device folder
        * 
        * @uses check for a mobile, phone, or tablet folder - will use device before mobile if exists
        * 
        * @uses called by self::__construct()
        * @since 1.2.0
        * 
        * 
        */
       function setDeviceFolder(){
           global $gMobileTemplate;
           
           //Check for a mobile folder
           if( file_exists($this->theme_dir.'/mobile')){
                $this->mobile_folder_exists = true;
           }
           
           //If this device is a phone
           if( $this->is_phone() ){
                if( file_exists($this->theme_dir.'/phone')){
                   $gMobileTemplate['device'] = 'phone';  
                } elseif( $this->mobile_folder_exists) {
                    $gMobileTemplate['device'] = 'mobile';  
                }
           }   
            
           //If this device is a tablet
           if( $this->is_tablet() ){
                if( file_exists($this->theme_dir.'/tablet')){
                   $gMobileTemplate['device'] = 'tablet';  
                } elseif( $this->mobile_folder_exists) {
                    $gMobileTemplate['device'] = 'mobile';  
                }
           }
           
           
           
           
       }


       
       /**
        * Adds ablity to include some default functions when the plugin is active
        * 
        * @since 8.23.13
        * 
        * @uses added to the theme_loaded hook by self::__construct()
        * 
        * @uses create a file in the root of theme titled 'mobile-template-functions.php' and it will load on each page load
        */
       function includeMobileFunctions(){
           if( file_exists($this->theme_dir.'/mobile-template-functions.php') ){
               include($this->theme_dir.'/mobile-template-functions.php');
           }
       }
       

        /**
         * Puts the mobile Theme functionality in motion
         * 
         * @since 11.7.13
         * @uses added to the wp hook by self::__construct()
         */
        function initMobileTheme(){
           //if test mode is on 
           if( $this->checkForTestMode() ){
               return;
           }
           
           if( $this->is_mobile() || $this->desktop_test_mode ){
                    
                //functions file    
                if( file_exists($this->getMobileTemplate('functions.php')) ){
                    require($this->getMobileTemplate('functions.php') );
                }
                
                //comments file
                if( file_exists($this->getMobileTemplate('comments.php')) ){
                    add_filter('comments_template', array( $this, 'commentsTemplate' ) );   
                } 
                
           
                $this->handleSwitchToDesktopLink();
                
                add_filter('template_include', array( $this, 'replaceMobileTemplate' ) );
            } 
        }
        
        
        /**
         * Returns location of the mobile theme's comments.php template
         * 
         * @uses added to the comments_template filter by self::initMobileTheme()
         * 
         * @since 11.7.13
         */
        function commentsTemplate(){
            return $this->getMobileTemplate('comments.php');
        }
        
        

       /**
        * Checks if test mode is on
        * Then verfies of a user is logged in and has rights to see the mobile version
        * 
        * @since 9.11.13
        * 
        * @uses called by self::initMobileTheme()
        * 
        * @return bool - true if should not display the mobile theme
        */
       function checkForTestMode(){
          global $gMobileTemplate;
        
          if( get_option('mobile_template_test_mode') != 'on' ){
              return false;
          }

          if( !current_user_can(get_option('mobile_template_capability')) ){
              return true;    
          }
 
          if( !$this->is_mobile() ){
              if( get_option('mobile_template_show_desktop') == 'on' ){
                 $this->desktop_test_mode = true;
                 $gMobileTemplate['device'] = get_option('mobile_template_show_device');
                 if( !file_exists($this->theme_dir.'/'.$gMobileTemplate['device']) ){
                    $gMobileTemplate['device'] = 'mobile';     
                 }
                 return false;
              } else {
                 return true;
              }
          } else {
              return false;
          }
           
          return true;
       }


       /**
        * Adds the settings the mobile-template option 
        * 
        * @since 8.26.13
        * 
        * @uses added to the admin_init hook by self::__construct()
        */
       function setupSettings(){
           register_setting( 'mobile-template', 'mobile_template_test_mode' );
           register_setting( 'mobile-template', 'mobile_template_capability' );
           register_setting( 'mobile-template', 'mobile_template_show_desktop' );
           register_setting( 'mobile-template', 'mobile_template_show_device' );
       }
       
       
       
       /**
        * Adds the Mobile Options Settings Page
        * 
        * @since 8.26.13
        * 
        * @filters include
        * apply_filters('mobile-template-menu-args', $args, $gMobileTemplate, $this )
        * 
        * @uses added to the admin_menu hook by self::__construct()
        */
       function addSettingsPage(){
            global $gMobileTemplate;

            $args = array(
                        'title'           => 'Mobile Options',
                        'capability'      => 'manage_options',
                        'slug'            => 'mobile-options',
                        'parent'          => 'options-general.php',
                        'output_function' => array( $this, 'settingsPage' )
                        );
                        
            $args = apply_filters('mobile-template-menu-args', $args, $gMobileTemplate, $this );
                        
            add_submenu_page($args['parent'], $args['title'], $args['title'], $args['capability'], $args['slug'], $args['output_function'] );

       }
       
       
       /**
        * The output of the settings Page
        * 
        * @since 8.23.13
        * 
        * @uses create with add_submenu_page by self::addSettingsPage()
        */
       function settingsPage(){
          ?><div class="wrap">
            <?php screen_icon(); ?>
                <h2>Mobile Options</h2>
                <form method="post" action="options.php">
                    <?php settings_fields( 'mobile-template' ); ?>
                    <?php do_settings_sections( 'mobile-template' ); ?>
                    
                    
                    <?php $test_mode = get_option('mobile_template_test_mode'); ?>
                    
                    <h3>Test Mode:</h3>
                    <p class="description">This mode will allow only logged in users to see the mobile theme.</p>
                    <select name="mobile_template_test_mode">
                        <option value="off" <?php selected('off', $test_mode); ?>>off</option>
                        <option value="on" <?php selected('on', $test_mode); ?>>on</option>
                    </select>
                    <p>&nbsp;</p>
                    
                    <?php $show_desktop = get_option('mobile_template_show_desktop'); ?>
                    <?php $show_device = get_option('mobile_template_show_device'); ?>
                   
                    <h3>Show Mobile Theme On Desktops:</h3>
                    <p class="description">This will display the mobile theme on desktops when logged in and test mode is on.</p>
                    <select name="mobile_template_show_desktop">
                        <option value="off" <?php selected('off', $show_desktop); ?>>off</option>
                        <option value="on" <?php selected('on', $show_desktop); ?>>on</option>
                    </select>
                    <br>
                    Device's Theme to Show:<select name="mobile_template_show_device">
                        <option value="phone" <?php selected('phone', $show_device); ?>>phone</option>
                        <option value="tablet" <?php selected('tablet', $show_device); ?>>tablet</option>
                    </select>
                     <p>&nbsp;</p>


                    <?php $capability = get_option('mobile_template_capability'); ?>
                    
                    <h3>Minimum User Role For Test Mode:</h3>
                    <p class="description">With test mode enabled this is the mimimum user role who will see the mobile theme.</p>
                    <select name="mobile_template_capability">
                        <option value="read" <?php selected('red', $capability); ?>>Any Logged In User</option>
                        <option value="edit_posts" <?php selected('edit_posts', $capability); ?>>Editor</option>
                        <option value="manage_options" <?php selected('manage_options', $capability); ?>>Administrator</option>
                    </select>
                     <p>&nbsp;</p>
                     
                    <?php submit_button(); ?>

                </form>
            </div>     
            <?php
       }
       
       
       
       /**
        * Gets the matching Mobile Template if it exists in the device named folder in the theme
        * 
        * @since 10.7.13
        * @param string $template - $template name
        * 
        * @return bool|string - the retrieved template or false if not exists
        */
       function getMobileTemplate($template){
           global $post, $gMobileTemplate;

           do_action('mobile-template-get-template', $template );
           
           //if lookup is for a file without a path
           if( sizeof(explode('/',$template)) == 1 ){
               $mobile_template = $this->theme_dir.'/'.$gMobileTemplate['device'].'/'.$template;
           } else {
               //If the lookup has a path attahced check if child theme or parent theme
                $mobile_template = str_replace($this->theme_dir, $this->theme_dir.'/'.$gMobileTemplate['device'], $template );
                if( $this->theme_dir != $this->parent_theme_dir ){
                    $mobile_template = str_replace($this->parent_theme_dir, $this->theme_dir.'/'.$gMobileTemplate['device'], $mobile_template );
                }
           }

           //For templates matching the names of pages or posts
           if( is_page() || is_single() ){
                $pre = substr($mobile_template, 0, -4);
                $named_template = $pre.'-'.$post->post_name.'.php';
                $id_template = $pre.'-'.$post->ID.'.php';
                $type_template = $pre.'-'.$post->post_type.'.php';
                if( file_exists($named_template) ){
                    return $named_template;   
                } elseif( file_exists($id_template ) ){
                    return $id_template;
                } elseif( file_exists( $type_template ) ){
                    return $type_template;   
                }
           }

           if( file_exists($mobile_template) ){ 
              return $mobile_template;   
           }
           
           return false;
           
       }
       
       
       
       
       /**
        * Sets or removes a cookie to switch a viewer to and from the desktop version
        * 
        * @since 8.2.13
        * 
        * @uses called during self::__construct if is_mobile() is true
        */
       function handleSwitchToDesktopLink(){
           if( !isset( $_GET['desktop-version'] ) ) return;
           
           if( $_GET['desktop-version'] == 'on' ){
                $_COOKIE['desktop-version'] = 1;
                setcookie('desktop-version', 1, time()+86000); 
                 
           } elseif( $_GET['desktop-version'] == 'off' ){
                unset( $_COOKIE['desktop-version'] );
                setcookie('desktop-version','', time()-86000);  
                 
           }
       }
       
       
       
       
       /**
        * Output a link to either switch to desktop or phone version
        * 
        * @since 8.26.13
        * 
        * @uses added to the 'mobile-template-after-footer' hook by self::initMobileTheme()
        */
       public function switchToDesktopLink(){
           
           ?><div id="switch-to-desktop-link" style="text-align: center; padding: 5px 0;"><?php
                if( isset( $_COOKIE['desktop-version'] ) ){                   
                    $link = apply_filters('mobile-template-phone-link-text', 'switch to mobile site');
                    printf('<a href="?desktop-version=off" title="switch to mobile site" />%s</a>', $link );  
                      
                } else {

                    $link = apply_filters('mobile-template-desktop-link-text', 'switch to full site');
                    printf('<a href="?desktop-version=on">%s</a>', $link ); 
                    
                }
           ?></div><?php
           
       }
        
       /**
        * Created the file link to the mobile version of the template file if exists
        * 
        * @uses called by construct on the 'template_include' filter
        * @since 8.23.13
        */
       function replaceMobileTemplate( $template ){
               
           $new_template = $this->getMobileTemplate($template);
           
           if( $new_template ){
               if( isset( $_COOKIE['desktop-version'] ) ){                 
                  add_action('wp_footer', array( $this, 'switchToDesktopLink' ) );
                  return $template;
               } else {
                  add_action('mobile-template-after-footer', array( $this, 'switchToDesktopLink' ) ); 
                  return $new_template;
               }
           }

           return $template;
       }
       
       
       
        /**
     * Checks to see if on a mobile device
     * @uses will return true if on a phone or tablet
     * @see is_phone() or is_tablet() for more refined
     * @return boolean
     * @since 1.7.13
     */
    function is_mobile(){
        
        //placeholder for the results so we don't have to run again
        if( isset( $this->ismobile ) ) return $this->ismobile;
        
        if( $this->mobile->isMobile() ){
            $this->ismobile = true;
        
        } else {
            $this->ismobile = false;
        }
        return $this->ismobile;
    }
    
    
    /**
     * Detects if on a specific device
     * @param string $device the device by
     *  * Mobile Browser
     *  * Operating System
     *  * Name
     * @uses for a complete list see the protected vars on the mat_Mobile_Detect Class
     * @return boolean
     * @since 4.22.13
     */
    function is_mobile_device( $device ){
        
        if( $this->mobile->{'is'.$device}() ){
            return true;
        }
        return false;
    }

    /**
     * Checks to see if on a tablet
     * @uses will return true if on a tablet
     * @see is_mobile() or is_phone() for other detections
     * @return boolean
     * @since 4.22.13
     */
    function is_tablet(){

        //placeholder for the results so we don't have to run again
        if( isset( $this->istablet ) ) return $this->istablet;
        
        if( $this->mobile->isTablet() ){
            $this->istablet = true;
        } else {
            $this->istablet = false;
        }
        
        return $this->istablet;

    }
    
    
    /**
     * Checks to see if on a phone
     * @uses will return true if on a phone and not on a tablet
     * @see is_mobile() or is_tablet() for other detections
     * @return boolean
     * @since 2.19.12
     */
    function is_phone(){
    
       //placeholder for the results so we don't have to run again
        if( isset( $this->isphone ) ) return $this->isphone;
    
        if( $this->mobile->isMobile() && !$this->mobile->isTablet() ){
            $this->isphone = true;
        } else {
            $this->isphone = false;
        }
        return $this->isphone;
    }
    
        
}
    