
 XFormHelper(Plugin)
 extends cakephp Form helper.

 On confirmation screen, this helper just show value of post data
  insted of making form tags.

 On form input screen, this helper behaves same as form helper.

 How does this helper know on confirmation screen?
 When the confirmation transition, do following 1 or 2.
  1. in controller
     $this->params['xformHelperConfirmFlag'] = true;
  2. in controller or view file
     $this->XformHelper->confirmScreenFlag = true;

 If you want to mask a password field on confirmation screen,
  use password method insted of input method.

 If you want to change separator of datetime,
  set separator value on the changeDatetimeSeparator property.


Installation
------------
 * Copy the files in this directory into app/Plugin/Xform
 * load plugin in bootstrap
 	CakePlugin::load('Xform');
 * Include the helper in your controller.php:
	var $helpers = array('Form', 'Xform.Xform');
 * call method of XformHelper in your view.
 	echo $this->Xform->input('title');

