function validateDateTime(){
    var d = new Date();
    var fdia = null;
    if (d.getMonth()< 9){
        fdia = d.getFullYear() + "-0" + (d.getMonth()+1) + "-" + d.getDate();
    } else {
        fdia = d.getFullYear() + "-" + (d.getMonth()+1).toString()+"-"+d.getDate().toString();
    }
    document.write(fdia);
    $("#dia").attr("min",fdia);
}
