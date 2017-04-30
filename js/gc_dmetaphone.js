var dosearch = true;
var newlookup = '';
var $ = jQuery;
$(document).ready(function () {
    $('#gc_doublem_input').on('keyup', function (e) {
        lookup($(this).val());
    });

    $(document).mouseup(function (e)
    {
        var container = $("#suggestions");
        var container2 = $("#search");

        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0 // ... nor a descendant of the container
                && !container2.is(e.target) // if the target of the click isn't the container...
                && container2.has(e.target).length === 0 // ... nor a descendant of the container
                )
        {
            container.hide();
            $('#search_shadow').removeClass('search_result');
            $('body').removeClass('rc_result');
        }
    });

    $('#bit_closeButton').on('click', function () {
        $("#suggestions").hide();
        $('#search_shadow').removeClass('search_result');
        $('body').removeClass('rc_result');
    });

    $('#searchform').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#gc_doublem_input').on('click', function () {
        $('#search_shadow').addClass('search_result');
        $('body').addClass('rc_result');
    });
    
    $('#suggestions').css('top',$('#searchform').position().top + $('#gc_doublem_input').height());
});

//AJAX Call
function lookup(inputString) {
    var minl = $('#gc_num_chars').val();   //minimum chars to start the ajax search :: will make the backend settings
    if (inputString.length == 0) {
        // Hide the suggestion box.
        $('#suggestions').hide();
        $('body').removeClass('rc_result');
    } else if (dosearch == true) {
        if (inputString.length >= minl) {
            //showing the loading icon
            $('#autoSuggestionsList').css('opacity', '0.9');
            $("#suggestions").show();
            $('body').addClass('rc_result');
            $('#suggestions .loading').show();
            dosearch = false;
            var ajxurl = $('#gc_siteurl').val();
            //Appending / if not in the url
            if (ajxurl.slice(-1) != '/') {
                ajxurl += '/';
            }
            $.post(ajxurl, {action: 'gc_dm_search_products', key: "" + escape(inputString) + ""}, function (data) {

                $('#suggestions').show();
                $('#autoSuggestionsList').html(data);
                //add new class to body
                $('#search_shadow').addClass('search_result');

                if (data.length > 0) {
                    $('#product_list').DataTable({
                        "aaSorting": [],
                    });
                } else {
                    $('#autoSuggestionsList').html('<div id="bit_closeButton">X</div><p class="no_result">No Matching Result Found. Please try updating your query.</p>');
                }
                $('#bit_closeButton').on('click', function () {
                    $("#suggestions").hide();
                    $('#search_shadow').removeClass('search_result');
                    $('body').removeClass('rc_result');
                });
                $('#suggestions .loading').hide();
                $('#autoSuggestionsList').css('opacity', '1');

                dosearch = true;
                if (newlookup != '') {
                    lookup(newlookup);
                    newlookup = '';
                }
            });
        }
    } else if (dosearch == false && inputString.length >= minl) {
        newlookup = $('#gc_doublem_input').val();
    }
} // lookup