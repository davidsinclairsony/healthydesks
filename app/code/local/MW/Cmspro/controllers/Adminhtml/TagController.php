<?php
class MW_Cmspro_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cmspro')
            ->_addBreadcrumb(Mage::helper('cmspro')->__('Cmspro'), Mage::helper('cmspro')->__('Cmspro'));

        return $this;
    }
    
	/**
     * Prepare tag model for manipulation
     *
     * @return Mage_Tag_Model_Tag | false
     */
    protected function _initTag()
    {
        $model = Mage::getModel('cmspro/tag');

        if (($id = $this->getRequest()->getParam('tag_id'))) {
            $model->load($id);

            if (!$model->getId()) {
                return false;
            }
        }

        Mage::register('cmspro_current_tag', $model);
        return $model;
    }
    	
	/**
     * Show grid action
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Cmspro'))
             ->_title($this->__('Tags'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('cmspro')->__('Tags'), Mage::helper('cmspro')->__('Tags'))
            ->_setActiveMenu('cmspro/tag')
            ->_addContent($this->getLayout()->createBlock('cmspro/adminhtml_tag'))
            ->renderLayout();
    }
    
	/**
     * Action to draw grid loaded by ajax
     *
     */
    public function ajaxGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('cmspro/adminhtml_tag_grid')->toHtml());
    }
    
    /**
     * New tag action
     *
     */
    public function newAction()
    {
    	$this->_forward('edit');
    }
    
    /**
     * Edit tag action
     *
     */
    public function editAction()
    {
        $this->_title($this->__('Cmspro'))
             ->_title($this->__('Tags'));

        if (! ($model = $this->_initTag())) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
            return $this->_redirect('*/*/index'/*, array('store' => $this->getRequest()->getParam('store'))*/);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Tag'));

        Mage::register('cmspro_tag', $model);

        $this->_initAction()->renderLayout();
    }  

    /**
     * Save tag action
     *
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            //$data['base_popularity']    = (isset($postData['base_popularity'])) ? $postData['base_popularity'] : 0;
            //$data['store']              = $postData['store_id'];

            if (!$model = $this->_initTag()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
                return $this->_redirect('*/*/index'/*, array('store' => $data['store'])*/);
            }

            $model->addData($data);

            if (isset($postData['cmspro_tag_assigned_news'])) {
                $newsIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput($postData['cmspro_tag_assigned_news']);
                $newsTagModel = Mage::getModel('cmspro/newstag');
                $newsTagModel->assignNewsToTag($model, $newsIds);
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The tag has been saved.'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if (($continue = $this->getRequest()->getParam('continue'))) {
                    return $this->_redirect('*/*/edit', array('tag_id' => $model->getId()/*, 'store' => $model->getStoreId()*/, 'ret' => $continue));
                } else {
                    return $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);

                return $this->_redirect('*/*/edit', array('tag_id' => $model->getId()/*, 'store' => $model->getStoreId()*/));
            }
        }

        return $this->_redirect('*/*/index', array('_current' => true));
    }

    /**
     * Delete tag action
     *
     * @return void
     */
    public function deleteAction()
    {
        $model   = $this->_initTag();
        $session = Mage::getSingleton('adminhtml/session');

        if ($model && $model->getId()) {
            try {
                $model->delete();
                $session->addSuccess(Mage::helper('adminhtml')->__('The tag has been deleted.'));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        } else {
            $session->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete.'));
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/' . $this->getRequest()->getParam('ret', 'index')));
    }    

    /**
     * Assigned products (with serializer block)
     *
     */
    public function assignedAction()
    {
        $this->_title($this->__('Tags'))->_title($this->__('Assigned'));

        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Assigned products grid
     *
     */
    public function assignedGridOnlyAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }   

    /**
     * Massaction for removing tags
     *
     */
    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if(!is_array($tagIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('cmspro/tag')->load($tagId);
                    $tag->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }

    /**
     * Massaction for changing status of selected tags
     *
     */
    public function massStatusAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        //$storeId = (int)$this->getRequest()->getParam('store', 0);
        if(!is_array($tagIds)) {
            // No products selected
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('cmspro/tag')
                        ->load($tagId)
                        ->setStatus($this->getRequest()->getParam('status'));
                     $tag->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
    }
    
}