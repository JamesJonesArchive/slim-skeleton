$.fn.dataTableExt.oApi.fnHideEmptyColumns = function ( oSettings, tableObject )
{
    /**
     * This plugin hides the columns that are empty.
     * If you are using datatable inside jquery tabs
     * you have to add manually this piece of code
     * in the tabs initialization
     * $("#mytable").datatables().fnAdjustColumnSizing();
     * where #mytable is the selector of table
     * object pointing to this plugin.
     * This plugin can be invoked from
     * <a href="//legacy.datatables.net/ref#fnInitComplete">fnInitComplete</a> callback.
     * @author John Diaz
     * @version 1.0
     * @date 06/28/2013
     */
    var selector = tableObject.selector;
    var columnsToHide = [];

    $(selector).find('th').each(function(i) {

        var columnIndex = $(this).index();
        var rows = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')'); //Find all rows of each column
        var rowsLength = $(rows).length;
        var emptyRows = 0;

        rows.each(function(r) {
            if (this.innerHTML == '')
                emptyRows++;
        });

        if(emptyRows == rowsLength) {
            columnsToHide.push(columnIndex);  //If all rows in the colmun are empty, add index to array
        }
    });
    for(var i=0; i< columnsToHide.length; i++) {
        tableObject.fnSetColumnVis( columnsToHide[i], false ); //Hide columns by index
    }
    /**
     * The following line doesn't work when the plugin
     * is used inside jquery tabs, then you should
     * add manually this piece of code inside
     * the tabs initialization where ("#myTable") is
     * your table id selector
     * ej: $("#myTable").dataTable().fnAdjustColumnSizing();
     */

    tableObject.fnAdjustColumnSizing();
}
