/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function dE(id, s){
	var e = document.getElementById(id);
	e.style.display = (s == 1) ? '' : 'none';
}

function showchatsize(s){
	dE('chatsize',s);
}
      
function setclienttype(){
    if(document.getElementById("servertype1").checked){
        dE('client_type',0);
    }else if(document.getElementById("servertype0").checked){
        dE('client_type',1);
    }
}
      
function chooseservertype(s){
	dE('chat_port',1);
	dE('chat_host',1);
	dE('http_port',1);
    if(s == 1){
        document.getElementById("default_server_port").innerHTML = '51212';
        document.getElementById("default_http_port").innerHTML = '31212';
        document.getElementById("client_loc0").innerHTML = document.getElementById("client_loc0").innerHTML.replace("35555","31212");
        
        document.getElementById("fc_server_port").value = '51212';
        document.getElementById("fc_server_port_h").value = '31212';
        document.getElementById("fc_client_loc").value = document.getElementById("fc_client_loc").value.replace("35555","31212");
        
        dE('client_type',0);
        
    }else{
        document.getElementById("default_server_port").innerHTML = '51127';
        document.getElementById("default_http_port").innerHTML = '35555';
        document.getElementById("client_loc0").innerHTML = document.getElementById("client_loc0").innerHTML.replace("31212","35555");
        
        document.getElementById("fc_server_port").value = '51127';
        document.getElementById("fc_server_port_h").value = '35555';
        document.getElementById("fc_client_loc").value = document.getElementById("fc_client_loc").value.replace("31212","35555");
        
        dE('client_type',1);
    }
}
      
function changeserver(id){
	if (id == 0){
		if (document.getElementById("server_loc").value == 0){
			dE('chat_port',0);
			dE('chat_host',0);
			dE('http_port',0);
		}else{
			dE('chat_port',1);
			dE('chat_host',1);
			dE('http_port',1);
		}
		dE('login_chat',1);
		dE('client_location',1);
		dE('client_loc0',1);
		dE('client_loc1',0);                
		dE('room_name',0);
		dE('u_al',1);
		dE('u_as',1);
        dE('set_skin',0);
        dE('set_lang',0);
        dE('room_list',1);
        dE('servertype',1);
		setclienttype();
	}else if (id == 1){
		dE('chat_port',0);
		dE('chat_host',0);
		dE('http_port',0);
		dE('login_chat',1);
		dE('client_location',1);                
		dE('client_loc0',0);
        dE('client_loc1',1);                
		dE('room_name',0);
		dE('u_al',1);
		dE('u_as',1);
		dE('set_skin',0);
		dE('set_lang',0);
		dE('room_list',1);
		dE('servertype',0);
		dE('client_type',1);
              
	}else{
		dE('chat_port',0);
		dE('chat_host',0);
		dE('http_port',0);
		dE('client_location',0);
		dE('room_name',1);
		dE('login_chat',0);
		dE('u_al',0);
		dE('u_as',0);
		dE('set_skin',1);
		dE('set_lang',1);
		dE('room_list',0);
		dE('servertype',0);
        dE('client_type',0);
	}
}