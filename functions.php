<?php

            /**
             * Misc Functions used along with the mobile template plugin
             * 
             * @author Mat Lipe
             * @since 5.6.13
             */
          
function mobile_template_get_header(){
    global $gMobileTemplate;
    locate_template('/'.$gMobileTemplate['device'].'/header.php', true);   
    
}

function mobile_template_get_footer(){
    global $gMobileTemplate;
    locate_template('/'.$gMobileTemplate['device'].'/footer.php', true);   
    
    do_action('mobile-template-after-footer',$gMobileTemplate);
}    



function mobile_template_get_template_part($first, $second = false){
    global $gMobileTemplate;
    if( $second ){
        $second = '-'.$second;
    }
    locate_template('/'.$gMobileTemplate['device'].'/'.$first.$second.'.php', true);   
}  

function mobile_template_get_sidebar($name = 'sidebar' ){
    global $gMobileTemplate;
    if( $name != 'sidebar'){
        $sidebar = 'sidebar-'.$name;
    }
    locate_template('/'.$gMobileTemplate['device'].'/'.$sidebar.'.php', true);   
    
}  




function mobile_template_working_dir(){
    global $gMobileTemplate;
    return STYLESHEETPATH .'/'.$gMobileTemplate['device'].'/';
}
    
    

function mobile_template_url(){
    global $gMobileTemplate;
    return get_bloginfo('stylesheet_directory') .'/'.$gMobileTemplate['device'].'/';
}    