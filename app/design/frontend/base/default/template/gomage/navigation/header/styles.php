<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.2
 * @since        Class available since Release 1.0
 */

?>
<style type="text/css">

	 <?php
			$_color1 = Mage::getStoreConfig('gomage_navigation/filter/bg_color'); 
			$_color2 = Mage::getStoreConfig('gomage_navigation/filter/bg_color2');
			$_font_color = Mage::getStoreConfig('gomage_navigation/filter/font_color');
	 ?>
  
	/* Buttons Color */
	.block-layered-nav .block-content button.button span span{
    
   color: <?php echo Mage::helper('gomage_navigation')->formatColor($_font_color);?>;
    
    <?php if (Mage::getStoreConfig('gomage_navigation/filter/gradient') && $_color1 && $_color2): ?>
		background-color: <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>;
		background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>), to(<?php echo Mage::helper('gomage_navigation')->formatColor($_color2);?>));
		background-image: -webkit-linear-gradient(top, <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>, <?php echo Mage::helper('gomage_navigation')->formatColor($_color2);?>);
		background-image:    -moz-linear-gradient(top, <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>, <?php echo Mage::helper('gomage_navigation')->formatColor($_color2);?>);
		background-image:      -o-linear-gradient(top, <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>, <?php echo Mage::helper('gomage_navigation')->formatColor($_color2);?>);
		background-image:         linear-gradient(to bottom, <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>, <?php echo Mage::helper('gomage_navigation')->formatColor($_color2);?>);
    
    <?php else:?>
    background: <?php echo Mage::helper('gomage_navigation')->formatColor($_color1);?>;
    <?php endif;?>
	}  
		
	.gan-loadinfo{
		
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/ajaxloader/bordercolor')):?>
		border-color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/ajaxloader/textcolor')):?>
		color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/ajaxloader/bgcolor')):?>
		background-color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_width = intval(Mage::getStoreConfig('gomage_navigation/ajaxloader/width'))):?>
		width:<?php echo $_width;?>px !important;
		<?php endif;?>
		
		<?php if($_height = intval(Mage::getStoreConfig('gomage_navigation/ajaxloader/height'))):?>
		height:<?php echo $_height;?>px !important;
		<?php endif;?>
		
		<?php if(0 == intval(Mage::getStoreConfig('gomage_navigation/ajaxloader/enable'))):?>
		display:none !important;
		<?php endif;?>
		
	}
	/* Background Color */
	.block.block-layered-nav .block-content{
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/filter/style')):?>
		background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#E7F1F4;
		<?php endif;?>
	}
	
	/* Slider Color */	
	.narrow-by-list .gan-slider-span{
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/filter/slider_style')):?>
		background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#0000FF;
		<?php endif;?>
	}
	
	/* Popup Window Background */
	#gan-left-nav-main-container .filter-note-content-in,
	#gan-right-nav-main-container .filter-note-content-in,
    #gan-content-nav-main-container .filter-note-content-in,
	.narrow-by-list .filter-note-content-in{
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/filter/popup_style')):?>
		background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#FFFFFF;
		<?php endif;?>
	}
	
	/* Help Icon View */
	#gan-left-nav-main-container .filter-note-handle,
	#gan-right-nav-main-container .filter-note-handle,
    #gan-content-nav-main-container .filter-note-handle,
	.narrow-by-list .filter-note-handle{
		<?php if($_color = Mage::getStoreConfig('gomage_navigation/filter/icon_style')):?>
		color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color);?>;
		<?php else:?>
		color:#519cde;
		<?php endif;?>
	}
	<?php if (Mage::helper('gomage_navigation')->isGomageNavigation() &&
			  Mage::getStoreConfig('gomage_navigation/menubarsettings/navigation') == GoMage_Navigation_Model_Layer::FILTER_TYPE_PLAIN): ?>
		<?php $categories = $this->getStoreCategories(); ?>
		<?php $_size = intval($this->getRootCategory()->getData('navigation_pw_m_bsize')); ?>		
		<?php if ($_size === 0 && $this->getRootCategory()->getData('navigation_pw_m_bsize') != ''): ?>
			#gan_nav_top.gan-plain-list > li > a{
				border:1px solid;
			}		
			#gan_nav_top.gan-plain-list > li > a{
				border-right-width:0;
			}				
			#gan_nav_top.gan-plain-list > li.last > a{
				border-right-width:1px;
			}
		<?php elseif($_size == 1): ?>
			#gan_nav_top.gan-plain-list > li > a{
		  		border:1px solid;
		    }
		<?php elseif($_size >= 2): ?>
			#gan_nav_top.gan-plain-list > li > a{
			    border:1px solid;
			    margin-right:<?php echo ($_size - 1) ?>px;
		  	}	
		<?php endif; ?>
		<?php foreach ($categories as $cat): ?>
			#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a{
				<?php if ($_color = $cat->getData('navigation_pw_m_bcolor')): ?>
			 		border-color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
				<?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_bgcolor')): ?>
					background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
				<?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_tcolor')): ?>
			  		color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			    <?php endif; ?>
				<?php if ($_radius = $cat->getData('navigation_pw_m_bradius')): ?>
			 		-webkit-border-radius: <?php echo $_radius ?>px <?php echo $_radius ?>px 0 0;
				    -moz-border-radius: <?php echo $_radius ?>px <?php echo $_radius ?>px 0 0;
				    border-radius: <?php echo $_radius ?>px <?php echo $_radius ?>px 0 0; 
				<?php endif; ?>			 			
			}
			#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a:hover,
      		#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a.over{      		
				<?php if ($_color = $cat->getData('navigation_pw_m_otcolor')): ?>
			  		color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			    <?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_obgcolor')): ?>
					background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
				<?php endif; ?>			 			
			}
			#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a.active{
				<?php if ($_color = $cat->getData('navigation_pw_m_sccolor')): ?>
					background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
				<?php endif; ?>
			}
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a:hover,
		    #gan_nav_top.gan-plain-list > li.over.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a{
		          border-bottom:0;
		          z-index:1000;
		          position:relative;
		    }
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a:hover span,
		    #gan_nav_top.gan-plain-list > li.over.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a span{
        
          <?php $_size = intval($this->getRootCategory()->getData('navigation_pw_m_bsize')); ?>		
          <?php if ($_size === 0 && $this->getRootCategory()->getData('navigation_pw_m_bsize') != ''): ?>
            padding-bottom:0px;
          <?php elseif($_size == 1): ?>
            padding-bottom:1px;
          <?php elseif($_size >= 2): ?>
            padding-bottom:1px;
          <?php endif; ?>        
		    }      
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> .gan-plain{
		        <?php if (($_color = $cat->getData('navigation_pw_s_bcolor')) && ($_size = $cat->getData('navigation_pw_s_bsize'))): ?>
		          margin-top:-<?php echo $_size; ?>px;
		        <?php endif; ?>	
		    }
		    #gan_nav_top.gan-plain-list > li.gan-plain-style2.nav-<?php echo $cat->getId() ?> .gan-plain{
		        <?php if (($_color = $cat->getData('navigation_pw_s_bcolor')) && ($_size = $cat->getData('navigation_pw_s_bsize'))): ?>
		          margin-top:-<?php echo $_size; ?>px;
		        <?php endif; ?>	
		    }      
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level1 > a{			
			  <?php if ($_color = $cat->getData('navigation_pw_fl_tcolor')): ?>
			  	color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_size = $cat->getData('navigation_pw_fl_tsize')): ?>
			  	font-size:<?php echo $_size; ?>px;
			  <?php endif; ?>
			  <?php if ($_color = $cat->getData('navigation_pw_fl_bgcolor')): ?>
			  	background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level1 > a:hover,
      		#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level1 > a.active{
			  <?php if ($_color = $cat->getData('navigation_pw_fl_otcolor')): ?>
			  	color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_color = $cat->getData('navigation_pw_fl_obgcolor')): ?>
			  	background:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level2 > a{
			  <?php if ($_color = $cat->getData('navigation_pw_sl_tcolor')): ?>
			  	color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_size = $cat->getData('navigation_pw_sl_tsize')): ?>
			  	font-size:<?php echo $_size; ?>px;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level2 > a:hover,
      		#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level2 > a.active{
			  <?php if ($_color = $cat->getData('navigation_pw_sl_otcolor')): ?>
			  	color:<?php echo Mage::helper('gomage_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
		<?php endforeach; ?>
	<?php endif; ?>

     <?php
     if ( Mage::helper('gomage_navigation')->isEnterprise()):
     ?>
         .sidebar-enterprise-block-layered-nav .currently { border:1px solid #d1d1d1; border-width:1px 0; padding:10px 10px 10px 17px; margin-bottom:-1px; background:#f4f4f4 url(<?php echo $this->getSkinUrl('images/bkg_currently.gif');?>) repeat-x 0 -20px; }
         .sidebar-enterprise-block-layered-nav .currently .block-subtitle { display:block; margin-bottom:5px; }
         .sidebar-enterprise-block-layered-nav .currently li { padding:5px 26px 5px 0; color:#444; position:relative; z-index:1; }
         .sidebar-enterprise-block-layered-nav .currently li .btn-remove { position:absolute; right:-7px; top:4px; }
         .sidebar-enterprise-block-layered-nav .currently li .btn-previous { position:absolute; right:12px; top:4px; }
         .sidebar-enterprise-block-layered-nav .currently .label { font-weight:bold; color:#d33911; font-size:12px; display:inline-block; }
         .sidebar-enterprise-block-layered-nav .currently .value { display:inline-block; }
         .sidebar-enterprise-block-layered-nav .currently .btn-remove { float:right; width:13px; height:0; padding-top:12px; margin-top:3px; overflow:hidden; background:url(<?php echo $this->getSkinUrl('images/btn_remove.gif');?>) no-repeat 0 0; }
     <?php
     endif;
     ?>
</style>

<script type="text/javascript">
//<![CDATA[

<?php		
	$text = trim(Mage::getStoreConfig('gomage_navigation/ajaxloader/text')) ? trim(Mage::getStoreConfig('gomage_navigation/ajaxloader/text')) : $this->__('Loading, please wait...');
	$text = addslashes(str_replace("\n", "<br/>", str_replace("\r", '', $text)));

	$currentCategory = Mage::registry('current_category');
            	
	if ( ($currentCategory && $currentCategory->getData('navigation_pw_gn_shopby') == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::USE_GLOBAL)
			||
		  !$currentCategory )
	{
		$position = Mage::getStoreConfig('gomage_navigation/general/show_shopby');
	}	        
	else if( $currentCategory )
	{
	   	$position = $currentCategory->getData('navigation_pw_gn_shopby');
	}
?>

var GomageNavigation = new GomageNavigationClass({
			<?php if($loadimage = Mage::getStoreConfig('gomage_navigation/ajaxloader/loadimage')):?>
			loadimage: '<?php echo Mage::getBaseUrl("media")."gomage/config/".$loadimage;?>',
			<?php else:?>
			loadimage: '<?php echo $this->getSkinUrl("images/gomage/loadinfo.gif");?>',
			<?php endif;?>
			loadimagealign: '<?php echo Mage::getStoreConfig('gomage_navigation/ajaxloader/imagealign');?>',
			back_to_top_action: '<?php echo Mage::getStoreConfig('gomage_navigation/general/back_to_top_action');?>',
			gomage_navigation_loadinfo_text: "<?php echo $text?>",
			
			<?php if ($url = $this->getNavigationCatigoryUrl()): ?>
				gan_static_navigation_url: '<?php echo $url; ?>',
				gomage_navigation_urlhash: false,
			<?php else:?>
				gomage_navigation_urlhash: <?php if (Mage::getStoreConfig('gomage_navigation/filter_settings/urlhash')): ?>true<?php else: ?>false<?php endif; ?>,
			<?php endif; ?>

		    <?php if (Mage::getStoreConfig('gomage_navigation/general/autoscrolling') == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Autoscrolling::AJAX): ?>	
			    gan_more_type_ajax: true,	
			<?php endif; ?>
			gan_shop_by_area: <?php echo intval($position); ?>,
            <?php if ( Mage::getStoreConfigFlag('gomage_navigation/filter/show_help') ): ?>
            help_icon_open_type: 'click',
            <?php else: ?>
            help_icon_open_type: 'over',
            <?php endif; ?>

            scrolling_speed: '<?php echo Mage::getStoreConfig('gomage_navigation/general/scrolling_speed');?>',
		});
//]]>	
</script>