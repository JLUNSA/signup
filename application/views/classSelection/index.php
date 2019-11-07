<div style="padding: 2em">
	<table>
		<tr>
			<th>Month</th>
			<th>Savings</th>
		</tr>
		<tr>
			<td>January</td>
			<td>$100</td>
		</tr>
	</table>
</div>
<script>
    window.onload = function(){
        $.ajax({
            type: "POST",
            url: "/classSelection/getClassList",
            data: {},
            dataType: "json",
            success: function (data) {
                if(data.status === 0){
                    window.location.href = "";
                }else{
                    alert(data.msg);
                }
            },
            complete: function () {

            },
            error: function (data) {
                alert("网络错误");
            }
		})
	}
</script>

