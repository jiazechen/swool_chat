
var ws_host = 'ws://127.0.0.1:8855';

var ws = new WebSocket(ws_host);

//绑定连接事件
ws.onopen = function(evt) {  
  console.log("Connection open ..."); 
};

//绑定收到消息事件
ws.onmessage = function(evt) {
  var jsonObj = JSON.parse(evt.data);
  console.log( "Received Message: " + evt.data);
  console.log(jsonObj);

  messageHandle( jsonObj );
  
};

//绑定关闭或断开连接事件
ws.onclose = function(evt) { 
　console.log("Connection closed.");
};

// 发送内容
var sendMsg = function(msg){
  appendSelfContent(msg);
  ws.send(msg);
  clearText();
}

// ###################### 消息处理 #########################

// 消息处理分发器
var messageHandle = function( obj ){
  switch (obj.type) {
    case 'member':
      memberDo( obj );
      break;
    case 'system':
      systemDo( obj );
      break;
    case 'user':
      userDo( obj );
      break;
  }
}

// 成员信息
var memberDo = function( obj ){
  $('#member').html('');
  $.each( obj.memberList,function(i,e){
    var item = "<li>"
                    + e +"\
                </li>";
    $('#member').append( item );
  } )
  
}

// 系统信息
var systemDo = function( obj ){
  var item =  " <p>\
                  <div style='text-align:center;'>"
                    + obj.msg +"\
                  </div>\
                </p>";
  $('#readContent').append( item );
}

// 用户信息
var userDo = function( obj ){
  var item = "<p>\
                <div>"
                    + obj.name +"：\
                </div>\
                <div>\
                  &nbsp;&nbsp;&nbsp;&nbsp;"
                    + obj.msg +"\
                </div>\
              </p>";
  $('#readContent').append( item );
}

// ###################### END 消息处理 #########################


// 将内容添加到聊天框
var appendSelfContent = function(msg){
  var item = "<p style='text-align:right'>"+msg+"</p>";
  $('#readContent').append( item );
}

// 情况输入框
var clearText = function(){
  $('#chatContent').val('');
}

// 回车监听
document.onkeydown = function(e){
  if(e.keyCode == 13){
    var msg = $('#chatContent').val();
    sendMsg(msg);
  }
}

// 发送按钮
$('#sendBtn').on('click',function(){
  var msg = $('#chatContent').val();
  sendMsg(msg);
});

//监听刷新页面事件方法
window.onbeforeunload = function(event){
  ws.close();
};