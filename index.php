<!doctype html>
<html xmlns:ng="http://angularjs.org"><head>
	<title>GoPro Videos</title>
<!-- Chrome, Firefox OS and Opera -->
<meta name="theme-color" content="#006bb3">
<!-- Windows Phone -->
<meta name="msapplication-navbutton-color" content="#006bb3">
<!-- iOS Safari -->
<meta name="apple-mobile-web-app-status-bar-style" content="#006bb3">

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.css">
<link href="//fonts.googleapis.com/css?family=EB+Garamond" rel="stylesheet">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqcloud/1.0.4/jqcloud.css">
<link href="//vjs.zencdn.net/5.19.1/video-js.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/list.css" />
</head>
<BODY style="background:#eee">

<!--
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">GoPro</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Page 1</a></li>
      <li><a href="#">Page 2</a></li>
      <li>
<div class="dropdown">
    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-cog" aria-label="Preferences"></i></span>
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li class="dropdown-header">Quality</li>
      <li><a href="#">HTML</a></li>
      <li><a href="#">CSS</a></li>
      <li><a href="#">JavaScript</a></li>
      <li class="divider"></li>
      <li class="dropdown-header">Preview</li>
      <li><i class="fa fa-check"><a href="#">Thumbnail</a></i></li>
      <li><a href="#">Filmstrip</a></li>

    </ul></li>
    </ul>
  </div>
</nav>
-->
<?/*
print $nav;
<div id="tagcloud" style="width:50%;height:200px;"></div>
 */?>
<div class="container" ng-app="GoPro-IndexApp" ng-controller="listCtrl"> 

<div class="row rcorners " ng-cloak ng-repeat="x in json.vid">
<form novalidate name="form{{ x.id }}" ng-submit="PushChanges(x.id,x.sql)" class="simple-form">
<div class="col-lg-4 mediabox" ng-click="showVideo(x.id)">
<!--    <span id="video_{{x.id}}" class="" style="display:none">_video here_</span><span id="preview_{{x.id}}"><p class="video-preview" data-frames="60" style="height:200px" data-source="{{ x.files.strip }}"></p></span>
-->
<img ng-src="{{ x.files.thumb }}">
</div>
<div class="col-lg-4" ng-hidden>{{video}}</div>
<div class="debug col-lg-8">
    <div class="titlename-group">
        <input type="text" class="form-control titleText noborder" name="name" placeholder="untitled" ng-model="x.sql.name" alt="NAME">
    </div>
    <div class="">
    <span class="col-lg-3">{{ x.jsdt | date:'MMMM d, y h:mm a' }}</span>
    <span class="col-lg-2">Duration: {{ FormatDuration(x.duration) }}</span>
    <span class="col-lg-2">ID: {{ x.id }}</span>
    </div>
	<div id="desc-group" class="form-group">
	    <input type="text" class="form-control noborder" name="desc" placeholder="add a description" ng-model="x.sql.description">
	</div>
	<div id="tags-group" class="form-group">
        <p ng-repeat="y in x.sql.tags">{{ y }}</p><input type="text" class="noborder input-tags" style="border:none;" name="tags" placeholder="tags" ng-model="x.sql.meta">
	</div>
<input ng-hide="!form{{x.id}}.$dirty" type="submit" id="submit" value="Submit"/>
    <div class="">
        <!-- File: {{ x.files.original }}<br> -->
	</div>

</FORM>
<!--End of row -->
</div>


<!--Container end-->
</div>

<pre>json.vid = {{json.vid | json}}</pre>
<pre>master = {{master | json}}</pre>


<script src="js/ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="js/ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-cookies.js"></script>

<script src="js/angular-input-modified.js"></script>

<script src="js/ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script src="js/ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
<script src="js/cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.js"></script>

<script src="js/use.fontawesome.com/f006bf6c27.js"></script>
<script src="js/maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="js/cdnjs.cloudflare.com/ajax/libs/jqcloud/1.0.4/jqcloud-1.0.4.js"></script>
<script src="js/magic.js"></script>
<script src="js/vjs.zencdn.net/5.19.1/video.js"></script>
    <!-- If you'd like to support IE8 -->
   <!-- <script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script> -->
<script src="js/cdn.rawgit.com/erasche/videojs-framebyframe/c9dd2b6bf0e7b527aa505d0e9e19ffe756eb1ee5/video-framebyframe.js"></script>
<script src="js/gopro.js"></script>
<script>

$(document).ready(function() {
    //ON PAGE LOAD
    setTimeout(stripview, 0);
});

</script>
</BODY>
</HTML>
