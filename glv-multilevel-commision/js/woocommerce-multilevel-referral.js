
function checkReferralProgramValue( $val ){
    switch ( $val ) {
        case "1":
            jQuery('.referral_terms_conditions').removeClass('hide');
            jQuery('.referral_code_panel').removeClass('hide');
            break;
        case "2":
            jQuery('.referral_terms_conditions').removeClass('hide');
            //jQuery('.referral_code_panel').val('');
            jQuery('.referral_code_panel').addClass('hide');
            break;
        case "3":
            jQuery('.referral_terms_conditions').addClass('hide');
            jQuery('.referral_code_panel').addClass('hide');
            break;
    }
}
jQuery(document).ready(function(){
    // Handle store credit limit
    jQuery('.store_credit_notice a').click(function(e){
        e.preventDefault();
        jQuery(this).parent().siblings('form').toggle('fast');
    });
    
    if ( jQuery('.woocommerce input[name="join_referral_program"]').size() > 0 ) {
        checkReferralProgramValue( jQuery('.woocommerce input[name="join_referral_program"]:checked').val() );
    }
    jQuery('.woocommerce input[name="join_referral_program"]').click(function(e){
        checkReferralProgramValue( jQuery(this).val() );
    });
    jQuery('.btn-invite-friends').click(function(e){
       e.preventDefault();
       jQuery('#dialog-invitation-form').toggleClass('hide');
    });
    jQuery("#wmc-social-media .wmc-banner-list select").on('change',function () {
           var selectBox=jQuery(this);
           var optionSelected = selectBox.find("option:selected");
           selectBox.attr("disabled", true);
           var image=optionSelected.data('image');
           var attachId=optionSelected.data('attachid');
           var title=optionSelected.data('title');
           var desc=optionSelected.data('desc');           
           var url=optionSelected.data('url');
           var fn=wmcAjax.URL+'images/icons.png';           
           
           jQuery('#wmc-social-media .share42init').attr('data-url',url).attr('data-title',title).attr('data-description',desc);
           jQuery('#wmc-social-media .wmc-banner-preview img').attr("src",selectBox.data('loader'));
           if(optionSelected.data('preset')=='yes'){
               jQuery.ajax({
                 type : "post",
                 dataType : "json",
                 url : wmcAjax.ajaxurl,
                 data : {action: "wmcChangeBanner", attachId : attachId,bTitle:title,bDesc:desc},
                 success: function(response) { 
                    if(response.type== "success") {                    
                       jQuery('#wmc-social-media .wmc-banner-preview').fadeOut(500, function() { 
                            var source= response.imageURL;                            
                            jQuery('#wmc-social-media .share42init').attr('data-image',source);
                            jQuery('#wmc-social-media .wmc-banner-preview img').attr("src",source);                                                 jQuery('#wmc-social-media .wmc-banner-preview').fadeIn(500);
                        }); 
                    }
                    selectBox.removeAttr("disabled");
                 }
              })  
           }else{
              jQuery('#wmc-social-media .wmc-banner-preview').fadeOut(500, function() {                     
                    jQuery('#wmc-social-media .share42init').attr('data-image',image)
                    jQuery('#wmc-social-media .wmc-banner-preview img').attr("src",image);                                                 jQuery('#wmc-social-media .wmc-banner-preview').fadeIn(500);
                });  
           }             
        //}
    });
    jQuery('#wmc-social-media .wmcShareWrapper a').click(function(e){
        e.preventDefault();
        var sharedButton=jQuery(this);
        var selectBox=jQuery("#wmc-social-media .wmc-banner-list select");
        var optionSelected = selectBox.find("option:selected");
        var cTitle=jQuery('#wmc-social-media #wmcBannerTitle').val();
        var cDesc=jQuery('#wmc-social-media #wmcBannerDescription').val();                
        var image=jQuery('#wmc-social-media .wmc-banner-preview img').attr("src");
        var attachId=optionSelected.data('attachid');
        var title=optionSelected.data('title');
        var desc=optionSelected.data('desc');
        var wmcShareWrapper = jQuery('#wmc-social-media .wmcShareWrapper');
        var url = wmcShareWrapper.data('url');
        console.log(url);
        cTitle=cTitle==''?title:cTitle;
        cDesc=cDesc==''?desc:cDesc;
        var shareURL=newWindow='';
        if( !sharedButton.hasClass('wmc-button-whatsup') ){
            newWindow = window.open('', '_blank', 'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0');
        }
        switch(sharedButton.data('count')){
            case 'fb':
                shareURL+='//www.facebook.com/sharer/sharer.php?u=';
            break;
            case 'gplus':
                shareURL+='//plus.google.com/share?url=';
            break;
            case 'lnkd':
                shareURL+='//www.linkedin.com/shareArticle?mini=true&amp;title='+cTitle+'&amp;url=';
            break;
            case 'pin':
                shareURL+='//pinterest.com/pin/create/button/?media='+image+'&amp;description='+cDesc+'&amp;url=';
            break;
            case 'twi':
                shareURL+='//twitter.com/intent/tweet?text='+cTitle+'&amp;url=';
            break;
            case 'whatsup':
                if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    shareURL+='whatsapp://send?text=';
                }else{
                    shareURL+='//web.whatsapp.com/send?text=';
                }
                
            break;
        }
        if( sharedButton.hasClass('wmc-button-whatsup') ){
			url = sharedButton.data('account')+'?ru='+sharedButton.data('ru')+'&title='+encodeURI(cTitle)+'&content='+encodeURI(cDesc)+'&image='+encodeURI(image)+'&share='+sharedButton.data('share');
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				shareURL+=encodeURIComponent(url);
				//shareURL = 'whatsapp://send?text=http%3A%2F%2Fbaszicare.prismitsolutions.com%2Fmy-account%2Freferral%2F%3Fru%3D8c6d2%26title%3Dxxx';
				console.log(shareURL);
				sharedButton.attr('href',shareURL);
				window.location.href=shareURL;
			}else{
				shareURL = 'https://web.whatsapp.com/send?text=';
				shareURL+=encodeURIComponent(url);
		        jQuery('<a href="'+ shareURL +'" target="_blank"></a>')[0].click();
			}
			return true;
		}
        shareURL+=encodeURI(url);
        jQuery.ajax({
             type : "post",
             dataType : "json",
             url : wmcAjax.ajaxurl,
             data : {action: "wmcSaveTransientBanner", attachId : attachId,bTitle:cTitle,bDesc:cDesc},
             success: function(response) { 
                 if(response.type== "success") {
                     if( sharedButton.hasClass('wmc-button-whatsup') ){
                        sharedButton.attr('href',shareURL);
                        newWindow.location.href=shareURL;
                          return true;
                     }else{
                         newWindow.location.href=shareURL;
                         console.log(shareURL);
                         return false;
                     }
                 }
             }
        });    
                
    });
    jQuery('.wmc-show-affiliates a.view_hierarchie').on('click',function(){
        var parentID=jQuery(this).data('finder');
        if(jQuery(this).hasClass('wmcOpen')){
            jQuery(this).removeClass('wmcOpen').addClass('wmcClose');
            jQuery('.wmc-show-affiliates').find('[class*=wmc-child-'+parentID+']').hide();
            jQuery('.wmc-show-affiliates').find('[class*=wmc-child-'+parentID+'] a.view_hierarchie').removeClass('wmcOpen').addClass('wmcClose');
        }else{
            jQuery(this).removeClass('wmcClose').addClass('wmcOpen');
            jQuery('.wmc-show-affiliates .wmc-child-'+parentID).show();
        }        
    });
    jQuery('.woocommerce-checkout select#join_referral_program').on('change',function () {
        var optionSelected = jQuery(this).find("option:selected");
        selectedValue=optionSelected.val();
        console.log(selectedValue);
        referralCode=jQuery('.woocommerce-checkout input#referral_code');
        if(selectedValue==1){
            if(referralCode.val()==''){
               referralCode.closest('p').addClass('woocommerce-invalid'); 
            }else{
               referralCode.closest('p').removeClass('woocommerce-invalid').addClass('woocommerce-valid');  
            }
            jQuery('.woocommerce-checkout #referral_code_field').show();
            jQuery('.woocommerce-checkout #termsandconditions_field').show();
            jQuery('.woocommerce-checkout #termsandconditions_field label.checkbox').removeClass('hidden');
        }else if(selectedValue==2){
            jQuery('.woocommerce-checkout #referral_code_field').hide();
            jQuery('.woocommerce-checkout #termsandconditions_field').show(); 
            jQuery('.woocommerce-checkout #termsandconditions_field label.checkbox').removeClass('hidden');           
        }else if(selectedValue==3){
            jQuery('.woocommerce-checkout #referral_code_field').hide();
            jQuery('.woocommerce-checkout #termsandconditions_field').hide(); 
            jQuery('.woocommerce-checkout #termsandconditions_field label.checkbox').addClass('hidden');               
        }
    });
    jQuery('.woocommerce-checkout select#join_referral_program').trigger("change");
});