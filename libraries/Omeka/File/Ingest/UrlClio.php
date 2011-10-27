<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Ingest URLs into the Omeka archive.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 */
class Omeka_File_Ingest_UrlClio extends Omeka_File_Ingest_Url
{
  

    /**
     * Get a HTTP client for retrieving the given file.
     *
     * @param string $source Source URI.
     * @return Zend_Http_Client
     */
    protected function _getHttpClient($source)
    {
        $client = parent::_getHttpClient($source);
        $adapter = new Zend_Http_Client_Adapter_Curl();
        $client->setAdapter($adapter);
        return $client;
    }

  
}
