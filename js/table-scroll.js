function moveScroll(){
    var scroll = $(window).scrollTop();
    var anchor_top = $("#maintable").offset().top;
    var anchor_bottom = $("#bottom_anchor").offset().top;
    if (scroll > anchor_top && scroll < anchor_bottom) {
        clone_table = $("#clone");
        if(clone_table.length === 0) {          
            clone_table = $("#maintable").clone();
            clone_table.attr({id: "clone"})
            .css({
                position: "fixed",
                "pointer-events": "none",
                 top:0
            })
            .width($("#maintable").width());

            $("#table-container").append(clone_table);
            // dont hide the whole table or you lose border style & 
            // actively match the inline width to the #maintable width if the 
            // container holding the table (window, iframe, div) changes width          
            $("#clone").width($("#maintable").width());
            // only the clone thead remains visible
            $("#clone thead").css({
                visibility:"visible"
            });
            // clone tbody is hidden
            $("#clone tbody").css({
                visibility:"hidden"
            });
            // add support for a tfoot element
            // and hide its cloned version too
            var footEl = $("#clone tfoot");
            if(footEl.length){
                footEl.css({
                    visibility:"hidden"
                });
            }
        }
    } 
    else {
        $("#clone").remove();
    }
}
$(window).scroll(moveScroll);