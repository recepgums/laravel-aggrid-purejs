<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet"
          href="https://aygin.clinic/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <script src="https://unpkg.com/@ag-grid-enterprise/all-modules@25.1.0/dist/ag-grid-enterprise.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>
    <style>
        .ag-theme-alpine-dark {
        ag-theme-alpine-dark(
        (
        alpine-active-color: #f05340,
        range-selection-border-color: #f05340,
        )
        );
        }

        .text {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
            Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }
        body,
        html {
            height: 100%;
            width: 100%;
        }

    </style>
</head>
<body>
<div class="container-fluid">
    <div id="myGrid" style="width: 100vw; height: 90vh;" class="ag-theme-balham-dark"


    ></div>
</div>
</body>
<script>
    const localMaxWidth = 150;
    const localMinWidth = 100;
    const columnDefs =[
        {
            field: 'athlete',
            filter: 'agSetColumnFilter',
            filterParams: {
                values: params => {
                    const field = params.colDef.field;
                    params.success(getValues(field));
                }
            }
        },
        {
            field: 'age', filter: 'agSetColumnFilter',
            filterParams: {
                values: params => {
                    const field = params.colDef.field;
                    params.success(getValues(field));
                }
            },
        },
        {
            field: 'country',
            enableRowGroup: true,
            filter: 'agSetColumnFilter',
            filterParams: {
                values: params => {
                    const field = params.colDef.field;
                    params.success(getValues(field));
                }
            }
        },
        { field: 'year', enableRowGroup: true },
        { field: 'date', sortable: false },
        { field: 'sport' },
        { field: 'gold', enableValue:true, aggFunc: 'sum', allowedAggFuncs:['avg','count','sum','min','max']},
        { field: 'silver', enableValue:true, aggFunc: 'sum', allowedAggFuncs:['avg','count','sum','min','max'] },
        { field: 'bronze', enableValue:true, aggFunc: 'sum', allowedAggFuncs:['avg','count','sum','min','max'] },
        { field: 'total', enableValue:true, aggFunc: 'sum', allowedAggFuncs:['avg','count','sum','min','max'] },
    ]



    const gridOptions = {
        serverSideStoreType: 'partial',
        rowModelType: 'serverSide',
        serverSideInfiniteScroll: true,
        columnDefs: columnDefs,
        animateRows: true,
        pagination:true,
        paginationAutoPageSize:true,
        rowGroupColumns:'always',
        enableRangeSelection:true,
        onGridReady: (params) => {
            let gridApi = params.api;
            let gridColumnApi = params.columnApi;

            const datasource = {
                getRows: params => {
                    // if filtering on group column, then change the filterModel key to have country as key
                    if (params.request.filterModel['ag-Grid-AutoColumn']) {
                        params.request.filterModel['country'] = params.request.filterModel['ag-Grid-AutoColumn'];
                        delete params.request.filterModel['ag-Grid-AutoColumn'];
                    }

                    let resp = getAthletes(JSON.stringify({ ...params.request }))
                    params.success({
                        rowData: resp.rows,
                        rowCount: resp.lastRow
                    })
                }
            }

            // setting the datasource, the grid will call getRows to pass the request
            params.api.setServerSideDatasource(datasource);
        },
        defaultColDef:{
            flex: 1,
            minWidth: 100,
            floatingFilter: true,
            sortable: true
        },
        debug: true,
        rowClass:'textCenter',
        resizable:true,
        floatingFilter:true,
        flex:1,
        statusBar: {
            statusPanels: [
                { statusPanel: 'agTotalAndFilteredRowCountComponent', align: 'left' },
            ],
        },
        autoGroupColumnDef : {
            headerName: 'Group',
            minWidth: 250,
            field: 'country',
            filter: 'agSetColumnFilter',
            filterParams: {
                values: params => {
                    const field = params.colDef.field;
                    params.success(getValues(field));
                }
            }
        }
    };


    var eGridDiv = document.querySelector('#myGrid');

    // create the grid passing in the div to use together with the columns & data we want to use
    new agGrid.Grid(eGridDiv, gridOptions);

    document.addEventListener('DOMContentLoaded', () => {
        $.ajax({
            method: "POST",
            url: `{{route('getData')}}`,
            data: JSON.stringify({
                "startRow": 0,
                "endRow": 100,
                "rowGroupCols": [],
                "valueCols": [
                    {
                        "id": "gold",
                        "aggFunc": "sum",
                        "displayName": "Gold",
                        "field": "gold"
                    },
                    {
                        "id": "silver",
                        "aggFunc": "sum",
                        "displayName": "Silver",
                        "field": "silver"
                    },
                    {
                        "id": "bronze",
                        "aggFunc": "sum",
                        "displayName": "Bronze",
                        "field": "bronze"
                    },
                    {
                        "id": "total",
                        "aggFunc": "sum",
                        "displayName": "Total",
                        "field": "total"
                    }
                ],
                "pivotCols": [],
                "pivotMode": false,
                "groupKeys": [],
                "filterModel": {},
                "sortModel": []
            }),
            contentType: "application/json; charset=utf-8",
            traditional: true,
        })
            .done(function( resp ) {
                gridOptions.api.setRowData(resp.rows);
            });

    });

    function getValues(field){
        var getValuesResponse;

        $.ajax({
            method: "GET",
            url: 'http://127.0.0.1:8000/api/olympicWinners/' + field,
            contentType: "application/json; charset=utf-8",
            traditional: true,
            async:false
        }).done(function (resp) {
            getValuesResponse = resp
            console.log(resp)
        });

       return getValuesResponse
    }

    function getAthletes(header) {

        var getValuesResponse;
        $.ajax({
            method: "POST",
            url: `{{route('getData')}}`,
            data: JSON.stringify({
                "startRow": 0,
                "endRow": 100,
                "rowGroupCols": [],
                "valueCols": [
                    {
                        "id": "gold",
                        "aggFunc": "sum",
                        "displayName": "Gold",
                        "field": "gold"
                    },
                    {
                        "id": "silver",
                        "aggFunc": "sum",
                        "displayName": "Silver",
                        "field": "silver"
                    },
                    {
                        "id": "bronze",
                        "aggFunc": "sum",
                        "displayName": "Bronze",
                        "field": "bronze"
                    },
                    {
                        "id": "total",
                        "aggFunc": "sum",
                        "displayName": "Total",
                        "field": "total"
                    }
                ],
                "pivotCols": [],
                "pivotMode": false,
                "groupKeys": [],
                "filterModel": {},
                "sortModel": []
            }),
            contentType: "application/json; charset=utf-8",
            traditional: true,
        })
            .done(function( resp ) {
                getValuesResponse = resp
                gridOptions.api.setRowData(resp.rows);
            });

        return getValuesResponse
    }

</script>
</html>
