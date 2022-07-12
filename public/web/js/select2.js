$(function() {

  $(document.body).on("change","#select_option_id",function(){
    $("#select_option_id option").each(function()
    {
       if($(this).val().slice(0,1) == "#"){
            is_color = true;
       }else{
        is_color = false;
       }
    });
    if(is_color){
        $(".select2-selection__choice").map(function(element, count)
        {
         color = $(this).attr('title')
         $(this).css("background-color", color)
        });
    }
    });






    
    
    
    

    $(document.body).on("click","span[aria-owns=select2-select_option_id-results]",function(){
        var listItems = $("#select2-select_option_id-results  li.select2-results__option");
        listItems.each(function(idx, li) {
            var product = $(li);
            color = product.text()
            product.css("background-color", color)
        });




     });


});

