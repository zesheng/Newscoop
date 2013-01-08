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
        'id' => 'id'
    );

    public function __construct($id = null)
    {
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\WeatherStat');
        if(is_null($id))
            $this->m_dbObject = $repository->getPrototype();
        else
            $this->m_dbObject = $repository->find($id);

        $this->m_properties = self::$m_baseProperties;

        $this->m_customProperties['location_name'] = 'getLocationName';
    }

    protected function getLocationName()
    {
        return $this->m_dbObject->getLocationName();
    }

}
