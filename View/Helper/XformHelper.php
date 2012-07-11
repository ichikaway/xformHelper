<?php
/**
*
* XFormHelper
*
* PHP 5
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @copyright Copyright 2010, Yasushi Ichikawa http://github.com/ichikaway/
* @package xform
* @subpackage xform.helper
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
*/

/**
 * XFormHelper
 *
 * On confirmation screen, this helper just show value of post data
 *  insted of making form tags.
 * On form input screen, this helper behaves same as form helper.
 *
 * How does this helper know on confirmation screen?
 * When the confirmation transition, do following 1 or 2.
 *  1. in controller
 *     $this->params['xformHelperConfirmFlag'] = true;
 *  2. in controller or view file
 *     XformHelper::confirmScreenFlag = true;
 *
 * If you want to mask a password field on confirmation screen,
 *  use password() insted of input().
 *
 * If you want to change separator of datetime,
 *  set separator value on the changeDatetimeSeparator property.
 */

class XformHelper extends FormHelper {

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
	public $changeDatetimeSeparator = null;

/**
 * set default options for the input method.
 *
 * @var array
 * @access public
 */
	public $inputDefaultOptions = array();

/**
 * if set true, month name will be number.
 *
 * @var boolean
 * @access public
 */
	public $monthNameSetNumber = false;



	public function __construct(View $View, $config = array()) {
		if(!empty($config)) {
			foreach($config as $key => $val) {
				$this->{$key} = $val;
			}
		}
		parent::__construct($View);
	}


/**
 * override __call for password() and text() method
 *
 */
	public function __call($method, $params) {
		$fieldName = $params[0];
		if($method === 'password' && $this->checkConfirmScreen()) {
			$value = $this->getConfirmInput($fieldName);
			if(!empty($value)) {
				return '*****';
			}else {
				return '';
			}

		} elseif($this->checkConfirmScreen()) {
			return $this->getConfirmInput($fieldName);
		}


		if($method === 'password' && $this->notFillinPasswordValue) {
			$params[1]['value'] =  ''; //password value clear if show input form.
		}

		return parent::__call($method, $params);
	}


	public function input($fieldName, $options = array()) {
		$options = array_merge($this->inputDefaultOptions, $options);

		return parent::input($fieldName, $options);
	}


	public function error($field, $text = null, $options = array()) {
		$defaults = array('wrap' => true);
		$options = array_merge($defaults, $options);
		return parent::error($field, $text, $options);
	}

