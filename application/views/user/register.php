<div style="padding: 2em">
	<h3 style="font-family:'mylangqian', serif; color: white; margin-bottom: 2em;">注册</h3>
	<?php echo validation_errors('<div class="alert alert-danger" role="alert"><b>', '</b></div>'); ?>

	<?php if(isset($err) && $err): ?>
		<div class="alert alert-danger" role="alert"><b><?php echo($msg); ?></b></div>
	<?php endif; ?>

	<input class="text" type="text" id="jxnumber" placeholder="学号" missingMessage="必须填写8位学号" required="true" maxlength="8" onkeyup="numberOnly(this)" onafterpaste="numberOnly(this)">

	<input class="text" type="text" id="name" placeholder="姓名" required="">
	<input class="text" type="text" id="department" placeholder="所在院系" required="">
	<input class="text email" type="email" id="email" placeholder="Email" required="">
	<input class="text" type="password" id="pwd1" name="password" placeholder="密码" required="">
	<input class="text w3lpass" type="password" id="pwd2" name="repassword" placeholder="确认密码" required="">

	<input type="file" id="imgUpload" accept="image/jpeg,image/png" style="display:none"/>
	<label for="uploadfile" id="openImgUpload" >上传一卡通的正面照片</label>
	<div class="input-icon input-icon-right">
		<input type="text" id="captcha" name="captcha" placeholder="验证码" />
		<i id="dorc"></i>
	</div>


	<p>
		<img class="check_num" src="/captcha/get" id="refreshCaptcha"  title="看不清，点击换一张" />
	</p>
	<!--<div class="wthree-text">
		<label class="anim">
			<input type="checkbox" id="A_Confirm" class="checkbox" required="">
			<span>我会准时到场参加比赛</span>
		</label>
		<div class="clear"> </div>
	</div>-->
	<input id="tj" type="submit" value="提交">
</div>
<script src="/static/js/register.js"></script>
<script>
    function numberOnly(that){
        that.value = that.value.replace(/\D/g,'');
    }
</script>
