<?php

        /**
         * Main Class for the Mobile Template Plugin
         * 
         * @author Mat Lipe
         * @since 8.23.13
         */
class MobileTemplate {
       private $theme_dir;
       private $parent_theme_dir;
    
       /**
        * @since 8.23.13
        */
       function __construct(){
           global $gMobileTemplate;
           $this->mobile = new MobileTemplateMobileDetect;   
           $this->theme_dir = get_stylesheet_directory();
           $this->parent_theme_dir = get_template_directory();
           
            if( $this->is_phone() ){
                $gMobileTemplate['device'] = 'phone';  

            }   
            
            if( $this->is_tablet() ){
                $gMobileTemplate['device'] = 'tablet';
            }

            if( $this->is_mobile() ){
                if( file_exists($this->getMobileTemplate('functions.php')) ){
                    require($this->getMobileTemplate('functions.php') );
                }
           
                $this->handleSwitchToDesktopLink();
                add_filter('template_include', array( $this, 'replaceMobileTemplate' ) );
            }
            
            add_action('admin_menu', array( $this, 'addSettingsPage' ) );
              
       }
       
       
       
       /**
        * Adds the Mobile Options Settings Page
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
          <?php
       }
       
       
       
       /**
        * Gets the matching Mobile Template if it exists in the device named folder in the theme
        * 
        * @since 8.23.13
        * @param string $template - $template name
        * 
        * @return bool|string - the retrieved template or false if not exists
        */
       function getMobileTemplate($template){
           global $post, $gMobileTemplate;
           $new_template = false;

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
                $named_template = substr($mobile_template, 0, -4).'-'.$post->post_name.'.php';
                if( file_exists($named_template) ){
                    $new_template = $named_template;   
                }
           }

           if( !$new_template ){
               if( file_exists($mobile_template) ){ 
                    $new_template = $mobile_template;   
               }
           }
           
           return $new_template;
           
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
        * @since 8.23.13
        * 
        * @uses added to the 'mobile-template-after-footer' hook by self::__construct()
        */
       function switchToDesktopLink(){
           
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
        * Created the file link to the phone version of the template file if exists
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
    