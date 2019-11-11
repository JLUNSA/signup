<h3 style="font-family:'mylangqian', serif; color: white; margin-bottom: 2em;">用户中心</h3>

<h3 style="font-family:'mylangqian', serif; color: white; margin-bottom: 1em;">欢迎，<?=$name?>！</h3>
<?php if(isset($verified) && $verified):?>
	<input type="submit" onclick="window.location.href='/classSelection/index'" value="选 课">
	<input type="submit" onclick="window.location.href='/classSelection/selectedClass'" value="已选课程查看">
<?php else: ?>
	<input type="submit" disabled="disabled" onclick="window.location.href='/classSelection/index'" value="请等待管理员审核您的账号">
<?php endif; ?>
