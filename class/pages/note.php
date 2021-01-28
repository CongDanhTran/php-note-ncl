<?php

/**
 * Class for handling edit note pages
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
 * A class that contains code to implement a edit note page
 * @psalm-suppress UnusedClass
 */
class Note extends \Framework\Siteaction {

    use \Support\NoCache;

    /**
     * Handle edit notes operations / edit page
     *
     * @param Context   $context    The context object for the site
     *
     * @return string   A template name
     */
    public function handle(Context $context) {
        $rest = $context->rest();
        $obj = $context->load(FW::NOTE, $rest[1]);
        $context->local()->addval('bean', $obj);
        
        if ($context->web()->isPost()) {
            try {
                $obj->edit($context);
                header('Location: /project/' . $obj->project_id);
                exit();
            } catch (\Exception $e) {
                $context->local()->message(\Framework\Local::ERROR, $e->getMessage());
                return '@content/edit/note.twig';
            }
        }

        return '@content/edit/note.twig';
    }

}

?>