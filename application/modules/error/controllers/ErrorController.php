<?php
class error_ErrorController extends Zend_Controller_Action
{
    public function init()
	{
		// Base url
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->baseUrl = $this->_request->getBaseUrl();
		$this->view->page = $this->_request->controller . '_' . $this->_request->action;
	}
    public function errorAction()
    {
        // Vide le contenu de la réponse
        $this->getResponse()->clearBody();

        $errors = $this->_getParam('error_handler');
		$request_params = $errors->request->getParams();

		$this->_helper->layout->disableLayout();

        switch($errors->type)
		{
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        $this->view->exception = $errors->exception;
        $this->view->request   = $request_params;

		// Meta
		$this->view->defaut_titre = "Résidéclic";
		$this->view->defaut_description = "Résidéclic, le réseau social de votre copropriété";
		$this->view->defaut_mots_cles = "réseau social, copropriété, entraide, solidarité";

        $config = Zend_Registry::get('config');
        if($config->phpSettings->display_errors == "0")
            $this->_redirect("/");

    }
}