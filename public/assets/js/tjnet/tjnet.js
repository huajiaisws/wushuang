var timeid = 0;
var sel_edge = null;
var sel_edge_nodes = null;
var LOAD_TOTAL_STEP = 496;
var LENGTH_MAIN = 250;
var LENGTH_SUB = 50;
var ROW_CNT = 56;
var ROW_PRE_CNT = 12;
var ROW_STEP = 70;
var edges_colors = ['#FF0000','#00FF00','#0000FF','#FF00FF','#00FFFF','#FFA500','#8968CD','#8B7500','#B03060','#6E8B3D','#8B2323','#999999'];
var arc = ['A区','B区','C区','D区','E区','F区','G区','H区','I区','J区'];
var nodes = []; edges = [];
var vis = null;
var network = null;
var bshowreg = true;


var displaymode = document.getElementById("displaymode");



function netdestroy() {
    if (network !== null) {
        network.destroy();
        network = null;
    }
}
function reg_btn_show_hide(btn){
    if (btn.value == "隐藏注册按钮") {
        bshowreg = false;
        btn.value = "显示注册按钮";
    }else{
        bshowreg = true;
        btn.value = "隐藏注册按钮";
    }
}
function hidereg(btn) {
    reg_btn_show_hide(btn);
    loaddata(curid);
}
var options = {
    layout: {
        hierarchical: {
            direction: displaymode.value,
            levelSeparation:200,
            nodeSpacing:200,
            sortMethod: "directed"
        }
    },
    nodes: {
        shape:'circularImage',//circularImage
        margin: 10,
        color: "#FFFFFF",
        borderWidth:2,
        size:20,
        font: {
            multi: 'html',
            size: 25,
            bold: {
                color: '#FFFFFF',
                size: 30, // px
                face: 'arial',
                vadjust: 0,
                mod: 'bold'
            },
            ital:{
                color:'#343434',
                size: 20
            },
            mono: {
                color: '#FFFFFF',
                size: 25, // px
                face: 'courier new',
                vadjust: 2,
                mod: ''
            },
            align: 'left'
        },
        scaling:{
            min: 16,
            max: 32
        },

    },
    edges:{
        smooth:{type:'discrete'},
        font: {size: 25},
        width: 3,
    },
    groups: groups,
    physics: {
        enabled: false
    },
    /*physics: {
        "barnesHut": {
            "springLength": 300,
            "gravitationalConstant": -5000,
        },
    },*/
};
var freeoptions = {
    nodes: {
        shape: 'circularImage',
        scaling: {min: 10,max: 30},
        font: {size: 12, face: 'Tahoma'}
    },
    edges: {
        width: 0.15,
        color: {inherit: 'from'},
        smooth: {type: 'continuous'}
    },
    physics: {
        stabilization: false,
        barnesHut: {
            gravitationalConstant: -40000,
            springConstant: 0.01,
            //springConstant: 0.001,
            springLength: 100
        },
        timestep: 1
    },
    interaction: {
        tooltipDelay: 200,
        hideEdgesOnDrag: true
    },
    groups: groups,
};

