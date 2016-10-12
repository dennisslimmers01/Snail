<?php

class Router {
    /*
     * This class handles routing
     */
    public function __construct() {
        require './http/Routes.php';

        /*
         * @var Object
         */
        $routes = new Routes();

        /*
         * @var Array
         */
        $url = $this->get_url();

        /*
         * @var String
         */
        $current_page = $url[2];

        /*
         * @var String
         */
        $page_not_found_controller = "PageNotFoundController";

        if (!empty($current_page)) {
            if (!empty($routes->getRoutes()[$current_page])) {
                $this->http($routes->getRoutes()[$current_page], $url);
            } else {
                require 'controllers/' . $page_not_found_controller . ".php";
                /*
                 * @var Object
                 */
                $controller = new $page_not_found_controller();

                /*
                 * Renders 404 page
                 */
                $controller->show();
            }
        } else {
            if (!isset($standard_controller)) {
                /*
                 * Replace with standard controller
                 */
                $standard_controller = ucfirst(Settings::$config["STANDARD_CONTROLLER"]) . "Controller";

                require 'controllers/' . $standard_controller . '.php';

                /*
                 * @var Object
                 */
                $controller = new $standard_controller;

                /*
                 * Render view
                 */
                $controller->show();
            }
        }
    }

    /**
     * @param null $controller
     *
     * This method routes the user based on http/Routes.php
     */
    public function http($controller = null, $url) {
        $current_page = $url[2];

        if (!empty($current_page)) {
            /*
             * If the $controller parameter
             * is a callable function run it
             */
            if (is_callable($controller)) {
                $controller();
            } else {
                if (strstr($controller, ".")) {
                    /*
                     * @var Array
                     */
                    $split = explode(".", $controller);

                    /*
                     * Checks if given controller exists
                     */
                    if (!file_exists('controllers/' . $split[0] . '.php')) {
                        Debug::exitdump('controllers/' . $split[0] . '.php does not exist!');
                    } else {
                        require 'controllers/' . $split[0] . '.php';

                        /*
                         * @var Object
                         */
                        $controller = new $split[0];

                        /*
                         * Checks if method exists
                         */
                        if (method_exists($controller, $split[1])) {
                            /*
                             * Executes given method
                             */
                            $controller->$split[1]();
                        }
                    }
                } else {
                    require 'controllers/' . $controller . '.php';

                    /*
                     * @var Object
                     */
                    $controller = new $controller;
                }
            }
        }
    }


    /**
     * @return array
     */
    public function get_url() {
        return explode("/", $_SERVER['REQUEST_URI']);
    }
}
