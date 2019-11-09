<h3 style="font-family:'mylangqian', serif; color: white; padding: 2em;">请点击要选择的课程</h3>

<div style="padding: .5em">
    <table id="classList">
        <tr>
            <!-- <th>课程 ID</th> -->
            <th>标题</th>
            <th>详情</th>
            <th>日期</th>
            <th>开始选课</th>
            <th>截止选课</th>
        </tr>
    </table>

    <table id="subclassList" style="display: none">
        <tr>
            <!-- <th>子课程 ID</th> -->
            <!-- <th>课程 ID</th> -->
            <th>标题</th>
            <th>容量(已选)</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>选课</th>
        </tr>
    </table>
</div>

<script src="/static/js/babel.min.js"></script>
<script type="text/babel">
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
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        }
        for(var k in o) {
            if(new RegExp("("+ k +")").test(fmt)){
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
            }
        }
        return fmt;
    };

    window.onload = function() {
        fetch('/classSelection/getClassList', {credentials: 'include'})
            .then(resp => resp.json()).then(json => {
                if (json.status === 0) {
                    json.data.forEach((c) => {
                        class_table.appendChild(createClassTableItem(c))
                    })
                } else {
                    alert(json.msg)
                }
            }).catch(() => alert('网络错误'))
    };

    function createTD(content, tr) {
        const td = document.createElement('td');
        td.innerText = content;
        tr.appendChild(td)
    }

    function createTimestampTD(timestamp, tr) {
        const date = new Date(timestamp);
        createTD(date.format("MM月dd日 hh:mm"), tr)
    }

    function createClassTableItem(data) {
        const tr = document.createElement('tr');

        // createTD(data.class_id, tr)
        createTD(data.title, tr);
        createTD(data.detail, tr);
        createTimestampTD(data.date, tr);
        createTimestampTD(data.start_select, tr);
        createTimestampTD(data.end_select, tr);

        tr.onclick = () => {
            class_table.style.display = 'none';
            subclass_table.style.display = null;
            let formData = new FormData();
            formData.append("class_id", data.class_id);
            fetch('/classSelection/getSubClassList', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            }).then(resp => resp.json()).then(json => {
                if (json.status === 0) {
                    json.data.forEach((c) => {
                        subclass_table.appendChild(createSubClassTableItem(c))
                    })
                } else {
                    alert(json.msg)
                }
            }).catch(() => alert('网络错误'))
        };

        return tr
    }

    function createSubClassTableItem(data) {
        const tr = document.createElement('tr');

        // createTD(data.subclass_id, tr)
        // createTD(data.class_id, tr)
        createTD(data.title, tr);
        createTD(`${data.capacity}(${data.selected})`, tr);
        createTimestampTD(data.start_time, tr);
        createTimestampTD(data.end_time, tr);

        const select = document.createElement('button');
        select.innerText = data.select ? '已选' : '选课';
		if (data.select) {
            select.setAttribute('disabled', 'disabled')
		}
        select.onclick = () => {
            let formData = new FormData();
            formData.append("subclass_id", data.subclass_id);
            fetch('/classSelection/selectClass', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            }).then((data) => data.json()).then((json) => {
                if (json.status === 0) {
                    select.setAttribute('disabled', 'disabled');
                    select.innerText = '已选';
                    alert('选课成功！')
                } else {
                    alert(json.msg)
                }
            }).catch(() => {
                alert('网络错误')
            })
        };
        const td = document.createElement('td');
        td.appendChild(select);
        tr.appendChild(td);

        return tr
    }
</script>
