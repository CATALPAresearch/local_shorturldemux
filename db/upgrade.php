<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

function xmldb_local_cassign_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    return true;
}