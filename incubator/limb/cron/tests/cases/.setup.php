<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
lmb_require('limb/core/tests/cases/init.inc.php');
lmb_require('limb/dbal/tests/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');
lmb_tests_init_db_dsn();
lmb_tests_setup_db(dirname(__FILE__).'/../../init/db.');