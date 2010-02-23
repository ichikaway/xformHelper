
 XFormHelper
 extends cakephp form helper.

 On confirmation screen, this helper just show value of post data
  insted of making form tags.

 On form input screen, this helper behaves same as form helper.

 How does this helper know on confirmation screen?
 When the confirmation transition, do following 1 or 2.
  1. in controller
     $this->params['xformHelperConfirmFlag'] = true;
  2. in controller or view file
     XformHelper::confirmScreenFlag = true;

 If you want to mask a password field on confirmation screen,
  use password( insted of input(.

 If you want to change separator of datetime,
  set separator value on the changeDatetimeSeparator property.

