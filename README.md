Mobile Template - WordPress plugin
===============

A WordPress plugin for extremely simple and lightweight mobile theming. 

#Usage

Once activated the plugin will do all the tough stuff for you. Simple create a folder titled either 'phone' or 'tablet' in 
your theme and add your WordPress named template files in this folder and they will automatically be served up to 
viewers using that type of device. 

If you would like a universal theme used for all mobile devices (both phones and tablets) create a folder titled 'mobile'


For instance, if you want to use a custom template for your homepage for only viewers using a mobile phone, 
you would create a folder named phone and add an home.php file inside of it.

##Function files
Simply add a functions.php file to your phone or tablet theme and it will be pulled in and usable on the front end or
your site for that particular device.

For a functions file that will be used only if this plugin is activated create a file name mobile-template-functions.php 
in the root of your theme (not phone or tablet folder). This file will be pulled in like a normal functions.php file
only when the plugin is active and on all devices and page.

##Themeing Functions
There are mobile versions of many of the standard WordPress theme functions available for use to keep things mobile specific.
The are titled the same as the standard versions simply with a 'mobile_template_' before the name. 

Here is a list of many of them
```php
/**
 * Gets the header.php file form the mobile template
 * 
 * @actions include
 * mobile-template-before-header
 * mobile-template-after-header
 * 
 * 
 * @see get_footer()
 */          
function mobile_template_get_header(){
    global $gMobileTemplate;
    do_action('mobile-template-before-header',$gMobileTemplate);
    locate_template('/'.$gMobileTemplate['device'].'/header.php', true);  
    do_action('mobile-template-after-header',$gMobileTemplate); 
    
}


/**
 * Gets the footer.php file form the mobile template
 * 
 * @actions include
 * mobile-template-before-footer
 * mobile-template-after-footer
 * 
 * 
 * @see get_footer()
 */
function mobile_template_get_footer(){
    global $gMobileTemplate;
    do_action('mobile-template-before-footer',$gMobileTemplate);
    locate_template('/'.$gMobileTemplate['device'].'/footer.php', true);   
    do_action('mobile-template-after-footer',$gMobileTemplate);
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
 * @since 8.23.13
 * 
 * @param string $file - the js file
 */
function mobile_template_js_file($file){   
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
```

##Test Mode
When this plugin is active you will find a Mobile Options menu under settings to enable Test Mode. This mode will
let you view the mobile theme if you are logged in and show the standard theme to all other viewers.


##Theme Switching
If you use the mobile_template_get_footer() function to output your footer this will automatically create a switch
to full site link. If a viewer has already switched to full site it will display a link to return to the mobile version.


##Actions and Filters
There are many built in filters and actions to tap into. Below is a list of some of them and their arguments
in no particular order. 
```php
apply_filters('mobile-template-phone-link-text', 'switch to mobile site');

apply_filters('mobile-template-desktop-link-text', 'switch to full site');

do_action('mobile-template-get-template', $template );

do_settings_sections( 'mobile-template' );

apply_filters('mobile-template-menu-args', $args, $gMobileTemplate, $this );

add_filter('template_include', array( $this, 'replaceMobileTemplate' ) );

do_action('mobile-template-before-header',$gMobileTemplate);

do_action('mobile-template-after-header',$gMobileTemplate); 

do_action('mobile-template-before-footer',$gMobileTemplate);

```

Seriously though, There is very little code in this plugin and everything is pretty well documented in the code
so you can probably figure out what everything is doing by taking a quick browse.






