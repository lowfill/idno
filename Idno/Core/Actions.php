<?php

    /**
     * Action management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Actions extends \Bonita\Forms
        {

            /**
             * Creates an action link that will submit via POST to the page
             * specified at $pageurl with the data specified in $data
             *
             * @param string $pageurl URL of the page to point to
             * @param string $label The text of the link
             * @param array $data Array of name:value pairs that will be submitted to $pageurl
             * @param array $options Array of options for future use (optional)
             * @return string
             */
            function createLink($pageurl, $label, $data = array(), $options = array())
            {
                $params = array('url' => $pageurl, 'label' => $label, 'data' => $data, 'class' => '', 'confirm' => false, 'confirm-text' => 'Are you sure?');
                $params = array_merge($params, $options);

                return site()->template()->__($params)->draw('forms/link');
            }

            /**
             * Creates a properly-signed POST form
             *
             * @param string $pageurl URL of the page to point to
             * @param string $body The body for the form
             * @return type
             */
            function createForm($pageurl, $body)
            {
                return site()->template()->__(array('body' => $body))->draw('forms/action');
            }

            /**
             * Signs forms so that you don't need to use createForm if you don't want to.
             *
             * @param string $pageurl The URL of the page we're signing for
             */
            function signForm($pageurl)
            {
                return site()->template()->__(array('action' => $pageurl, 'time' => time()))->draw('forms/token');
            }

        }

    }

