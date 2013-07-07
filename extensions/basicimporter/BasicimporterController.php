<?php
/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2013, {@link http://aksw.org AKSW}
 * @license   http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * Controller for OntoWiki Basicimporter Extension
 *
 * @category OntoWiki
 * @package  Extensions_Basicimporter
 * @author   Sebastian Tramp <mail@sebastian.tramp.name>
 */
class BasicimporterController extends OntoWiki_Controller_Component
{
    private $_model = null;
    private $_post = null;

    /**
     * init() Method to init() normal and add tabbed Navigation
     */
    public function init()
    {
        parent::init();

        OntoWiki::getInstance()->getNavigation()->disableNavigation();

        if ($this->_owApp->selectedModel === null) {
            $this->_owApp->appendMessage(
                new OntoWiki_Message(
                    $this->view->_('No model selected.'),
                    OntoWiki_Message::ERROR
                )
            );
            $this->view->errorFlag = true;
            return;
        } else {
            $this->_model = $this->_owApp->selectedModel;
        }

        if (!$this->_model->isEditable()) {
            $this->_owApp->appendMessage(
                new OntoWiki_Message(
                    $this->view->_('No write permissions on this model.'),
                    OntoWiki_Message::ERROR
                )
            );
            return;
        }

        // provide basic view data
        $action = $this->_request->getActionName();
        $this->view->placeholder('main.window.title')->set('Import Data');
        $this->view->formActionUrl    = $this->_config->urlBase . 'basicimporter/' . $action;
        $this->view->formEncoding     = 'multipart/form-data';
        $this->view->formClass        = 'simple-input input-justify-left';
        $this->view->formMethod       = 'post';
        $this->view->formName         = 'importdata';
        $this->view->supportedFormats = $this->_erfurt->getStore()->getSupportedImportFormats();

        // add a standard toolbar
        $toolbar = $this->_owApp->toolbar;
        $toolbar->appendButton(
            OntoWiki_Toolbar::SUBMIT,
            array('name' => 'Import Data', 'id' => 'importdata')
        )->appendButton(
            OntoWiki_Toolbar::RESET,
            array('name' => 'Cancel', 'id' => 'importdata')
        );
        $this->view->placeholder('main.window.toolbar')->set($toolbar);

        if ($this->_request->isPost()) {
            $this->_post = $this->_request->getPost();
        }

    }

    public function rdfpasterAction()
    {
        $this->view->placeholder('main.window.title')->set('Paste RDF Content');

        if ($this->_request->isPost()) {
            // TODO process post data
            $this->_owApp->appendSuccessMessage('Data successfully imported.');
            $this->_owApp->appendSuccessMessage($this->_post['filetype-paste']);
            $this->_owApp->appendSuccessMessage($this->_post['paste']);
        }
    }

    public function rdfwebimportAction()
    {
        $this->view->placeholder('main.window.title')->set('Import RDF from the Web');

        if ($this->_request->isPost()) {
            // TODO process post data
            $this->_owApp->appendSuccessMessage('Data successfully imported.');
            $this->_owApp->appendSuccessMessage($this->_post['location']);
        }
    }

    public function rdfuploadAction()
    {
        $this->view->placeholder('main.window.title')->set('Upload RDF Dumps');

        if ($this->_request->isPost()) {
            $upload = new Zend_File_Transfer();
            $filesArray = $upload->getFileInfo();

            $message = '';
            switch (true) {
                case empty($filesArray):
                    $message = 'upload went wrong. check post_max_size in your php.ini.';
                    break;
                case ($filesArray['source']['error'] == UPLOAD_ERR_INI_SIZE):
                    $message = 'The uploaded files\'s size exceeds the upload_max_filesize directive in php.ini.';
                    break;
                case ($filesArray['source']['error'] == UPLOAD_ERR_PARTIAL):
                    $message = 'The file was only partially uploaded.';
                    break;
                case ($filesArray['source']['error'] >= UPLOAD_ERR_NO_FILE):
                    $message = 'Please select a file to upload';
                    break;
            }
            if ($message != '') {
                $this->_owApp->appendErrorMessage($message);
                return;
            }

            // TODO process post data
            $this->_owApp->appendSuccessMessage('Data successfully imported.');
            $this->_owApp->appendSuccessMessage($this->_post['filetype-upload']);
        }
    }
}