function loadnode(item){
    var str = item.code;//+ ' '+ arrlevel[item.rank] + '('+item.icode+')' + ' ' + item.dtype
    var txt = '<b>'+str+'</b>\r\n';
    txt+= ' <b>总计:</b> <code> '+item.totalnum + '<code> <b>直推:</b> <code>' + item.tjnum+ '</code>\r\n';
    var uname = '<br/>姓名:'+item.uname;
    if(item.mobile != ''){
        if(uname != '') uname += '<br/>';
        uname += '手机号:'+item.mobile;
    }
    var title="<div class='popdiv'>";
    title +="<div class='poptitle'>用户名:" + item.code +"&nbsp;"+ uname + "</div>";
    //title +="<div class='poptitle'>" + arrlevel[item.rank] + "&nbsp;"+item.icode+"&nbsp;"+ item.dtype +"</div>";
    title +="<table class='poptable' cellpadding='0' cellspacing='0' border='0'>";
    title +="<tr><td class='t1'>总计</td>";
    title +="<td class='t2'>"+ item.totalnum+"</td><td class='t1'>直推</td><td class='t2'>"+ item.tjnum+"</td>";
    title +="</tr>";
    title +="<tr><td class='tm' colspan='4'>"+item.tm+"</td></tr>";
    title +="</table>";
    title +="</div>";

    str = item.tm;
    pad = ROW_CNT - str.length;
    if(pad < 0) pad = 0;
    pad = parseInt(pad / 2);
    var prestr = Array(pad).join(" ");
    txt+='<code>'+prestr+'</code><b>'+str+'</b>\r\n';
    var group = 'g'+item.rank;
    if(item.status != 1) group = 'notactive';
    nodes.push({id: item.id, label:item.code+'\r\n'+item.mobile, group: group, title: title, image:'/assets/img/avatar.png', data:item, brokenImage:'/asset/img/qrcode.png'});
    if(bshowreg && item.canreg == 1){
        var subid = 'reg_'+item.id+'_0';
        nodes.push({id: subid,  font:{size:25},shape:'circle',group:'rn', label:'注册', title: '['+item.code + ']'+'推荐注册'});
        edges.push({from: item.id, to: subid, length: LENGTH_SUB,'arrows':'to', color:{color:'#FF7F00'}});
    }
    return item.id;
};
var networkCanvas = document.getElementById("mynetwork").getElementsByTagName("canvas")[0]
function changeCursor(newCursorStyle){
    networkCanvas.style.cursor = newCursorStyle;
}
function draw() {
    var container = document.getElementById('mynetwork');
    var x = container.clientWidth / 2 + 50;
    var y = container.clientHeight / 2 + 50;
    var data = {
        nodes: nodes,
        edges: edges
    };
    netdestroy();
    if(displaymode.value == 'FREE'){
        if(nodes.length > 100){
            freeoptions.nodes.shape = 'dot';
        }else{
            freeoptions.nodes.shape = 'circularImage';
        }
        network = new vis.Network(container, data, freeoptions);
    }else{
        options.layout.hierarchical.direction = displaymode.value;
        network = new vis.Network(container, data, options);
    }
    var moveToOptions = {
        //position: {x:-150, y:-350},    // position to animate to (Numbers)
        scale: 0.5,              // scale to animate to  (Number)
        //offset: {x:0, y:-250},      // offset from the center in DOM pixels (Numbers)
    }
    network.focus(curid, moveToOptions);
    network.on("hoverNode", function (params) {
        var spreid = params.nodes[0];
        var arr = spreid.split("_",3);
        if(arr[0] == 'reg' && arr.length >= 3){
            changeCursor('pointer');
        }
    });
    network.on("click", function (params) {
        var itemid = 0;
        if(params.nodes.length > 0){
            itemid = params.nodes[0];
            var arr = itemid.split("_",3);
            if(arr[0] == 'reg' && arr.length >= 3){
                window.open(regurl + "?tjid="+arr[1]);
            }
        }
    });

    network.on("doubleClick", function (params) {
        var itemid = 0;
        if(params.nodes.length > 0){
            itemid = params.nodes[0];
            var arr = itemid.split("_",3);
            if(!isNaN(Number(itemid))){
                curid = itemid;
                currentpage=1;
                $('#btnpre').attr('disabled', "true");
                loaddata(curid);
            }
        }
        else if(params.edges.length > 0){
            itemid = params.edges[0];
            var i=0;
            var cnodes = this.getConnectedNodes(itemid);
            for( ;i<nodes.length;i++){
                if(nodes[i].id == cnodes[1]){
                    break;
                }
            }
            $("#cur_userid").text(nodes[i].id);
            $("#cur_username").text(nodes[i].data.uname);
            $("#cur_nickname").text(nodes[i].data.code);
            $("#cur_usermobile").text(nodes[i].data.mobile);
            $("#destuser").val("");
            $("#destuserid").val("");
            $("#userModal").modal('show');
        }
    });

    network.on("release", function (params) {
        if(sel_edge != null){
            var nodes = this.getConnectedNodes(sel_edge);
            if(nodes.length == 2){
                if(nodes[0] != sel_edge_nodes[0]){
                    update_member_relationship(sel_edge_nodes[1], sel_edge_nodes[0], nodes[0], sel_edge);
                    console.log('release Event:', sel_edge_nodes[1]+",oldparent:"+sel_edge_nodes[0]+",newparent:"+nodes[0]);
                    sel_edge = null;
                } else if(nodes[1] != sel_edge_nodes[1]){
                    var oldparent = 0;
                    for(var i=0;i<edges.length;i++){
                        if(edges[i].to == nodes[1]){
                            oldparent = edges[i].from;
                            break;
                        }
                    }
                    update_member_relationship(nodes[1], oldparent, nodes[0], sel_edge);
                    console.log('release Event:', nodes[1]+",oldparent:"+oldparent+",newparent:"+nodes[0]);
                    sel_edge = null;
                }
            }
        }
    });
}

