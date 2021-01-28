<?php

/**
 * Class for handling project pages
 *
 * @author Cong Danh Tran <c.d.tran2@ncl.ac.uk>
 * @copyright 2021 Newcastle University
 * @package Framework
 * @subpackage UserPages
 */

namespace Pages;

use \Support\Context as Context;
use \Config\Framework as FW;

/**
 * A class that contains code to implement a project page
 * @psalm-suppress UnusedClass
 */
class Project extends \Framework\Siteaction {

    use \Support\NoCache;

    /**
     * Handle project page
     *
     * @param Context   $context    The context object for the site
     *
     * @return string   A template name
     */
    public function handle(Context $context) {
        $bean = $context->load(FW::PROJECT, $context->rest()[0]);
        $context->local()->addval('bean', $bean);
        return '@content/project.twig';
    }

}

?>