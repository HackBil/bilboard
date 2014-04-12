<?php

class Zend_View_Helper_LastVisited {
    /**
     * Example:
     * Zend_View_Helper_LastVisited::saveThis($this->_request->getRequestUri());
     */
    function saveThis($url) {
//        $lastPg = new Zend_Session_Namespace('history');
//        $lastPg->last = $url;
        Zend_Debug::dump('test');
        //echo $lastPg->last;// results in /controller/action/param/foo
    }

    /**
     * I typically use redirect:
     * $this->_redirect(Zend_View_Helper_LastVisited::getLastVisited());
     */
    function getLastVisited() {
        $lastPg = new Zend_Session_Namespace('history');
        if(!empty($lastPg->last)) {
            $path = $lastPg->last;
            $lastPg->unsetAll();
            return $path;
        }

        return ''; // Go back to index/index by default;
     }
}