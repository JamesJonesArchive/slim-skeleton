$(document).ready(function(){

    // fix the search term/identifer when hitting these conditions:
    // * Submit search that returns multiple results
    // * Submit search that results in one result
    // * Hit 'back' button to return to the first set of results
    if ($("#multiSearchTerm").length){
        if ($('#multiSearchTerm').val() !== $("#searchTerm").val()) {
            $("#searchTerm").val($('#multiSearchTerm').val());
        }
    }

    if ($("#multiSearchIdentifier").length){
        if ($('#identifierType').val() !== $("#multiSearchIdentifier").val()) {
            $("#identifierType").val($('#multiSearchIdentifier').val());
        }
    }

    $('#collapseEntitlements-heading').click(function (){
        $('#collapseEntitlements').collapse('toggle');
    });

    $('#collapseGroups-heading').click(function (){
        $('#collapseGroups').collapse('toggle');
    });

    $('#collapseAttrs-heading').click(function (){
        $('#collapseAttrs').collapse('toggle');
    });

    $('#collapseSRVact-heading').click(function (){
        $('#collapseSRVact').collapse('toggle');
    });

    //Configure Table when there are multiple possible results
    $('#resultsTable').DataTable({
        order: [],
        paging: false,
        searching: false,
        info: false,
        responsive: false,
        columnDefs: [
            { "orderable": false, "targets": 0 }
        ],
        fnInitComplete: function () {
            this.fnHideEmptyColumns(this);
        }
    });

    //Configure tooltips for error handling
    $('#searchTerm').tooltip({title: "Please enter a search term", trigger: "manual", placement: 'bottom'});
    $('#dLabel').tooltip({title: "Please select an identifier type", trigger: "manual", placement: 'top'});

    $("#searchSubmitButton").click(function(){
        submitSearch();
    });

    // Hide tooltips when error conditions clear
    $(document).keydown(function(e){
        if ($('#searchTerm').val() !== '') {
            $('#searchTerm').tooltip('hide');
        }
        if ($("#identifierType").val() !== '') {
            $('#dLabel').tooltip('hide');
        }
        // submit form on enter
        if (e.which == 13) {
            submitSearch();
        }
    });

    $("#dLabel").click(function (){
        $('#dLabel').tooltip('hide');
    });

    // search for a given usfid on the multiple-results page
    $(".viewButton").click(function(event){
        $(this).toggleClass('active');
        var searchURL = baseUrl() + '/search/usfid/' + encodeURIComponent($(this).attr('id'));
        window.location.href = searchURL;
    });

    // search for a given usfid on the multiple-results page
    $(".viewAdButton").click(function(event){
        $(this).toggleClass('active');
        var searchURL = baseUrl() + '/viewAD/' + encodeURIComponent($(this).attr('id'));
        window.location.href = searchURL;
    });
});
