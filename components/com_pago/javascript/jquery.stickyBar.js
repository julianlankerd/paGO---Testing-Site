/*
* jQuery stickyBar Plugin
* Copyright (c) 2010 Brandon S. <antizeph@gmail.com>
* Version: 1.1.2 (09/14/2010)
* http://plugins.jquery.com/project/stickyBar
* 
* Usage (simple):      $.stickyBar(div);
* Usage (advanced):    $.stickyBar(divTarget, {'showClose' : true, 'divBase' : divBase});
* 
* Notes:    divTarget is the div you want to be stickied (and by default is also divBase).
*           divBase is the target to scroll past to invoke stickyBar.
*           showClose displays a small 'x' that closes stickyBar
*/
(function(jQuery){
    
	
	jQuery.fn.stickyBar = function(o){
        jQuery.stickyBar(o);
    }

    jQuery.stickyBar = function(divTarget, options){
        menuTop = options;
        var defaults = {
            'divBase'   : '',
            'showClose' : false
        };
        settings = jQuery.extend(defaults, options);

        var wrapped = 0; //initial value
        
        //if divBase is a defined option, set the stickyBarTop value to it, otherwise, use divTarget
        divTargetBase = (settings.divBase) ? divTargetBase = settings.divBase : divTargetBase = divTarget;

        var stickyBarTop = jQuery(divTargetBase).offset().top;
        jQuery(window).scroll(function(){
            var scrollPos = jQuery(window).scrollTop();

            if (scrollPos > stickyBarTop){
                if (wrapped == 0){                
                    jQuery(divTarget).wrap('<div class="sticky">');
                    jQuery(".sticky").css({
                                'position'    : "fixed",
                                'top'         : menuTop+"px",
                                'right'       : "30px",
                                'z-index'     : "9999"
                            });
                    wrapped = 1;

                    if (settings.showClose){
                        jQuery(".sticky").append('<div class="stickyClose" style="left:95%;position:absolute;color:#fff;top:0;left:98%;cursor:pointer">x</div>');
                        jQuery(".stickyClose").click(function(){
                            jQuery(".sticky").slideUp();
                            setTimeout(function(){
                                jQuery(divTarget).unwrap();
                                jQuery(".stickyClose").remove();
                            },400);
                            wrapped = 2; //won't happen again on the page until a refresh
                        });
                    }

                }
            } else {
                if (wrapped == 1){
                    jQuery(divTarget).unwrap();
                    jQuery(".stickyClose").remove();
                    wrapped = 0;
                }
            }
        });
    };
}) (jQuery);