 function makeAjaxRequest() {
     $.ajax({
         url: 'targetsListView.php',
         cache: false,
         type: 'get',
         success: function(data) {
             if(data!==false) {
                 targetsDS = JSON.parse(data);
                 var row =$("<tr><td>" + targetsDS.targetsid + "</td><td>" + targetsDS.targetsname +
                         "</td><td>" + targetsDS.targetscount + "</td><td>" + targetsDS.targetsstatus+
                         "</td><td>"+
                         "<button type='button' class='btn btn-link btnDeleteMi' onclick='borraMi();'>\n\
                         <span class='glyphicon glyphicon-remove'>Borrar</span></button>"+
                         "</td></tr>" );
                 $('table#targetsListView tbody').show();       
                 $('table#targetsListView tbody').html(row);
             } else {
                 $('table#targetsListView tbody').show();
                 $('table#targetsListView tbody').html("<tr style=\"color: red; \">\n\
                 <td colspan='6'><b> !!! EMPTY !!! </b></td></tr>");
             }
                           
         }
                    
     });
 }

jQuery(document).ready(function($) {
    $('#targetsListView').show();
    $('table#targetsListView tbody').show();
    $('table#targetsListView tbody').html("<tr style=\"color: red; \">\n\<td colspan='6'><b> !!! EMPTY !!! </b></td></tr>");
    makeAjaxRequest();
});