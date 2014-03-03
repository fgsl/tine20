<?php
/**
 * Tine 2.0

 * @package     Sales
 * @subpackage  Model
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Alexander Stintzing <a.stintzing@metaways.de>
 * @copyright   Copyright (c) 2013 Metaways Infosystems GmbH (http://www.metaways.de)
 */

/**
 * class to hold Invoice data
 *
 * @package     Sales
 * @subpackage  Model
 */

class Sales_Model_Invoice extends Tinebase_Record_Abstract
{
    /**
     * holds the configuration object (must be declared in the concrete class)
     *
     * @var Tinebase_ModelConfiguration
     */
    protected static $_configurationObject = NULL;
    
    /**
     * Holds the model configuration (must be assigned in the concrete class)
     *
     * @var array
     */
    protected static $_modelConfiguration = array(
        'recordName'        => 'Invoice',
        'recordsName'       => 'Invoices', // ngettext('Invoice', 'Invoices', n)
        'hasRelations'      => TRUE,
        'hasCustomFields'   => TRUE,
        'hasNotes'          => TRUE,
        'hasTags'           => TRUE,
        'modlogActive'      => TRUE,
        'hasAttachments'    => TRUE,
        'createModule'      => TRUE,
        'containerProperty' => NULL,
        'defaultFilter'     => 'description',
        'titleProperty'     => 'description',
        'appName'           => 'Sales',
        'modelName'         => 'Invoice',
        
        'filterModel' => array(
            'contract' => array(
                'filter' => 'Tinebase_Model_Filter_ExplicitRelatedRecord',
                'label' => 'Contract', // _('Contract')
                'options' => array(
                    'controller' => 'Sales_Controller_Contract',
                    'filtergroup' => 'Sales_Model_ContractFilter',
                    'own_filtergroup' => 'Sales_Model_InvoiceFilter',
                    'own_controller' => 'Sales_Controller_Invoice',
                    'related_model' => 'Sales_Model_Contract',
                ),
                'jsConfig' => array('filtertype' => 'sales.invoicecontract')
            ),
            'customer' => array(
                'filter' => 'Tinebase_Model_Filter_ExplicitRelatedRecord',
                'label' => 'Customer', // _('Customer')
                'options' => array(
                    'controller' => 'Sales_Controller_Customer',
                    'filtergroup' => 'Sales_Model_CustomerFilter',
                    'own_filtergroup' => 'Sales_Model_InvoiceFilter',
                    'own_controller' => 'Sales_Controller_Invoice',
                    'related_model' => 'Sales_Model_Customer',
                ),
                'jsConfig' => array('filtertype' => 'sales.invoicecustomer')
            ),
        ),
        
        'fields'            => array(
            'number' => array(
                'label' => 'Invoice Number', //_('Invoice Number')
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => TRUE),
            ),
            'description' => array(
                'label'   => 'Description', // _('Description')
                'type'    => 'text',
                'queryFilter' => TRUE,
            ),
            'address_id'       => array(
                'label'      => 'Address',    // _('Address')
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => FALSE),
                'type'       => 'record',
                'shy' => TRUE,
                'config' => array(
                    'appName'     => 'Sales',
                    'modelName'   => 'Address',
                    'idProperty'  => 'id',
                )
            ),
            'fixed_address' => array(
                'label'      => 'Address',    // _('Address')
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => TRUE),
            
            ),
            'date' => array(
                'type' => 'date',
                'label'      => 'Date',    // _('Date')
            ),
            'start_date' => array(
                'type' => 'date',
                'label'      => 'Interval Begins',    // _('Interval Begins')
            ),
            'end_date' => array(
                'type' => 'date',
                'label'      => 'Interval Ends',    // _('Interval Ends')
            ),
            'credit_term' => array(
                'title' => 'Credit Term', // _('Credit Term')
                'type'  => 'integer',
                'default' => 10
            ),
            'costcenter_id' => array(
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => TRUE, Zend_Filter_Input::DEFAULT_VALUE => NULL),
                'label' => 'Cost Center', //_('Cost Center')
                'type'  => 'record',
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => TRUE, Zend_Filter_Input::DEFAULT_VALUE => NULL),
                'config' => array(
                    'appName'     => 'Sales',
                    'modelName'   => 'CostCenter',
                    'idProperty'  => 'id'
                )
            ),
            'description' => array(
                'label' => 'Description', //_('Description')
                'validators' => array(Zend_Filter_Input::ALLOW_EMPTY => TRUE, Zend_Filter_Input::DEFAULT_VALUE => NULL)
            ),
            'cleared' => array(
                'label' => 'Cleared', //_('Cleared')
                'default' => 'TO_CLEAR',
                'type' => 'keyfield',
                'name' => Sales_Config::INVOICE_CLEARED
            ),
            'type' => array(
                'label' => 'Type', //_('Type')
                'default' => 'INVOICE',
                'type' => 'keyfield',
                'name' => Sales_Config::INVOICE_TYPE
            ),
            'is_auto' => array(
                'type' => 'bool',
                'label' => NULL
            ),
            'contract' => array(
                'type' => 'virtual',
                'config' => array(
                    'type' => 'relation',
                    'label' => 'Contract',    // _('Contract')
                    'config' => array(
                        'appName'   => 'Sales',
                        'modelName' => 'Contract',
                        'type' => 'CONTRACT'
                    )
                )
            ),
            'customer' => array(
                'type' => 'virtual',
                'config' => array(
                    'type' => 'relation',
                    'label' => 'Customer',    // _('Customer')
                    'config' => array(
                        'appName'   => 'Sales',
                        'modelName' => 'Customer',
                        'type' => 'CUSTOMER'
                    )
                )
            ),
        )
    );
    
    /**
     * @see Tinebase_Record_Abstract
     */
    protected static $_relatableConfig = array(
        array('relatedApp' => 'Sales', 'relatedModel' => 'Contract', 'config' => array(
            array('type' => 'CONTRACT', 'degree' => 'sibling', 'text' => 'Contract', 'max' => '0:0'), // _('Contract')
        ), 'defaultType' => 'CONTRACT'
        ),
        array('relatedApp' => 'Sales', 'relatedModel' => 'Customer', 'config' => array(
            array('type' => 'CUSTOMER', 'degree' => 'sibling', 'text' => 'Customer', 'max' => '0:0'), // _('Customer')
        ), 'defaultType' => 'CUSTOMER'
        ),
        array('relatedApp' => 'Sales', 'relatedModel' => 'Invoice', 'config' => array(
            array('type' => 'REVERSAL', 'degree' => 'sibling', 'text' => 'Reversal Invoice', 'max' => '1:1'), // _('Reversal Invoice')
        ), 'defaultType' => 'REVERSAL'
        )
    );
}
