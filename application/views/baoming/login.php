登录页模板<br/>

<?php
echo form_error('jxh');
echo form_error('name');
echo form_error('password');
if($err){
	print($msg);
}
