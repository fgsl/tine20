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
interface Syncope_Backend_IFolderState
{
    /**
     * @param Syncope_Model_IFolderState $_folderState
     * @return Syncope_Model_IFolderState
     */
    public function create(Syncope_Model_IFolderState $_folderState);
    
    /**
     * delete all stored folderId's for given device
     *
     * @param Syncope_Model_Device $_deviceId
     * @param string $_class
     */
    public function resetState($_deviceId);
    
    /**
     * get array of ids which got send to the client for a given class
     *
     * @param Syncope_Model_Device $_deviceId
     * @param string $_class
     * @return array
     */
    public function getClientState($_deviceId, $_class);
}
