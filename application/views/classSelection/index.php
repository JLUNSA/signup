<div style="padding: 2em">
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
            <th>容量</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>选课</th>
        </tr>
    </table>
</div>
<script>
    const class_table = document.querySelector('#classList')
    const subclass_table = document.querySelector('#subclassList')

    window.onload = function() {
        fetch('/classSelection/getClassList')
            .then(resp => resp.json()).then(json => {
                if (json.status === 0) {
                    json.data.forEach((c) => {
                        class_table.appendChild(createClassTableItem(c))
                    })
                } else {
                    alert(json.msg)
                }
            }).catch(() => alert('网络错误'))
    }

    function createTD(content, tr) {
        const td = document.createElement('td')
        td.innerText = content
        tr.appendChild(td)
    }

    function createTimestampTD(timestamp, tr) {
        const date = new Date(timestamp)
        createTD(`${date.toLocaleDateString()} ${date.toLocaleTimeString()}`, tr)
    }

    function createClassTableItem(data) {
        const tr = document.createElement('tr')

        // createTD(data.class_id, tr)
        createTD(data.title, tr)
        createTD(data.detail, tr)
        createTimestampTD(data.date, tr)
        createTimestampTD(data.start_select, tr)
        createTimestampTD(data.end_select, tr)

        tr.onclick = () => {
            table.style.display = 'none'
            subclass_table.style.display = 'block'
            fetch('/classSelection/getSubClassList', {
                method: 'POST',
                body: JSON.stringify({
                    subclass_id: data.subclass_id
                })
            }).then(resp => resp.json()).then(json => {
                if (json.status === 0) {
                    json.data.forEach((c) => {
                        subclass_table.appendChild(createSubClassTableItem(c))
                    })
                } else {
                    alert(json.msg)
                }
            }).catch(() => alert('网络错误'))
        }

        return tr
    }

    function createSubClassTableItem(data) {
        const tr = document.createElement('tr')

        // createTD(data.subclass_id, tr)
        // createTD(data.class_id, tr)
        createTD(data.title, tr)
        createTD(data.capacity, tr)
        createTimestampTD(data.start_time, tr)
        createTimestampTD(data.end_time, tr)

        const select = document.createElement('button')
        select.innerText = data.selected ? '已选' : '选课'
        if (data.select) {
            select.setAttribute('disabled')
        }
        select.onclick = () => {
            fetch('/classSelection/selectClass', {
                method: 'POST',
                body: JSON.stringify({
                    subclass_id: data.subclass_id
                })
            }).then((data) => data.json()).then((json) => {
                if (json.status === 0) {
                    select.setAttribute('disabled')
                } else {
                    alert(json.msg)
                }
            }).catch(() => {
                alert('网络错误')
            })
        }
        const td = document.createElement('td')
        td.appendChild(content)
        tr.appendChild(td)

        return tr
    }
</script>