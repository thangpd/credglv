jQuery(document).ready(function(){
    var currentUserTotalReferral = 0;
    var currentListUser;
    
    jQuery('input[name="search_by_join_sdate"]').mask('0000/00/00');
    jQuery('input[name="search_by_join_edate"]').mask('0000/00/00');
    jQuery('input[name="search_start_date"]').mask('0000/00/00');
    jQuery('input[name="search_end_date"]').mask('0000/00/00');

    jQuery('a.view_hierarchie').click(function(e){
        e.preventDefault();
        currentListUser = jQuery(this);
        jQuery.get('?load_referral_user_list=' + jQuery(this).data('id'), function(data){
            jQuery('#dialog_referral_user').html( data );
            currentUserTotalReferral = jQuery(currentListUser).data('total');
            currentUserName = jQuery(currentListUser).data('name');
            jQuery('#dialog_referral_user').dialog({
                  title: 'List of '+currentUserName+' Referrals ('+currentUserTotalReferral+')', 
                  modal: true,
                  resizable: false,
                  width: 350,
                  height: 400,
                  open: function( event, ui ) {
                        jQuery('#referral_user_form .wp-list-table').css('width','calc(100% - 350px)');
                        jQuery('body.woocommerce_page_wc_referral .ui-dialog').css('top', jQuery(currentListUser).position().top );       
                    },
                  close: function(){
                        jQuery('#referral_user_form .wp-list-table').css('width','100%');
                  }
            });     
        });
        return false;
    });
    jQuery('#dialog_referral_user').on('click', '.get_referral_user', function(e){
        e.preventDefault();
        jQuery('#dialog_referral_user').addClass('loading');
        currentListObj = jQuery(this).parent('div').parent('li');
        fetchRecords = jQuery(currentListObj).data('get');
        if (fetchRecords) {
            jQuery('#dialog_referral_user').removeClass('loading');
            jQuery(currentListObj).find('ul').first().toggle('slow');
        }else{
            jQuery.get('?load_referral_user_list=' + jQuery(currentListObj).data('id'), function(data){
                jQuery(currentListObj).append( data );
                jQuery(currentListObj).data('get', 1);
                jQuery('#dialog_referral_user').removeClass('loading');
            });   
        }
        return false;
    });
    jQuery('.active_referral_user').click(function(e){
        e.preventDefault();
        currentListObj = jQuery(this).parent('div').parent('li');
        if (confirm('Are sure want to active this user?')) {
            jQuery('.wrap').addClass('loading');
            jQuery.get('?active_referral_user=' + jQuery(this).data('id'), function(data){
                window.location.href = data;
            })
        }
    });
    jQuery('#dialog_referral_user').on('click', '.remove_referral_user', function(e){
        e.preventDefault();
        if (confirm('Are sure want to remove?')) {
        
        currentListObj = jQuery(this).parent('div').parent('li');
        
            jQuery('#dialog_referral_user').addClass('loading');
            
        jQuery.get('?remove_referral_user=' + jQuery(currentListObj).data('id'), function(data){
            currentUserName = jQuery(currentListUser).data('name');
            if (jQuery(currentListObj).parents('li').size() > 0) {
                currentListObj = jQuery(currentListObj).parents('li').first();
                jQuery(currentListObj).find('ul').remove()
                jQuery(currentListObj).append( data );
                
                count = parseInt(jQuery(currentListObj).find('.count').first().html());
                if (count > 0) {
                     jQuery(currentListObj).find('.count').first().html( count - 1 );
                }
                   
                jQuery(currentListObj).parents('li').each(function(){
                   count = parseInt(jQuery(this).find('.count').first().html());
                   if (count > 0) {
                        jQuery(this).find('.count').first().html( count - 1 );
                   }
                });
            }else{
                jQuery('#dialog_referral_user').html( data );
            }
            if( currentUserTotalReferral  > 0 ){
                currentUserTotalReferral = currentUserTotalReferral - 1
                jQuery('.ui-dialog-title').html( 'List of '+currentUserName+' Referrals ('+currentUserTotalReferral+')' );
                jQuery(currentListUser).parents('tr').find('td.no_of_followers').html( currentUserTotalReferral );
            }
            
            jQuery('#dialog_referral_user').removeClass('loading');
        });
        }
        return false;
    });
    jQuery('#referral_user_form #reset_button').click(function(){
       jQuery('#referral_user_form input[type=text]').val(''); 
       jQuery('#referral_user_form').submit(); 
    });
     jQuery('#form_widthdraw_filter #reset_button_withdraw').click(function(){
       jQuery('#form_widthdraw_filter input[type=text]').val(''); 
       jQuery('#form_widthdraw_filter').submit(); 
    });
    jQuery('input[type=radio][name=wmc-levelbase-credit]').change(function() {       
        if(this.value==1){
            jQuery('.wmc-optional').removeClass('wmc-hide');
            $totalLevels=jQuery('table.wmc-level-table .wmc-level').length;
            if(($totalLevels)>1){
                jQuery('.wmc-buttons #wmc-delete-last').show();
            }else{
                jQuery('.wmc-buttons #wmc-delete-last').hide();
            }
        }else{
            jQuery('.wmc-optional').addClass('wmc-hide');
            jQuery('.wmc-buttons #wmc-delete-last').hide();
        }
    });
    jQuery('#wmc-add-more').click(function(){
        $totalLevels=jQuery('table.wmc-level-table .wmc-level').length;        
        $row=jQuery('table.wmc-level-table .wmc-level[data-level=1]').clone();         
        $row.attr('data-level',$totalLevels+1);
        $row.find('label').attr('for','wmc-level-'+($totalLevels+1));
        $row.find('input[type=number]').attr('id','wmc-level-'+($totalLevels+1)).val(0);
        $row.find('label span').html($totalLevels+1);        
        jQuery('table.wmc-level-table tbody').append($row);
        jQuery('#wmc-max-level').val($totalLevels+1);
        if(($totalLevels+1)>1){
            jQuery('.wmc-buttons #wmc-delete-last').show();
        }else{
            jQuery('.wmc-buttons #wmc-delete-last').hide();
        }
    });
    jQuery('.wmc-buttons #wmc-delete-last').click(function(){
        $totalLevels=jQuery('table .wmc-level').length;
        if($totalLevels > 1){
            jQuery('table.wmc-level-table tr:last').remove();
            $totalLevels--;
            jQuery('#wmc-max-level').val($totalLevels);         
        }
        if($totalLevels < 2){           
            jQuery('.wmc-buttons #wmc-delete-last').hide();
        }
    });
});