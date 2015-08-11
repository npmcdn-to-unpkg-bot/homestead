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
   console.log('len:'+ulArr.length);
   if (ulArr.length == 0) {
       $(".right_col").width(220);

   }
    
});