function proc_data(ret){
    if(ret.code == 1 ){
        nodes.length = 0;
        edges.length = 0;
        curpid = ret.pid;

        var pos_cnt = ret.pos_cnt;
        if(pos_cnt == 0){ //太阳线
            displaymode.value = 'FREE';
            if(ret.size > 100){
                var btn = document.getElementById('btnshowreg');
                btn.value = "隐藏注册按钮";
                reg_btn_show_hide(btn);
            }
        }
        if(curpid == 0){
            $('#btnparent').attr('disabled',"true");
            $('#btngotop').attr('disabled',"true");
        }else{
            $('#btnparent').removeAttr('disabled');
            $('#btngotop').removeAttr('disabled');
        }
        var is = 1;
        for(var key in ret.data){
            var nid = loadnode(ret.data[key]);
            var tjid = ret.data[key].tjid;
            if(nid != tjid && tjid > 0){
                var cidx = tjid % edges_colors.length;
                edges.push({from: ret.data[key].tjid, to:nid, length: LENGTH_SUB,'arrows':'to', color:{color:edges_colors[cidx]}});
            }
            if(ret.mid){
                thismid = ret.mid;
            }else if(is==1){
                thismid = key;
                is=0;
            }
        }
        console.log(ret.data[thismid]);
        $('#pagename').html(ret.data[thismid].code);
        $('#allpage').html(ret.data[thismid].allpage);
        $('#allnums').html(ret.data[thismid].tjnum);
        $('#tpage').val(currentpage);

        if(currentpage==1){
            $('#btnpre').attr('disabled', "true");
            if(currentpage!=ret.data[thismid].allpage){
                $('#btnnext').removeAttr('disabled');
            }
        }

        if(currentpage==ret.data[thismid].allpage){
            if(currentpage!=1){
                $('#btnpre').removeAttr('disabled');
            }
            $('#btnnext').attr('disabled', "true");
        }

        if(currentpage < ret.data[thismid].allpage && currentpage > 1){
            $('#btnpre').removeAttr('disabled');
            $('#btnnext').removeAttr('disabled');
        }


        draw();
    }else{
        var container = document.getElementById('mynetwork');
        container.innerHTML = '<div style="text-align:center;">没有数据</div>';
    }
}

function loaddata(mid){
    startProgress();
    timeid = setInterval("onLoadTick()",500);
    $.ajax({
        url: loadurl,// 跳转到 action
        data:{
            mid : mid,
            page : currentpage,
        },
        type:'get',
        cache:false,
        dataType:'json',
        success:function(ret) {
            clearInterval(timeid);
            timeid = 0;
            proc_data(ret);
            ProgressDone();
            var allpages = $('#allpage').html();
            if(currentpage >= allpages){
                $('#btnnext').attr('disabled','true');
            }
        },
        error : function() {
            clearInterval(timeid);
            timeid = 0;
            tip.msgbox.err("加载异常!");
        }
    });
}


function startProgress(){
    cur_pro_step = 0;
    document.getElementById('text').innerHTML = '';
    document.getElementById('bar').style.width = '0px';
    document.getElementById('loadingBar').style.display = 'block';
    document.getElementById('loadingBar').style.opacity = 1;
    $("#text").addClass("loading");
}

function setProgress(params){
    var maxWidth = 496;
    var minWidth = 20;
    var widthFactor = params.iterations/params.total;
    var width = Math.max(minWidth,maxWidth * widthFactor);
    document.getElementById('bar').style.width = width + 'px';
    if(params.loading == 1){
        document.getElementById('text').innerHTML = "加载中...";
    }else{
        $("#text").removeClass("loading");
        document.getElementById('text').innerHTML = Math.round(widthFactor*100) + '%';
    }
    // console.log('setProgress Event:', params.iterations);
}

