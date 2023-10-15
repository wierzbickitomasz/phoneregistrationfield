<?php

class AdminPhoneregistrationfieldController extends ModuleAdminController
{
    public function l($sttring, $class = NULL, $addslashes = false, $htmlentities = true)
    {
        return $sttring;
    }

    public function __construct()
    {

        $this->table = "customer";
        $this->className = "CustomerThread";
        $this->fields_list = array(
            'id_customer' => array('title' => 'id_customer'),
            'phone' => array('title' => 'phone'),
        );

        $this->bootstrap = true;
        $this->addRowAction("view");
        $this->addRowAction("edit");
        $this->addRowAction("delete");

        // Set fields form for form view
        $this->context = Context::getContext();
        $this->context->controller = $this;
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add / Edit Comment'),
            ),
            'input' => array(
                array('type' => 'text', 'label' => $this->l('the parameter value'), 'name' =>
                    'parameter', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('the parameter value'), 'name' =>
                    'id_mymodule', 'size' => 30, 'required' => true)
            ),

            'submit' => array('title' => $this->l('Save'))
        );


        parent::__construct();
    }

    public function postProcess()
    {
        return parent::postProcess(); // TODO: Change the autogenerated stub
    }

    public function renderView()
    {
        // TODO: Change the autogenerated stub
    }

}