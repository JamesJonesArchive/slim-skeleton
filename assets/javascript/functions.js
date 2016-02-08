// Save selected identifier type to a hidden variable
function setIdentifierType(type, name) {
    $("#identifierType").val(type);
    $("#dLabel").html(name + ' <span class="caret"></span>');
    $( "#searchTerm" ).focus();
}

// Create a search URL and redirect the user
function submitSearch() {
    if ($("#searchTerm").val() !== '' && $("#identifierType").val() !== '') {
        $('#searchSubmitButton').toggleClass('active');
        $('#searchResults').fadeOut();

        var searchURL = baseUrl() + '/search/' + $("#identifierType").val() + '/' + encodeURIComponent($("#searchTerm").val());
        window.location.href = searchURL;
    } else {
        if ($("#searchTerm").val() === '') {
            $('#searchTerm').tooltip('show');
        }
        if ($("#identifierType").val() === '') {
            $('#dLabel').tooltip('show');
        }
    }
}

// Get the base URL of the current page
function baseUrl() {
    var pathArray = location.href.split( '/' );
    var protocol = pathArray[0];
    var host = pathArray[2];
    var url = protocol + '//' + host;

    return url;
}
