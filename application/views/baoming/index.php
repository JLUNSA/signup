<input class="text" type="text" id="jxnumber" placeholder="教学号" missingMessage="必须填写8位教学号" required="true" maxlength="8" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">

<input class="text" type="text" id="username" placeholder="姓名" required="">
<input class="text" type="text" id="department" placeholder="所在院系" required="">
<input class="text email" type="email" id="email" placeholder="Email" required="">
<input class="text" type="password" id="pwd1" name="password" placeholder="密码" required="">
<input class="text w3lpass" type="password" id="pwd2" name="repassword" placeholder="确认密码" required="">

<input type="file" id="imgupload" accept="image/jpeg,image/x-png,image/gif" style="display:none"/>

<label for="uploadfile" id="OpenImgUpload" >拍照上传校园学生卡的个人信息面</label>


<div class="input-icon input-icon-right">
	<input type="text" id="vcode" name="vcode" placeholder="验证码" />
	<i id="dorc"></i>
</div>





<p>
	<img class="check_num" src="img/code_num.php" id="getcode_num"  title="看不清，点击换一张" />
</p>
<div class="wthree-text">
	<label class="anim">
		<input type="checkbox" id="A_Confirm" class="checkbox" required="">
		<span>我会准时到场参加培训</span>
	</label>
	<div class="clear"> </div>
</div>
<input id="tj" type="submit" value="提 交">
