<h3 style="font-family:'mylangqian', serif; color: white; padding: 2em;">已选课程列表</h3>

<div style="padding: .5em">
    <table id="classList">
        <tr>
            <!-- <th>课程 ID</th> -->
            <th>标题</th>
            <th>详情</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>视频</th>
        </tr>
    </table>
</div>

<script src="/static/js/babel.min.js"></script>
<script type="text/javascript">
    const class_table = document.querySelector('#classList');
    const subclass_table = document.querySelector('#subclassList');

    Date.prototype.format = function(fmt) {
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, `${this.getFullYear()}`.substr(4 - RegExp.$1.length));
        }
        for(var k in o) {
            if(new RegExp("("+ k +")").test(fmt)){
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
            }
        }
        return fmt;
    };

    window.onload = function() {
        fetch('/classSelection/getSelectedClass', { credentials: 'include' })
            .then(resp => resp.json()).then(json => {
                if (json.status === 0) {
                    if(json.data.length === 0){
                        alert("您没有已选的课程"); //TODO:change me
                    }
                    json.data.forEach(c => class_table.appendChild(createClassTableItem(c)));
                } else {
                    alert(json.msg);
                }
            }).catch(() => alert('网络错误'));
    };

    function createTD(content, tr) {
        const td = document.createElement('td');
        td.innerHTML = content;
        console.log(content);
        tr.appendChild(td);
    }

    function createDateTimeTD(timestamp, tr) {
        const date = new Date(timestamp.replace(/-/g, '/'));
        createTD(date.format("MM月dd日 hh:mm"), tr);
    }

    function toHtml(node) {
        var obj = document.createElement("div");
        obj.appendChild(node);
        return obj.innerHTML;
    }

    function createClassTableItem(data) {
        const tr = document.createElement('tr');

        // createTD(data.class_id, tr)
        createTD(data.title, tr);
        createTD(data.detail, tr);
        createDateTimeTD(data.start_time, tr);
        createDateTimeTD(data.end_time, tr);

        if(data.have_stream){
            const a = document.createElement('a');
            const text = document.createTextNode("点击查看");
            a.appendChild(text);
            a.href = "/stream/get?class_id=" + data.class_id;
            createTD(toHtml(a), tr);
		}else{
            createTD("暂无", tr);
		}
        return tr;
    }
</script>
