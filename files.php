<?PHP
$hide=array("md5"=>true);

?>
<HTML>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script src="./js/jquery-1.10.2.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>
var app = angular.module('fileApp', []);
app.controller('filelistCtrl', function($scope, $http) {
	var data = { mode: "fileList"};
	$http({ url:"json.php",
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $.param({mode: "fileList"}) })
    .then(function (response) {$scope.json = response.data;});
});
</script>
</head>
<BODY>

<div ng-app="fileApp" ng-controller="filelistCtrl"> 
<table id="FileTable" class="table compact table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <TH>icons</TH>
        <th>File</th>
        <?if(!$hide["md5"]){?><th>MD5</th><?}?>
        <th>Timestamp</th>
        <th>Duration</th>
        <th>Filesize</th>
        <th>_high</th>
        <th>_med</th>
        <th>_low</th>
        <th>_strip</th>
        <th>_thumb</th>
      </tr>
    </thead>
    <tbody>
  <TR CLASS="{{ x.error ? 'danger':'sucess' }}" ng-repeat="x in json.vid">
    <td>{{ x.id }}</td>
    <td>&nbsp;</td>
    <td>{{ x.sql.filename }}</td>
<!--    <td>{{ x.sql.md5 }}</td> -->
    <td>{{ x.sql.dt }}</td>
    <td>{{ x.sql.duration }}</td>
    <td>{{ x.files.original }}</td>
    <td>{{ x.files.high }} </td>
    <td>{{ x.files.med }} </td>
    <td>{{ x.files.low }} </td>
    <td>{{ x.files.strip }} </td>
    <td>{{ x.files.thumb }} </td>
  </TR>
</TBODY>
</TABLE>

<?
/*
<div ng-repeat="x in json.vid" ID="{{ x.id }}">
{{ x.id }}
</div>
 */?>
</div>
</BODY>
</HTML>
