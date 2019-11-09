<div style="padding: 2em">
	<h3 style="font-family:'mylangqian', serif; color: white; margin-bottom: 2em;">登录</h3>
	<div class="alert alert-danger" role="alert" style="display: none" id="err"><b></b></div>

	<input class="text" type="text" id="student_id" placeholder="学号" missingMessage="必须填写8位学号" required="true" maxlength="8" onkeyup="numberOnly(this)" onafterpaste="numberOnly(this)">
	<input class="text" type="text" id="name" placeholder="姓名" required="">
	<input class="text" type="password" id="password" name="password" placeholder="密码" required="">

	<div class="input-icon input-icon-right">
		<input type="text" id="captcha" name="captcha" placeholder="验证码" />
		<i id="dorc"></i>
	</div>

	<p>
		<img class="check_num" src="/captcha/get" id="refreshCaptcha"  title="看不清，点击换一张" />
	</p>
	<input id="login" type="submit" value="提交">
</div>
<script>
    function numberOnly(that){
        that.value = that.value.replace(/\D/g,'');
    }
	window.onload = function(){
	    let button = $("#login");
	    let student_id = $("#student_id");
	    let name = $("#name");
	    let password = $("#password");
	    let captcha = $("#captcha");
	    let err = $("#err");
	    let refreshCaptcha = $("#refreshCaptcha");

        function showMessage(msg, domObj) {
            layer.tips("<span style='color:white'>" + msg + "</span>", domObj, {
                tips: [2, 'green']
            });
        }

        function focusOn(domObj) {
            $("html,body").animate({
                    scrollTop: domObj.offset().top - 10
                },
                500
            );
        }

        //刷新验证码
        function new_captcha() {
            refreshCaptcha.attr("src", "/captcha/get?r=" + Math.random());
        }
        refreshCaptcha.click(new_captcha);

        button.click(function (){
            //检查数据
            if (student_id.val().length < 8) {
                focusOn(student_id);
                showMessage("教学号不足8位!", student_id);
                return false;
            }

            if (name.val().length < 2) {
                focusOn(name);
                showMessage("请输入完整姓名!", name);
                return false;
            }

            if (password.val().length === 0) {
                focusOn(password);
                showMessage("请输入密码!", password);
                return false;
            }

            button.prop('disabled', true);
            err.children().html("");
            err.css({
                "display": "none"
            });

            $.ajax({
                type: "POST",
                url: "/user/login",
                data: {
                    "student_id": student_id.val(),
                    "name": name.val(),
                    "password": password.val(),
                    "captcha": captcha.val(),
				},
                dataType: "json",
                success: function (data) {
                    button.prop('disabled', false);
                    if(data.status === 0){
                        window.location.href = "/user/index";
					}else{
                        //验证码错误
                        if(data.status === 2){
                            new_captcha();
                        }

                        err.children().html(data.msg);
                        err.css({
                            "display": "block"
						})
					}
				},
                error: function (data) {
                    button.prop('disabled', false);
                    alert("网络错误");
                }
        	})
		});
	};
</script>
