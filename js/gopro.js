// Cookies Stuff
var n = new Date();
var expiresValue= new Date(n.getFullYear()+1, n.getMonth(), n.getDate());
    // this will set the expiration to 12 months


var app = angular.module('GoPro-IndexApp', ['ngCookies','ngInputModified']);


	app.config(['$cookiesProvider', function($cookiesProvider) {
  }])


	app.directive('strip', function() {
    return {
        restrict: 'E',
        replace: true,
        template: function(tElement, tAttrs) {
            console.log("Frames: "+tAttrs.frames);
            
            var id=tAttrs.id;
            var div='<div class="col-lg-4 mediabox" style="overflow:hidden;height:200px;width:355px;padding:0" ng-mouseleave="Mleave($event)" ng-mousemove="captureCoordinate($event)"><img id="img_'+id+'" ng-src="cache/'+id+'_strip.jpg" style="position: absolute; cursor: pointer; left: -5325px;"><div id="slider_'+id+'" style="width: 2px; height: 100%; background: rgb(221, 221, 221); position: absolute; top: 0px; opacity: 0.6; cursor: pointer; left: 340px; display: none;">';
            return div;
        },
        transclude: true,
    };
});

    app.controller('listCtrl', function($scope, $http, $cookies) {
    $http.get("json.php").then(function (response) {
		$scope.json = response.data;
		$scope.jsonorig = response.data;
		
	});

		$scope.captureCoordinate = function($event) {
            var id=$event.currentTarget.id;
			var rect = $event.currentTarget.getBoundingClientRect();
            var left=$event.x-rect.left;
            var pos=left/($event.currentTarget.clientWidth);

	        $("#slider_"+id).show().css('left', left-1 )
        var width=$("#img_"+id).width();
        var frames=60;
		if(pos<0)pos=0;		//FIXME
        var frame=Math.floor(pos*frames);
        var ipos=-Math.floor(pos*frames) * (width/frames);
//        console.log("Width: "+width+", Pos: "+pos+", Frames: "+frames+", Frame: "+frame+", Calc: "+ipos);
        $("#img_"+id).css('left',ipos);
            
    };
        $scope.Mleave = function($event) {
            var id=$event.currentTarget.id;
            $("#slider_"+id).hide();
        $("#img_"+id).css('left',-$("#img_"+id).width()/4);

        };



	// Format Duration, Either "2h23m15s", "23m15s", or "15s"
    $scope.FormatDuration = function(duration){
         var hh=Math.floor(duration/3600);
         var mm=Math.floor((duration-(hh*3600))/60);
         var ss=duration-(hh*3600)-(mm*60);
         if(hh>0)
             return hh+"h"+mm+"m"+ss+"s";
         else
			 if(mm>0)
			    return mm+"m"+ss+"s";
			else
				return ss+"s";
    };
		$scope.master = {};
	$scope.update = function(user) {
        $scope.master = angular.copy(user);
      };
$scope.reset = function() {
        $scope.user = angular.copy($scope.master);
      };

      $scope.reset();

	$scope.PushChanges = function(id,sql){
		var data = {};
		data.username = "unknown";
		data.id	  = id;
		data.name = sql.name;
		data.desc = sql.description;
		data.meta = sql.meta;
				//form.$setPristine();
		
		$http.post("process.php", data).success(function (data,status,headers) {
			if(status==200) {
				// Need to hide submit button for form now...
				// Also need to provide feedback toast message
				console.log(data);
			}else{
				alert("Error: "+status);
			}
			console.log("Status: "+status);
			console.log("ID: "+sql.id+" submitted");
});

console.log("ID: "+sql.id);
console.log("Name: "+sql.name);
console.log("Desc: "+sql.description);
console.log("Meta: "+sql.meta);

};

    $scope.showVideo=function(id,seek){
        document.getElementById("preview_" + id).style.display='none';
        document.getElementById("video_" + id).innerHTML="<video id=\"vid_" + id + "\" class=\"video-js\" width=\"640\" height=\"264\" poster=\"GOPRO_cache/" + id + "_thumb.jpg\" data-setup='{\"fluid\":false, \"controls\":true, \"autoplay\":true, \"preload\":\"auto\", \"plugins\":{ \"framebyframe\":{ \"fps\":29.97, \"steps\": [         { \"text\": \"-1\", \"step\": -1 },         { \"text\": \"+1\", \"step\": 1 }       ]}  } }'><source src=\"GOPRO_cache/" + id + "_med.mp4\" type='video/mp4'></video><div id=\"time\"><a href=\"#!\" onclick=\"trim()\">trim</a></div>";
        document.getElementById("video_" + id).style.display='';
videojs("vid_" + id).ready(function(){
    myPlayer = this;
    if(seek===null){
    }else {
        myPlayer.currentTime(seek);
        seek=null;
    }
/*this.on('timeupdate', function () {
      //$('#time').text(this.currentTime());
    })*/

});
    };


    });

