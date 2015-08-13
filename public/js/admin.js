$(document).ready(function() {
    
   $("#name").blur(function(){
      var nameInput = $(this).val();
      var slugInput = $('#slug').val();
      if (slugInput != makeSlug(nameInput)) {
          $('#slug').val(makeSlug(nameInput));
      }
      if ($("#display_name").val() == '') {
          $('#display_name').val(nameInput);
      }

   }); 
   
   function makeSlug(str){
    return str
        .toLowerCase()
        .replace(/ /g,'_')
        .replace(/[^\w-]+/g,'')
        ; 
   }
   
   // if there are no children in category hierarchy, narrow the right_column
   // so that a vertical list of categories is displayed
   // if a hierarchy has parent-children relationships, there will be ul li tags
   var ulArr = $(".right_col").find('ul');

   if (ulArr.length == 0) {
       $(".right_col").width(220);

   }
   
    /*
     * Sortable categories
     */
    $(function() {
        
        var token = $('#token').val();
        
        $("#sortable").sortable();
        //$("#sortable").disableSelection();
        $('#sortable').sortable({
            axis: 'y',
            update: function (event, ui) {
                var data = $(this).sortable('serialize');
                // POST to server using $.post or $.ajax
                $.ajax({
                    headers: { 'X-XSRF-TOKEN' : token }, 
                    data: data,
                    type: 'POST',
                    url: '/categories/sort'
                });
            }
        });

    });

    var currentForm;
    $("#dialog").dialog({
       autoOpen: false,
       modal: true,
       buttons : {
            "Confirm" : function() {
                currentForm.submit();            
            },
            "Cancel" : function() {
              $(this).dialog("close");
            }
          }
    });

    $(".btn-delete").on("click", function(e) {
        currentForm = $(this.form);
        e.preventDefault();
        $("#dialog").dialog("open");
    });
    
});