<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IWidgetContext.php';
 
/**
 * Widget interace
 */
interface IWidget
{
    /**
     * Get widget title.
     * @return string|NULL
     */
    public function getTitle();

    /**
     * Render widget.
     * @param IWidgetContext $context
     * @return void
     */
    public function render(IWidgetContext $context);
}
