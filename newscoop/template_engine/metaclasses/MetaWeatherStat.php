<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaWeatherStat extends MetaDbObject
{
    /** @var array */
    private static $m_baseProperties = array(
        'id' => 'id',
    );

    private static $m_defaultCustomProperties = array(
        'location_id' => 'getLocationId',
        //'location_name' => 'getLocationName'
    );

    public $location_name;

    public function __construct($id = null)
    {
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\WeatherStat');

        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        if(is_null($id))
            $this->m_dbObject = $repository->getPrototype();
        else
            $this->m_dbObject = $repository->find($id);

        $this->location_name = $this->m_dbObject->getLocationName();

    }

    protected function getLocationName()
    {
        return $this->m_dbObject->getLocationName();
    }

    protected function getLocationId()
    {
        return $this->m_dbObject->getLocationId();
    }
}
