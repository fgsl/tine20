<?php
/**
 * Tine 2.0
 *
 * @package     Syncope
 * @subpackage  Backend
 * @license     http://www.tine20.org/licenses/agpl-nonus.txt AGPL Version 1 (Non-US)
 *              NOTE: According to sec. 8 of the AFFERO GENERAL PUBLIC LICENSE (AGPL), 
 *              Version 1, the distribution of the Syncope module in or to the 
 *              United States of America is excluded from the scope of this license.
 * @author      Lars Kneschke <l.kneschke@metaways.de>
 * @copyright   Copyright (c) 2009-2012 Metaways Infosystems GmbH (http://www.metaways.de)
 * 
 */

/**
 * sql backend class for the folder state
 *
 * @package     Syncope
 * @subpackage  Backend
 */
class Syncope_Backend_FolderState implements Syncope_Backend_IFolderState
{
    /**
     * the database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    
    public function __construct(Zend_Db_Adapter_Abstract $_db)
    {
        $this->_db = $_db;
    }
    
    /**
     * create new folder state
     *
     * @param Syncope_Model_IFolderState $_folderState
     * @return Syncope_Model_IFolderState
     */
    public function create(Syncope_Model_IFolderState $_folderState)
    {
        $id = sha1(mt_rand(). microtime());
        $deviceId = $_folderState->device_id instanceof Syncope_Model_IDevice ? $_folderState->device_id->id : $_folderState->device_id;
    
        $this->_db->insert('syncope_folderstates', array(
        	'id'             => $id, 
        	'device_id'      => $deviceId,
        	'class'          => $_folderState->class,
        	'folderid'       => $_folderState->folderid,
        	'creation_time'  => $_folderState->creation_time->format('Y-m-d H:i:s'),
        	'lastfiltertype' => $_folderState->lastfiltertype
        ));
        
        return $this->get($id);
    }
    
    /**
     * @param string  $_id
     * @throws Syncope_Exception_NotFound
     * @return Syncope_Model_IFolderState
     */
    public function get($_id)
    {
        $select = $this->_db->select()
            ->from('syncope_folderstates')
            ->where('id = ?', $_id);
    
        $stmt = $this->_db->query($select);
        $state = $stmt->fetchObject('Syncope_Model_FolderState');
        
        if (! $state instanceof Syncope_Model_IFolderState) {
            throw new Syncope_Exception_NotFound('id not found');
        }
        
        if (!empty($state->creation_time)) {
            $state->creation_time = new DateTime($state->creation_time, new DateTimeZone('utc'));
        }
    
        return $state;
    }
    
    /**
     * delete all stored folderId's for given device
     *
     * @param Syncope_Model_Device|string $_deviceId
     * @param string $_class
     */
    public function resetState($_deviceId)
    {
        $deviceId = $_deviceId instanceof Syncope_Model_IDevice ? $_deviceId->id : $_deviceId;
         
        $where = array(
            $this->_db->quoteInto($this->_db->quoteIdentifier('device_id') . ' = ?', $deviceId)
        );
        
        $this->_db->delete('syncope_folderstates', $where);
    }
    
    public function update(Syncope_Model_IFolderState $_state)
    {
        $deviceId = $_state->device_id instanceof Syncope_Model_IDevice ? $_state->device_id->id : $_state->device_id;
    
        $this->_db->update('syncope_folderstates', array(
        	'lastfiltertype'     => $_state->lastfiltertype
        ), array(
        	'id = ?' => $_state->id
        ));
    
        return $this->get($_state->id);
    }
    
    /**
     * get array of ids which got send to the client for a given class
     *
     * @param Syncope_Model_Device|string $_deviceId
     * @param string $_class
     * @return array
     */
    public function getClientState($_deviceId, $_class)
    {
        $deviceId = $_deviceId instanceof Syncope_Model_IDevice ? $_deviceId->id : $_deviceId;
        
        $select = $this->_db->select()
            ->from('syncope_folderstates', array('folderid'))
            ->where($this->_db->quoteIdentifier('device_id') . ' = ?', $deviceId)
            ->where($this->_db->quoteIdentifier('class') . ' = ?', $_class);
        
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_COLUMN);

        return $result;
    }
    
    /**
     * get array of ids which got send to the client for a given class
     *
     * @param Syncope_Model_Device|string $_deviceId
     * @param string $_class
     * @return array
     */
    public function getFolderState($_deviceId, $_folderId)
    {
        $deviceId = $_deviceId instanceof Syncope_Model_IDevice ? $_deviceId->id : $_deviceId;
        
        $select = $this->_db->select()
            ->from('syncope_folderstates')
            ->where($this->_db->quoteIdentifier('device_id') . ' = ?', $deviceId)
            ->where($this->_db->quoteIdentifier('folderid') . ' = ?', $_folderId);
        
        $stmt = $this->_db->query($select);
        $state = $stmt->fetchObject('Syncope_Model_FolderState');
        
        if (! $state instanceof Syncope_Model_IFolderState) {
            throw new Syncope_Exception_NotFound('id not found');
        }
        
        if (!empty($state->creation_time)) {
            $state->creation_time = new DateTime($state->creation_time, new DateTimeZone('utc'));
        }
        
        return $state;
    }
}
