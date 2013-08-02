<?php

        /**
         * Main Class for the Mobile Template Plugin
         * 
         * @author Mat Lipe
         * @since 8.2.13
         */
class MobileTemplate {
        
       function __construct(){
           global $gMobileTemplate;
            $this->mobile = new MobileTemplateMobileDetect;   
            
            if( $this->is_phone() ){
                $gMobileTemplate['device'] = 'phone';  
            }   
            
            if( $this->is_tablet() ){
                $gMobileTemplate['device'] = 'tablet';
            }
            
            if( $this->is_mobile() ){
                $this->handleSwitchToDesktopLink();
                add_filter('template_include', array( $this, 'getMobileTemplate' ) );
                add_action('mobile-template-after-footer', array( $this, 'switchToDesktopLink' ) ); 
            }
              
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
               
                setcookie('desktop-version', 1, time()+86000); 
                 
           } elseif( $_GET['desktop-version'] == 'off' ){
               
                setcookie('desktop-version','', time()-86000);  
                 
           }
       }
       
       
       
       
       /**
        * Output a link to either switch to desktop or phone version
        * 
        * @since 8.2.13
        * 
        * @uses added to the 'mobile-template-after-footer' hook by self::__construct()
        */
       function switchToDesktopLink(){
           
           //to test if cookie work on this browser
           setcookie('test-cookie',1,time()+3600);
           if( !isset( $_COOKIE['test-cookie'] ) ) return;
           setcookie('test-cookie','',time()-3600);
           
           ?><div id="switch-to-desktop-link"><?php
                if( isset( $_COOKIE['desktop-version'] ) ){                   
                    $link = apply_filters('mobile-template-phone-link-text', 'switch to mobile site');
                    printf('<a href="?desktop-version=off" title="switch to mobile site" />', $link );  
                      
                } else {

                    $link = apply_filters('mobile-template-desktop-link-text', 'switch to full site');
                    printf('<a href="?desktop-version=on"/>', $link ); 
                    
                }
           ?></div><?php
           
       }
       
       
       
       /**
        * Created the file link to the phone version of the template file if exists
        * 
        * @uses called by construct on the 'template_include' filter
        * @since 8.2.13
        */
       function getMobileTemplate( $template ){
           global $post;
           
           if( isset( $_COOKIE['desktop-version'] ) ){
               return $template;
           }
           
           do_action('mobile-template-get-template', $template );
           
           $mobile_template = str_replace(MOBILE_TEMPLATE_THEME, MOBILE_TEMPLATE_THEME.'/'.$gMobileTemplate['device'].'/', $template );
           
           //For templates matching the names of pages or posts
           if( is_page() || is_single() ){
                $named_template = substr($mobile_template, 0, -4).'-'.$post->post_name.'.php';
                if( file_exists($named_template) ){
                    return $named_template;   
                }
           }

           if( file_exists($mobile_template) ){
               return $mobile_template;   
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
    