<?php
/**
*
* XFormjpHelper
*
* PHP 5
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @copyright Copyright 2010, Yasushi Ichikawa http://github.com/ichikaway/
* @package xformjp
* @subpackage xformjp.xform.helper
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
*/

/**
 * XFormjpHelper for Japanese users.
 */

App::uses('XformHelper', 'Xform.View/Helper');
class XformjpHelper extends XformHelper {

/**
 * confirmation screen flag
 *
 * @var boolean
 * @access public
 */
    public $confirmScreenFlag = false;

/**
 * not fillin password value
 * if set false, password value is set on form input tag.
 *
 * @var boolean
 * @access public
 */
    public $notFillinPasswordValue = true;


/**
 * output value are escaped on confirmation screen.
 *
 * @var boolean
 * @access public
 */
    public $doHtmlEscape = true;


/**
 * execute nl2br() for output value on confirmation screen.
 *
 * @var boolean
 * @access public
 */
    public $doNl2br = true;

/**
 * If set true and change $doHtmlEcpane or $doNl2br properties,
 * these properties are not changed by default value after output.
 *
 * @var boolean
 * @access public
 */
    public $escapeBrPermanent = false;

/**
 * The field which has array data like checkbox(),
 * thease array value join with this separator on confirmation screen.
 *
 * @var string
 * @access public
 */
    public $confirmJoinSeparator = ', ';


/**
 * change datetime separator on form input and confirmation screen.
 *
 * @var array
 * @access public
 *
 * Example:
 *   var $changeDatetimeSeparator = array(
 *       'datefmt' => array(
 *           'year' => ' / ',
 *           'month' => ' / ',
 *           'day' => '',
 *           'afterDateTag' => '&nbsp;&nbsp;&nbsp;', //set value between date and time tags.
 *           ),
 *       'timefmt' => array(
 *           'hour' => ' : ',
 *           'min' => '',
 *           'meridian' => '',
 *           )
 *       );
 */
    public $changeDatetimeSeparator = array(
            'datefmt' => array(
                'year' => '年',
                'month' => '月',
                'day' => '日',
                'afterDateTag' => '&nbsp;&nbsp;&nbsp;', //dateとtimeの表示の間に入れる文字列
                ),
            'timefmt' => array(
                'hour' => '時',
                'min' => '分',
                'meridian' => '',
                )
            );

/**
 * set default options for the input method.
 *
 * @var array
 * @access public
 */
    public $inputDefaultOptions = array('label' => false, 'error' => false, 'div' => false);


/**
 * if set true, month name will be number.
 *
 * @var boolean
 * @access public
 */
    public $monthNameSetNumber = true;


/**
 * the create method which shows only form and input tag.
 * delete display:none, because can not send Token with Japanese mobile devices.
 *
 * @param string model name
 * @param array options
 */
    public function create($model = null, $options = array()) {
        return strip_tags(parent::create($model, $options), '<form><input>');
    }


/**
 * the end method which shows only form and input tag.
 * delete display:none, because can not send Token with Japanese mobile devices.
 *
 * @param array options
 */
    public function end($options = null) {
        return strip_tags(parent::end($options), '<form><input>');
    }



}
