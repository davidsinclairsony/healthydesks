<?php

class MW_Cmspro_Block_Adminhtml_News_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {	
	 	parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'cmspro';
        $this->_controller = 'adminhtml_news';
        
        $this->_updateButton('save', 'label', Mage::helper('cmspro')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('cmspro')->__('Delete Item'));
        
        $this->_addButton('duplicate_button', array(
            'label'     => Mage::helper('cmspro')->__('Duplicate'),
        	'onclick'   => 'mwDuplicateEdit()',
            'class'     => 'save',
        ), -100);
        
        /*
        $this->_addButton('preview_button', array(
            'label'     => Mage::helper('cmspro')->__('Preview'),
        	'onclick'   => 'mwpreview()',
            'class'     => 'save',
        ), -100);
        */
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
        $duplicate = '\'' . $this->getDuplicateUrl() . '\'';
        $preview = '\'' . $this->getPreviewUrl() . '\'';
        $url_backup = '\'' . $this->getbackupUrl() . '\'';
        
           	        
	    //$html_images ='<td class="label"><label for="images">Images</label></td><td class="value"><a onclick="imagePreview("images_image"); return false;" href="http://127.0.0.1/magento1702a/media/'.$this->image_link.'"><img width="22" height="22" class="small-image-preview v-middle" alt="'.$this->image_link.'" title="'.$this->image_link.'" id="images_image" src="http://127.0.0.1/magento1702a/media/'.$this->image_link.'"></a> <input type="file" class="input-file" value="'.$this->image_link.'" name="images" id="images"><span class="delete-image"><input type="checkbox" id="images_delete" class="checkbox" value="1" name="images[delete]"><label for="images_delete"> Delete Image</label><input type="hidden" value="'.$this->image_link.'" name="images[value]"></span></td>';
 
        
        $this->_formScripts[] = "
        	
        	var action = $('edit_form').action;
			function toggleEditor() {
                if (tinyMCE.getInstanceById('news_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'news_content');
                } 
                else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'news_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function mwDuplicateEdit(){
                editForm.submit({$duplicate});
            }
            function mwpreview(){
            	$('edit_form').target = '_blank';
                editForm.submit({$preview});
                $('edit_form').action = action;
                $('edit_form').target = '_self';
            }
            
            document.observe('dom:loaded', function () {
            	$('news_backup').observe('change', function () {
            		if($('news_backup').value > 0){
						$('loading-mask').setStyle({display:'block'});            	
		            	new Ajax.Request({$url_backup}, {
						    method: 'post',
						    postBody: 'mw_news_backup='+$('news_backup').value,
						    onComplete: showResponse
			
						});
					}
				})
            
           });
           
           function showResponse(req)
			{
			   // tinymce.get('news_summary').execCommand('mceInsertContent',false, resp.summary); 
			   //tinymce.get('news_content').execCommand('mceInsertContent',false, resp.content);
			   resp = req.responseText.evalJSON();
			   
			   if(resp.title) $('title').value = resp.title;
			   if(resp.identifier) $('identifier').value = resp.identifier;
			   if(resp.category_id) $('category_id').value = resp.category_id;

			  
			   
			   tinymce.get('news_summary').setContent(resp.summary);
			   tinymce.get('news_content').setContent(resp.content);
			   
			   $('status').value = resp.status;
 
			   $('page_title').value = resp.page_title;
		
			   $('meta_keyword').value = resp.meta_keyword;
			
			   $('meta_description').value = resp.meta_description;
		
			   $('feature').value = resp.feature;
			
			   $('allowcomment').value = resp.allowcomment;
			
			   $('allow_show_image').value = resp.allow_show_image;
			  
			   $('guser').value = resp.groups;
			 
			   $('users').value = resp.users;
			   if($('news_author')) $('news_author').value = resp.author;
			   
			   if(resp.images != ''){
			   		$('images').up(1).update(resp.images);
			   }
				
				
			   $('loading-mask').setStyle({display:'none'});
			   
			  
			}
	
           
        ";
    }
	public function getbackupUrl()
    {
    	//return Mage::helper("adminhtml")->getUrl('cmspro/adminhtml_news/backup');
        return Mage::getUrl('cmspro/index/backup');
    }
	public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }
	public function getPreviewUrl()
    {
    	if( Mage::registry('cmspronews_data') && Mage::registry('cmspronews_data')->getNewsId() ) {
	    	$new_id = Mage::registry('cmspronews_data')->getNewsId();
	    	if($new_id){
	    		return $this->getUrl('cmspro/index/preview',array('id'=>$new_id));
	    	}
    	}
        	
        return $this->getUrl('cmspro/index/preview');
    }
    public function getHeaderText()
    {
        if( Mage::registry('cmspronews_data') && Mage::registry('cmspronews_data')->getNewsId() ) {
            return Mage::helper('cmspro')->__("Edit News '%s'", $this->htmlEscape(Mage::registry('cmspronews_data')->getTitle()));
        } else {
            return Mage::helper('cmspro')->__('Add News');
        }
    }
    
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cmspro/adminhtml/news/' . $action);
    }
}