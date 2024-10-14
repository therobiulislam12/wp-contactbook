<?php

namespace ContactBook;

/**
 * Admin class
 */

class Admin {

    public function __construct() {
        new Admin\Menu();
        new Admin\Ajax();
    }

}
