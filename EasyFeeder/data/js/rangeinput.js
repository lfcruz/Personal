/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */
var bslider = document.getElementById('bduration');
var boutput = document.getElementById('bdurationvalue');
var lslider = document.getElementById('lduration');
var loutput = document.getElementById('ldurationvalue');
var dslider = document.getElementById('dduration');
var doutput = document.getElementById('ddurationvalue');
boutput.innerHTML = bslider.value;
loutput.innerHTML = lslider.value;
doutput.innerHTML = dslider.value;
bslider.oninput = function(){
    boutput.innerHTML = this.value;
};
lslider.oninput = function(){
    loutput.innerHTML = this.value;
};
dslider.oninput = function(){
    doutput.innerHTML = this.value;
};

