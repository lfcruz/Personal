/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */
var gateway = `ws://${window.location.hostname}/ws`;
var websocket;
window.addEventListener('load', onLoad);
function initWebSocket() {
    console.log('Trying to open a WebSocket connection...');
    websocket = new WebSocket(gateway);
    websocket.onopen    = onOpen;
    websocket.onclose   = onClose;
    websocket.onmessage = onMessage;
}
function onOpen(event) {
    console.log('Connection opened');
}
function onClose(event) {
    console.log('Connection closed');
    setTimeout(initWebSocket, 2000);
}
function onMessage(event) {
    var state;
    if (event.data === "1"){
      state = "ON";
    }
    else{
      state = "OFF";
    }
    document.getElementById('state').innerHTML = state;
}
function onLoad(event) {
    //initWebSocket();
    //initButton();
}
function initButton() {
    //document.getElementById('saveScheduler').addEventListener('click', saveConfiguration('Scheduler'));
    //document.getElementById('saveWireless').addEventListener('click', saveConfiguration('Wireless'));
    
}
function openMeal(mealName) {
    var i;
    var x = document.getElementsByClassName("meal");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";  
    }
    document.getElementById(mealName).style.display = "block";  
}
function saveConfiguration(dtoType){
    const objWireless = {
        ssid:null,
        password:null,
        setssid(value){
            this.ssid = value;
        },
        setpassword(value){
            this.password = value;
        }
    };
    const objScheduler = {
        breakfast:{
            hour:null,
            duration:null
        },
        lunch:{
            hour:null,
            duration:null
        },
        dinner:{
            hour:null,
            duration:null
        },
        setbreakfast(value1, value2){
            this.breakfast.hour = value1;
            this.breakfast.duration = value2;
        },
        setlunch(value1, value2){
            this.lunch.hour = value1;
            this.lunch.duration = value2;
        },
        setdinner(value1, value2){
            this.dinner.hour = value1;
            this.dinner.duration = value2;
        }
    };
    switch (dtoType){
        case 'Wireless':
            objWireless.setssid(document.getElementById('wifissid').value);
            objWireless.setpassword(document.getElementById('wifipassword').value);
            websocket.send(JSON.stringify(objWireless));
            alert('Saving '+dtoType+' having: '+JSON.stringify(objWireless));
            break;
        case 'Scheduler': 
            objScheduler.setbreakfast(document.getElementById('bhour').value, document.getElementById('bduration').value);
            objScheduler.setlunch(document.getElementById('lhour').value, document.getElementById('lduration').value);
            objScheduler.setdinner(document.getElementById('dhour').value, document.getElementById('dduration').value);
            websocket.send(JSON.stringify(objScheduler));
            alert('Saving '+dtoType+' having: '+JSON.stringify(objScheduler));
            break;
        default:
            break;
    }
}

