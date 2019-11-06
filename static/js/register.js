window.onload = function () {

	let flag_vcode = false;
	let flag_pwd_same = false;
	let flag_upload_file = false;
	let flag_have_existed = true;
	let myuploadfile;
	let reg = /^\w+((-\w+)|(\.\w+))*@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;

	function showMessage(msg, domObj) {
		//layer.tips(msg, domObj,{tips:[3,"#A8A117"]});//弹出框加回调函数
		layer.tips("<span style='color:white'>" + msg + "</span>", domObj, {
			tips: [2, 'green']
		}); //弹出框加回调函数
	}

	function focusOn(domObj) {
		//layer.tips(msg, domObj,{tips:[3,"#A8A117"]});//弹出框加回调函数
		$("html,body").animate({
				scrollTop: domObj.offset().top - 10
			},
			500
		);
	}

	//一堆选择器
	let psw1 = $("#pwd1");
	let psw2 = $("#pwd2");
	let refreshCaptcha = $("#refreshCaptcha");
	let captcha = $("#captcha");
	let dorc = $("#dorc"); //我也不知道这是啥
	let studentId = $("#jxnumber");
	let openImgUpload = $('#openImgUpload');
	let imgUpload = $('#imgUpload');
	let name = $("#name");
	let department = $("#department");
	let email = $("#email");

	//验证两次输入密码一致
	psw2.blur(function () {
		if (psw1.val() !== psw2.val()) {
			showMessage("两次输入密码不一致!", psw1);
			psw1.val("");
			psw2.val("");
			psw1.focus();
			flag_pwd_same = false;
		} else {
			flag_pwd_same = true;
		}

	});

	//刷新验证码
	function new_captcha() {
		$("#refreshCaptcha").attr("src", "/captcha/get?r=" + Math.random());
	}
	refreshCaptcha.click(new_captcha);

	//验证码验证
	captcha.blur(function () {
		let search = captcha.val();
		if (null != search && "" !== search) {
			$.ajax({
				url: "/captcha/check",
				type: "get",
				data: {
					code: search
				},
				dataType: "json",
				success: function (data) {
					if (data.status === 0) {
						dorc.css("color", "#10B910");
						dorc.text("✔");
						flag_vcode = true;
					} else {
						dorc.css("color", "red");
						dorc.text("✘");
						flag_vcode = false;
					}
				}
			})
		}
	});

	studentId.blur(function () {
		let search_jxnum = studentId.val();
		$.ajax({
			url: "/user/isExist",
			type: "get",
			data: {
				id: search_jxnum
			},
			dataType: "json",
			success: function (data) {
				if (data.status === 0) {
					flag_have_existed = false;
				} else {
					showMessage("该用户已注册", studentId);
					flag_have_existed = true;
				}

			}
		})
	});

	//修改验证码对号和叉号
	captcha.keydown(function () {
		/* Act on the event */
		dorc.text("");
	});

	openImgUpload.click(function () {
		imgUpload.trigger('click');
	});

	imgUpload.change(function (e) {
		//$('#openImgUpload').val(e.target.files[0].type);
		const allowedExtension = ["image/jpeg", "image/png"];
		let f = e.target.files[0];
		if (allowedExtension.indexOf(f.type) === -1) {
			focusOn(openImgUpload);
			showMessage("只能上传jpg/png格式的图片!", openImgUpload);
			flag_upload_file = false;
			return false;
		}

		if (f.size > 3 * 1024 * 1024) {
			//$('#imgupload').value = '';                   //保证重复选择某个文件时触发 onchange 事件
			focusOn(openImgUpload);
			showMessage("上传图片必须小于3M!", openImgUpload);
			flag_upload_file = false;
			return false;
		}

		myuploadfile = e.target.files[0];

		openImgUpload.html(myuploadfile.name);
		flag_upload_file = true;

	});

	$("#tj").click(function () {
		/* Act on the event */

		if (studentId.val().length < 8) {
			focusOn(studentId);
			showMessage("教学号不足8位!", studentId);
			return false;
		}

		if (name.val().length < 2) {
			focusOn(name);
			showMessage("请输入完整姓名!", name);
			return false;
		}

		if (department.val().length < 2) {
			focusOn(department);
			showMessage("请输入院系名称!", department);
			return false;
		}

		if (!reg.test(email.val())) {
			gotoit(email);
			showMessage("请输入正确邮箱格式!", email);
			return false;
		}

		if (!flag_pwd_same) {
			focusOn(psw1);
			showMessage("请输入密码!", psw1);
			return false;
		}

		if (!flag_vcode) {
			focusOn(captcha);
			showMessage("请输入正确的验证码!", captcha);
			return false;
		}

		/*if ($("#A_Confirm").is(':checked') != true) {

			gotoit($("#A_Confirm"))
			showMessage("请确认会准时参赛!", $("#A_Confirm"));
			return false;
		}*/

		if (!flag_upload_file) {
			focusOn(openImgUpload);
			showMessage("请上传正确格式的图片!", openImgUpload);
			return false;
		}

		//查重是否重复报名
		if (flag_have_existed) {
			layer.open({
				title: '警告',
				closeBtn: 0,
				icon: 7,
				content: '你已经报名，请不要重复报名！',
				btnAlign: 'c',
				yes: function (index, _) {
					window.location.reload();
					layer.close(index); //如果设定了yes回调，需进行手工关闭
				}
			});
			return false;
		}

		//all ok
		let formData = new FormData();

		formData.append('student_id', studentId.val());
		formData.append('name', name.val());
		formData.append('department', department.val());
		formData.append('email', email.val());
		formData.append('password', psw1.val());
		formData.append('captcha', captcha.val());
		formData.append('file', myuploadfile);

		$.ajax({
			type: "POST",
			url: "/user/register",
			//同目录下的php文件
			data: formData,
			dataType: "json",
			//如果传递的是FormData数据类型，那么下来的三个参数是必须的，否则会报错
			cache: false,
			//默认是true，但是一般不做缓存
			processData: false,
			//用于对data参数进行序列化处理，这里必须false；如果是true，就会将FormData转换为String类型
			contentType: false,
			//一些文件上传http协议的关系，自行百度，如果上传的有文件，那么只能设置为false
			beforeSend: function () {
				// 禁用按钮防止重复提交
				$("#tj").attr({
					disabled: "disabled"
				});
				layer.msg('上传中', {
					icon: 16,
					shade: 0.01,
					time: 30000
				});
			},
			success: function (data) { //请求成功后的回调函数
				if(data.status === 0){
					layer.open({
						title: '通知',
						closeBtn: 0,
						icon: 1,
						content: '报名成功！',
						btnAlign: 'c',
						yes: function (index, _) {
							window.location.href = "/user/index";
						}
					});
				}else {
					//验证码错误
					if(data.status === 2){
						new_captcha();
					}
					layer.open({
						title: '通知',
						closeBtn: 0,
						icon: 1,
						content: data.msg,
						btnAlign: 'c',
						yes: function (index, _) {
							layer.close(index);
						}
					});
				}
			},
			complete: function () {
				//$("loading").hide();
				layer.closeAll('loading');

			},
			error: function (err_msg) {
				alert("网络错误");
			}
		});

	});
};