function ProgressDone(){
    document.getElementById('text').innerHTML = '100%';
    document.getElementById('bar').style.width = '496px';
    document.getElementById('loadingBar').style.opacity = 0;
    // really clean the dom element
    setTimeout(function () {document.getElementById('loadingBar').style.display = 'none';}, 500);
}
function onLoadTick(){
    cur_pro_step +=20;
    if(cur_pro_step > LOAD_TOTAL_STEP) cur_pro_step = 0;
    setProgress({iterations:cur_pro_step, total:LOAD_TOTAL_STEP, loading:1});
}

function update_member_relationship(mid, oldparentid, newparentid, refedge) {
    //network.clustering.updateEdge(refedge, {label:'确认中...'});
    var lbtext = network.body.edges[refedge].options['label'];
    network.body.edges[refedge].setOptions({label:'更新中...'});
    //network.setOptions({id:refedge,label:'确认中...'});
    $.ajax({
        url: relationshipurl,// 跳转到 action
        data: {
            mid: mid,
            curid: curid,
            oldparentid: oldparentid,
            newparentid: newparentid,
        },
        type: 'get',
        cache: false,
        dataType: 'json',
        success: function (ret) {
            /* network.body.edges[refedge].setOptions( {
               color: '#000000',
               label: 'S区',
               from: sel_edge_nodes[0],
               to: sel_edge_nodes[1]
           });*/
            if(ret.code != 0){/*不能修改，则恢复原来的会员关系*/
                network.clustering.updateEdge(refedge, {
                    label: lbtext,
                    from: sel_edge_nodes[0],
                    to: sel_edge_nodes[1]
                });
                tip.msgbox.err(ret.msg);
            }else{
                /*network.clustering.updateEdge(refedge, {
                    label: ret.posname
                });*/
                proc_data(ret.data);
            }
        }
    });
}


function ondisplay(mode){
    draw();
}
function loadparent(){
    curid = curpid;
    loaddata(curid);
}
function changepage(page){
    var allpages = $('#allpage').html();
    var tpage = $('#tpage').val();

    if(page == -1){
        if(currentpage > 1){
            currentpage--;
            if(currentpage == 1){
                $('#btnpre').attr('disabled', "true");
            }
        }
    }else if(page ==2){
        if(tpage >=1 && tpage <=allpages){
            currentpage = tpage;
        }
    }else {
        currentpage++;
        if(currentpage > 1){
            $('#btnpre').removeAttr('disabled');
        }
        if(currentpage == allpages){
            $('#btnnext').attr('disabled','true');
        }
    }
    $('#loadingBar').attr("style","display:block;");
    loaddata(curid);
}
function gotop(){
    curid = 0;
    loaddata(curid);
}
function doEdit(){
    if(sel_edge != null){
        sel_edge = null;
        network.disableEditMode();
        return;
    }
    var edges = network.getSelectedEdges();
    if(edges.length == 0){
        tip.msgbox.err("请先选择要编辑的会员连接线!");
        return;
    }
    sel_edge = edges[0];
    sel_edge_nodes = network.getConnectedNodes(sel_edge);
    network.editEdgeMode();
}

function doMove()
{
    var oldparentid = 0;
    var mid = $("#cur_userid").text();
    var newparentid = $("#destuserid").val();
    var pos = $("#pos").val();
    if(newparentid == ""){
        tip.msgbox.err("请先选择会员的新推荐人");
        return;
    }
    tip.confirm('确认修改会员推荐人？', function () {
        for(var i=0;i<edges.length;i++){
            if(edges[i].to == mid){
                oldparentid = edges[i].from;
                break;
            }
        }
        $("#btnMove").text("正在处理...");
        $("#btnMove").addClass("disabled");
        $('#btnMove').attr('disabled',"true");
        $.ajax({
            url: relationshipurl,// 跳转到 action
            data: {
                mid: mid,
                curid: curid,
                oldparentid: oldparentid,
                newparentid: newparentid,
                pos:pos
            },
            type: 'get',
            cache: false,
            dataType: 'json',
            success: function (ret) {
                $("#userModal").modal('hide');
                $("#btnMove").text("确认修改");
                $("#btnMove").removeClass("disabled");
                $('#btnMove').removeAttr('disabled');
                if(ret.code != 0){/*不能修改，则恢复原来的会员关系*/
                    tip.msgbox.err(ret.msg);
                }else{
                    proc_data(ret.data);
                }
            }
        });
    });
}