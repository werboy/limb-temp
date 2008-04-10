<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__) . '/tests_runner/common.inc.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeFilePathNode.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeGlobNode.class.php');

$fork = false;
$quiet = false;
$tests = array();
$failed_tests = array();

function out($msg)
{
  global $quiet;

  if(!$quiet)
    echo $msg;
}

function process_argv(&$argv)
{
  global $quiet;
  global $fork;

  $new_argv = array();
  foreach($argv as $arg)
  {
    if($arg == '-q')
      $quiet = true;
    else if($arg == '--fork')
      $fork = true;
    else
      $new_argv[] = $arg;
  }
  $argv = $new_argv;
}

function get_php_bin()
{
  ob_start();
  phpinfo(INFO_GENERAL);
  $info = ob_get_contents();
  ob_end_clean();

  $php_bin = $_ENV["_"];
  $php_ini = "";

  $lines = explode("\n", $info);
  foreach($lines as $line)
  {
    if(preg_match('~^Loaded Configuration File\s*=>\s*(.*)$~', $line, $m))
      $php_ini = "-c " . $m[1];
  }
  return $php_bin . " " . $php_ini;
}

process_argv($argv);

if(sizeof($argv) > 1)
  $tests = array_splice($argv, 1);

if(!$tests)
  $tests = glob("*/tests/cases");

if($fork)
{
  $php_bin = get_php_bin();
  out("=========== Forking procees for each test(PHP cmdline '$php_bin') ===========\n");
}

$res = true;
foreach($tests as $test)
{
  if(file_exists($test) || is_dir($test))
  {
    out("=========== Running tests from '$test' ===========\n");

    if($fork)
    {
      system($php_bin . " " . __FILE__ . " -q $test", $ret);
      if($ret != 0)
      {
        $res = false;
        $failed_tests[] = $test;
      }
    }
    else
    {
      $runner = new lmbTestRunner();
      if(!$runner->run(new lmbTestTreeFilePathNode($test)))
      {
        $res = false;
        $failed_tests[] = $test;
      }
    }
  }
  else
    out("=========== Test path '$test' is not valid, skipping ==========\n");
}

if(!$res)
{
  out("=========== TESTS HAD ERRORS ===========\n");
  out("Failed tests: [" . implode(", ", $failed_tests) . "]\n");
}
else
  out("=========== ALL TESTS PASSED ===========\n");

exit($res ? 0 : 1);
