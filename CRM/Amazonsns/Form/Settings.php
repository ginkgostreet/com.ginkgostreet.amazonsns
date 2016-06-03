<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Amazonsns_Form_Settings extends CRM_Core_Form {

  /**
   * Set default values for the form. For edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return array
   */
  function setDefaultValues() {
    $defaults =  array();

    $defaults['amazon_sns_api_key'] = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_api_key");
    $defaults['amazon_sns_api_secret'] = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_api_secret");
    $defaults['amazon_sns_region'] = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_region");


    return $defaults;
  }

  function buildQuickForm() {

    // add form elements
    $this->add(
      'text', // field type
      'amazon_sns_api_key', // field name
      'Amazon SNS API Key', // field label
      null,
      true // is required
    );

    $this->add(
      'text', // field type
      'amazon_sns_api_secret', // field name
      'Amazon SNS API Secret', // field label
      null,
      true // is required
    );

    $this->add(
      'text', // field type
      'amazon_sns_region', // field name
      'Amazon SNS Region', // field label
      null,
      true // is required
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save Settings'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Handle the data submitted
   */
  function postProcess() {
    $values = $this->exportValues();

    CRM_Core_BAO_Setting::setItem(CRM_Utils_Array::value('amazon_sns_api_secret', $values),"com.ginkgostreet.amazonsns", "amazon_sns_api_secret");
    CRM_Core_BAO_Setting::setItem(CRM_Utils_Array::value('amazon_sns_api_key', $values),"com.ginkgostreet.amazonsns", "amazon_sns_api_key");
    CRM_Core_BAO_Setting::setItem(CRM_Utils_Array::value('amazon_sns_region', $values),"com.ginkgostreet.amazonsns", "amazon_sns_region");

    parent::postProcess();
    CRM_Core_Session::setStatus(ts("Settings Saved"), '', 'success');
  }

  function validate() {
    return parent::validate();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
