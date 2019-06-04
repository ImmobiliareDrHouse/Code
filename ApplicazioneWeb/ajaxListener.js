$(document).ready(function(){
    $('#Prov').on('change',function() {

        var pr = $("#Prov").val();
            $.ajax({
                type:'POST',
                url:'ajaxData.php',
                data: {prov : pr},
                success:function(html){
                    $('#cit').html(html);
                }
        });
    });
});