<div style="padding: 4em">

	<h3 style="font-family:'mylangqian', serif; color: white; margin-bottom: 2em;">登录查询系统</h3>

	<?php echo validation_errors('<div class="alert alert-danger" role="alert"><b>', '</b></div>'); ?>

	<?php if($err): ?>
	<div class="alert alert-danger" role="alert"><b><?php echo $msg; ?></b></div>
	<?php endif; ?>

	<?php echo form_open('baoming/login'); ?>

	<input class="text" type="text" id="jxnumber" placeholder="教学号" missingMessage="必须填写8位教学号" required="true" maxlength="8" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">

	<input class="text" type="text" id="username" placeholder="姓名" required="">

	<input class="text" type="password" id="pwd" name="password" placeholder="密码" required="">

	<input id="tj" type="submit" value="登 录">

	</form>

</div>
