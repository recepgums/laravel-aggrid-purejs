<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AGGrid Test</title>

    <style>
        .ag-theme-alpine-dark {
					alpine-active-color: #f05340;
					range-selection-border-color: #f05340;
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
<script src="https://unpkg.com/ag-grid-community@28.2.1/dist/ag-grid-community.min.noStyle.js"></script>
<script src="https://unpkg.com/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div id="myGrid" style="width: 100vw; height: 90vh;" class="ag-theme-alpine"></div>
</div>

<script>
const gridOptions = {
    rowModelType: "serverSide",
    columnDefs: [
        { field: "id", filter: "agNumberColumnFilter" },
        { field: "message", filter: "agTextColumnFilter" },
        { field: "posts.title", headerName: "Post", filter: "agTextColumnFilter" },
				{ field: "users.name", headerName: "User", filter: "agTextColumnFilter",
                searchable: true,
								resizable: true,
								enableRowGroup: true,
							// group
							 /* rowGroup: true, hide: true */
 },
    ],

    defaultColDef: {
        flex: 1,
        minWidth: 100,
        floatingFilter: true,
        sortable: true,
    },

    groupIncludeFooter: true,
    groupIncludeTotalFooter: true,

    serverSideInfiniteScroll: true,
    animateRows: true,
    pagination: true,
    paginationAutoPageSize: true,
		enableRangeSelection: true,
		groupIncludeFooter: true,
		groupIncludeTotalFooter: true,
		serverSideInfiniteScroll: true,
		pagination: true,
		paginationPageSize: 100,
		paginationAutoPageSize: true,
		rowModelType: "serverSide",
		statusBar: {
				statusPanels: [
						{statusPanel: 'agTotalAndFilteredRowCountComponent', key: 'totalAndFilter', align: 'left'},
						{statusPanel: 'agSelectedRowCountComponent', align: 'left'},
						{statusPanel: 'agAggregationComponent', align: 'right'}
				]
		},
		sideBar: ['columns', 'filters'],
};

const gridDiv = document.querySelector("#myGrid");
new agGrid.Grid(gridDiv, gridOptions);

const datasource = {
    getRows(params) {
        fetch("/api/messages/", {
            method: "post",
            body: JSON.stringify(params.request),
            headers: { "Content-Type": "application/json; charset=utf-8" },
        })
            .then((httpResponse) => httpResponse.json())
            .then((response) => {
                params.successCallback(response.rows, response.lastRow);
            })
            .catch((error) => {
                console.error(error);
                params.failCallback();
            });
    },
};

gridOptions.api.setServerSideDatasource(datasource);
</script>
</body>
</html>
