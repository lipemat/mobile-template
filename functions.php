<?php

            /**
             * Misc Functions used along with the mobile template plugin
             * 
             * @author Mat Lipe
             * @since 11.8.13
             */

             
/**
 * Gets the header.php file form the mobile template
 * 
 * @actions include
 * mobile-template-before-header
 * mobile-template-after-header
 * 
 * @since 11.8.13
 * 
 * @see get_footer()
 */          
function mobile_template_get_header($name = null){
    global $gMobileTemplate;
    
    if ( isset($name) )
        $templates[] = '/'.$gMobileTemplate['device']."/header-{$name}.php";

    $templates[] = '/'.$gMobileTemplate['device'].'/header.php';
    
    do_action('mobile-template-before-header',$gMobileTemplate);
    locate_template( $templates, true);  
    do_action('mobile-template-after-header',$gMobileTemplate); 
    
}


/**
 * Gets the footer.php file form the mobile template
 * 
 * @actions include
 * mobile-template-before-footer
 * mobile-template-after-footer
 * 
 * @since 11.8.13
 * 
 * @see get_footer()
 */
function mobile_template_get_footer($name = null){
    global $gMobileTemplate;
    
    if ( isset($name) )
        $templates[] = '/'.$gMobileTemplate['device']."/footer-{$name}.php";

    $templates[] = '/'.$gMobileTemplate['device'].'/footer.php';
    
    do_action('mobile-template-before-footer',$gMobileTemplate);
    locate_template( $templates, true);     
    do_action('mobile-template-after-footer',$gMobileTemplate);
}    



/**
* Output a link to either switch to desktop or phone version
* 
* @since 1.1.0
*
* @uses MobileTemplate::switchToDesktopLink()
*/
function mobile_template_switch_to_desktop_link(){
   MobileTemplate::switchToDesktopLink();   
}



/**
 * Includes a template file from the mobile template
 * 
 * @param string $first - the type of template
 * @param string [$second] - the particluar template name
 * 
 * @see get_template_part()
 */
function mobile_template_get_template_part($first, $second = false){
    global $gMobileTemplate;
    if( $second ){
        $second = '-'.$second;
    }
    locate_template('/'.$gMobileTemplate['device'].'/'.$first.$second.'.php', true);   
}  


/**
 * Includes a sidebar file from the mobile template
 * 
 * @param string [$name] - the name of the sidebar file minus the 'sidebar-' 
 * 
 * @see get_sidebar()
 */
function mobile_template_get_sidebar($name = 'sidebar' ){
    global $gMobileTemplate;
    if( $name != 'sidebar'){
        $sidebar = 'sidebar-'.$name;
    }
    locate_template('/'.$gMobileTemplate['device'].'/'.$sidebar.'.php', true);   
    
}  


/**
 * Outputs a js file form the /js folder of the mobile theme
 * 
 * @uses add to your header.php for footer.php file
 * 
 * @since 9.3.13
 * 
 * @param string $file - the js file
 */
function mobile_template_js_file($file){
    if( strpos( $file, '.js') === false ){
        $file = $file.'.js';
    }   
    printf('<script type="text/javascript" src="%sjs/%s" ></script>', mobile_template_url(), $file );
}


/**
 * Outputs a css file from the / folder of the mobile theme
 * 
 * @uses add to your header.php for footer.php file
 * 
 * @since 8.23.13
 * 
 * @param string $file - the css file
 */
function mobile_template_css_file($file){   
    printf('<link rel="stylesheet" type="text/css" media="all" href="%s%s" />', mobile_template_url(), $file );
}


/**
 * Return the current mobile directory
 * 
 * @return string
 */
function mobile_template_working_dir(){
    global $gMobileTemplate;
    return STYLESHEETPATH .'/'.$gMobileTemplate['device'].'/';
}
    
    
/**
 * Return the current mobile url
 * 
 * @return string
 */
function mobile_template_url(){
    global $gMobileTemplate;
    return get_bloginfo('stylesheet_directory') .'/'.$gMobileTemplate['device'].'/';
}    