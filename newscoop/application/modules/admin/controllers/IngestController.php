<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Services\IngestService;

/**
 * @Acl(ignore=1)
 */
class Admin_IngestController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\IngestService */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('ingest');
    }

    public function indexAction()
    {
        $this->view->auto_mode = (bool) SystemPref::Get(IngestService::MODE_SETTING);
        $this->view->feeds = $this->service->getFeeds();
        $this->view->entries = $this->service->findBy(array('published' => null), array('updated' => 'desc'), 25, 0);
    }

    public function detailAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->entry = $this->service->find($this->_getParam('entry'));
    }

    public function switchModeAction()
    {
        SystemPref::Set(IngestService::MODE_SETTING, !SystemPref::Get(IngestService::MODE_SETTING));
        $this->_helper->redirector('index');
    }

    public function publishAction()
    {
        try {
            $entry = $this->service->find($this->_getParam('entry'));
            $this->service->publish($entry);
            $this->_helper->flashMessenger(getGS("Entry '$1' published", $entry->getTitle()));
            $this->_helper->redirector('index');
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }
}