	public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = array()) {

		if($this->checkConfirmScreen()) {
			$args = func_get_args();
			return $this->getConfirmDatetime($fieldName, $args);
		}

		if(empty($attributes['monthNames']) && $this->monthNameSetNumber){
			$attributes['monthNames'] = false;
		}


		$separator = (!empty($attributes['separator'])) ? $attributes['separator'] : '-';
		$datefmt = array(
				'year' => $separator,
				'month' => $separator,
				'day' => '',
				'afterDateTag' => '',
				);
		$timefmt = array(
				'hour' => ':',
				'min' => '',
				'meridian' => '',
				);

		if(!empty($this->changeDatetimeSeparator)) {
			$datefmt = $this->changeDatetimeSeparator['datefmt'];
			$timefmt = $this->changeDatetimeSeparator['timefmt'];
		}

		$out = $out_date = $out_time = null;
		if(!empty($dateFormat) && $dateFormat !== 'NONE') {
			$tmp_separator = (!empty($attributes['separator'])) ? $attributes['separator'] : null;
			$attributes['separator'] = '__/__';
			$out_date = parent::dateTime($fieldName, $dateFormat, 'NONE', $attributes);
			$attributes['separator'] = $tmp_separator;
		}

		if(!empty($timeFormat) && $timeFormat !== 'NONE') {
			$out_time = parent::dateTime($fieldName, 'NONE', $timeFormat, $attributes);
		}

		if(!empty($out_date)){
			$pattern = '#^(.+?)__/__(.+?)__/__(.+?)$#is';
			$out .= preg_replace($pattern, '$1' . $datefmt['year']. ' $2'.$datefmt['month']. ' $3'. $datefmt['day'], $out_date);
			$out .= $datefmt['afterDateTag'];
		}

		if(!empty($out_time) && $timeFormat == 24) {
			$pattern = '#^<select(.*?)</select>:<select(.*?)$#is' ;
			$replace = '<select$1</select>' . $timefmt['hour'] . ' <select$2' . $timefmt['min'];
			$out .= preg_replace($pattern, $replace, $out_time);
		}

		if(!empty($out_time) && $timeFormat == 12) {
			$pattern = '#^<select(.*?)</select>:<select(.*?)</select> <select(.*?)$#is' ;
			$replace = '<select$1</select>' . $timefmt['hour'] . ' <select$2</select>' . $timefmt['min'] . '<select$3';
			$out .= preg_replace($pattern, $replace, $out_time);
		}

		return $out;
	}

	public function textarea($fieldName, $options = null) {
		if($this->checkConfirmScreen()) {
			return $this->getConfirmInput($fieldName);
		}

		$args = func_get_args();
		return $this->__xformCallParent( array($this, 'parent::textarea'), $args);
	}

	public function radio($fieldName, $options = null, $attributes = array()) {
		if($this->checkConfirmScreen()) {
			return $this->getConfirmInput($fieldName, $options);
		}
		$args = func_get_args();
		return $this->__xformCallParent( array($this, 'parent::radio'), $args);

	}


	public function select($fieldName, $options = null, $attributes = array()) {
		if($this->checkConfirmScreen()) {
			return $this->getConfirmInput($fieldName, $options);
		}
		$args = func_get_args();
		return $this->__xformCallParent( array($this, 'parent::select'), $args);

	}

	public function checkbox($fieldName, $options = null) {
		if($this->checkConfirmScreen()) {
			return $this->getConfirmInput($fieldName);
		}
		$args = func_get_args();
		return $this->__xformCallParent( array($this, 'parent::checkbox'), $args);
	}

	public function checkConfirmScreen() {
		if(!empty($this->request->params['xformHelperConfirmFlag']) && $this->request->params['xformHelperConfirmFlag'] === true) {
			return true;
		}

		if($this->confirmScreenFlag === true) {
			return true;
		}
		return false;
	}


	protected function _confirmValueOutput($data) {
		if($this->doHtmlEscape) {
			$data = h($data);
		}

		if($this->doNl2br) {
			$data = nl2br($data);
		}

		if($this->escapeBrPermanent === false) {
			$this->doHtmlEscape = true;
			$this->doNl2br = true;
		}

		return $data;
	}


	protected function _getFieldData($fieldName, $options = null) {

		$modelname = key($this->request->params['models']);

		// for Model.field pattern
		$model_field = explode('.', $fieldName);

		if(!empty($model_field[1]) && !empty($this->request->data[$model_field[0]])) {
			$fieldName = $model_field[1];

		}else if(!empty($model_field[0])) {
			$fieldName = $model_field[0];
		}


		if(!empty($model_field[1]) && !empty($this->request->data[$model_field[0]])) {
			$data = $this->request->data[$model_field[0]];

		}else{
			if(empty($modelname)) {
				$data = current($this->request->data);
			}else {
				$data = $this->request->data[$modelname];
			}
		}

		if(isset($data[$fieldName])) {
			return $data[$fieldName];
		}

		return false;
	}



	public function getConfirmInput($fieldName, $options = null) {
		$data = $this->_getFieldData($fieldName, $options);
		if(isset($data)) {

			if(is_array($data)) {
				if(is_array($options)) {
					foreach($data as $key => $val) {
						$data[$key] = (!empty($options[$val])) ? $options[$val] : $val;
					}
				}
				$out = join($this->confirmJoinSeparator, $data);
			}else {
				$out = (is_array($options) && !empty($options[$data])) ? $options[$data] : $data;
			}
			return $this->_confirmValueOutput($out);
		}

		return '';
	}


	public function getConfirmDatetime($fieldName, $options = array()) {
		if($data = $this->_getFieldData($fieldName)) {
			if(is_array($data)) {
				$nothing = true;
				foreach($data as $key => $val) {
					if(!empty($val)){
						$nothing = false;
					}
				}

				if($nothing) {
					return '';
				}

				$separator = (!empty($options[4]['separator'])) ? $options[4]['separator'] : '-';
				$datefmt = array(
						'year' => $separator,
						'month' => $separator,
						'day' => '',
						'afterDateTag' => '',
						);
				$timefmt = array(
						'hour' => ':',
						'min' => '',
						'meridian' => '',
						);


				$out = null;

				if(!empty( $this->changeDatetimeSeparator )){
					$datefmt = $this->changeDatetimeSeparator['datefmt'];
					$timefmt = $this->changeDatetimeSeparator['timefmt'];
				}


				foreach($datefmt as $key => $val) {
					$out .= (isset($data[$key]) ? $data[$key] . $val : '');
				}
				if(!empty($options[2]) && $options[2] !== 'NONE') {
					$out .= ' ';
					foreach($timefmt as $key => $val) {
						$sprintf_fmt = (isset($data[$key]) && is_numeric($data[$key])) ? '%02d' :'%s';
						$out .= (isset($data[$key]) ? sprintf($sprintf_fmt ,$data[$key]) .$val : '');
					}
				}


			}else {
				$out = $data;
			}

			return $this->_confirmValueOutput($out);

		}
		return '';
	}


/**
 * call call_user_func_array with different arguments.
 * php5.3 has different arguments from under php5.2.
 */
	private function __xformCallParent( $call, $args) {

		if(PHP_VERSION >= 5.3 && is_array($call)) {
			$call = $call[1];
		}
		return call_user_func_array( $call, $args);

	}

}
