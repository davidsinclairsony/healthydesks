<?php require_once('recaptchalib.php');
class MW_Cmspro_CommentController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){}
	
	 public function postAction()
    {
        $post = $this->getRequest()->getPost();
		
        if ( $post ) {		
            try {
						$session = Mage::getSingleton('customer/session');
						$cus = $session->getCustomerId();//var_dump($cus);die;
						
						if(!isset($cus)||$cus <1){
							$privatekey = Mage::getStoreConfig("mw_cmspro/news/privatekey_capcha"); // This is the private key you get from recpatcha registration
							//var_dump($privatekey);die;
							$nonekey=true;
							if($privatekey==null||$privatekey==''||$privatekey==' '||$privatekey=='  ')
							$nonekey=false;
							else {$resp = recaptcha_check_answer ($privatekey,
							$_SERVER["REMOTE_ADDR"],
							$_POST["recaptcha_challenge_field"],
							$_POST["recaptcha_response_field"]);
							$nonekey==true;
							};//var_dump($resp);die;	
									if($nonekey==false||$resp->is_valid == true){
										$post['customer_id'] = 0;
										$this->saveData($post);
									}else{
										if($resp->error =="invalid-site-private-key")
										Mage::getSingleton('core/session')->addError(Mage::helper('cmspro')->__('Private key of reCaptcha is invalid.Please contact to admin for fixing this problem!Thank so much!'));
										else
										Mage::getSingleton('core/session')->addError(Mage::helper('cmspro')->__('Wrong captcha '));
									}
						}else{
							$post['customer_id'] = $cus;
							$this->saveData($post);
						}			
							$this->_redirectReferer();
							return;
            } catch (Exception $e) {
			//echo $e;die;
                $translate->setTranslateInline(true);

                Mage::getSingleton('core/session')->addMessage('Unable to submit your request. Please, try again later');
                $this->_redirectReferer();
                return;
            }

        } else {
           $this->_redirectReferer();
        }
	}
	
	private function saveData($post){
		
			// Init model data
			$model = Mage::getModel('cmspro/comment');
			
			 $model->setData($post);
			 if ($model->getCreatedTime == NULL) {
				$model->setCreatedTime(now());
			}
			 $model->save();
			 
	   // echo "<pre>";
	   // print_r($post);
	   // print_r($model);
	   // echo "</pre>";
	   // die;	
	   
			// $translate->setTranslateInline(true);
			 Mage::getSingleton('core/session')->addSuccess(Mage::helper('cmspro')->__('Comment sent success and will watting for approved by administrator'));
				
	}
	
}