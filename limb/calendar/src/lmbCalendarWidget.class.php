<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 *  This class implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 * @package calendar
 * @version $Id$
 */
class lmbCalendarWidget
{
  protected $newline = "\n";
  protected $calendar_lib_path;

  protected $calendar_file;
  protected $calendar_lang_file;
  protected $calendar_setup_file;
  protected $calendar_theme_file;
  protected $calendar_options;

  function __construct($lang              = 'en',
                       $stripped          = true,
                       $theme             = 'calendar-win2k-1',
                       $calendar_lib_path = '/shared/calendar/js/')
  {
    if($stripped)
    {
      $this->calendar_file = 'calendar_stripped.js';
      $this->calendar_setup_file = 'calendar-setup_stripped.js';
    }
    else
    {
      $this->calendar_file = 'calendar.js';
      $this->calendar_setup_file = 'calendar-setup.js';
    }
    $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
    $this->calendar_theme_file = $theme.'.css';
    $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
    $this->calendar_options = array('ifFormat' => '%Y-%m-%d',
                                    'daFormat' => '%Y-%m-%d');
  }

  function setOption($name, $value)
  {
    $this->calendar_options[$name] = $value;
  }

  function loadFiles()
  {
    static $rendered = false;

    $code  = '';

    if(!$rendered)
    {
      $code  = '<link rel="stylesheet" type="text/css" media="all" href="' .
                $this->calendar_lib_path . $this->calendar_theme_file .
                 '" />' . $this->newline;
      $code .=  '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_lang_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
               $this->calendar_lib_path . $this->calendar_setup_file .
               '"></script>';
    }

    $rendered = true;

    return $code;
  }

  function makeButton($field_id, $cal_options = array(), $field_attributes = array())
  {
    $id = $this->_genId();
    $out = '<a href="#" id="'. $this->_triggerId($id) . '">' .
        '<img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';

    $options = array_merge($cal_options,
                           array('inputField' => $field_id,
                                 'button'     => $this->_triggerId($id)));
    return $out . $this->_makeCalendar($options);
  }

  function _makeCalendar($other_options = array())
  {
    $js_options = $this->_makeJsHash(array_merge($this->calendar_options, $other_options));
    $code  = '<script type="text/javascript">Calendar.setup({' .
             $js_options .
             '});</script>';
    return $code;
  }

  function _fieldId($id) { return 'f-calendar-field-' . $id; }
  function _triggerId($id) { return 'f-calendar-trigger-' . $id; }
  function _genId() { static $id = 0; return ++$id; }

  function _makeJsHash($array)
  {
    $jstr = '';
    reset($array);
    while(list($key, $val) = each($array))
    {
      if(is_bool($val))
        $val = $val ? 'true' : 'false';
      else if(!is_numeric($val))
        $val = '"'.$val.'"';
      if($jstr) $jstr .= ',';
      $jstr .= '"' . $key . '":' . $val;
    }
    return $jstr;
  }

  function _makeHtmlAttr($array)
  {
    $attrstr = '';
    reset($array);
    while(list($key, $val) = each($array))
    {
      $attrstr .= $key . '="' . $val . '" ';
    }
    return $attrstr;
  }
}

?>