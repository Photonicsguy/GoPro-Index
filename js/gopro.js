var n = new Date();
var expiresValue= new Date(n.getFullYear()+1, n.getMonth(), n.getDate());
    // this will set the expiration to 12 months


var app = angular.module('GoPro-IndexApp', ['ngCookies','ngInputModified']);


	app.config(['$cookiesProvider', function($cookiesProvider) {
  }])


    app.controller('listCtrl', function($scope, $http, $cookies) {
    $http.get("json.php").then(function (response) {
		$scope.json = response.data;
		$scope.jsonorig = response.data;
		
	});

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
this.on('timeupdate', function () {
      //$('#time').text(this.currentTime());
    })

});
    };

    });


(function($) {
        $.fn.videoPreview = function(options) {
            return this.each(function() {
                var elm = $(this);
                var frames = parseFloat(elm.data('frames'));

                var img = $('<img/>', { 'src': elm.data('source') }).hide().css({
                    'position': 'absolute',
                    'cursor': 'pointer'
                }).appendTo(elm);
                var slider = $('<div/>').hide().css({
                    'width': '2px',
                    'height': '100%',
                    'background': '#ddd',
                    'position': 'absolute',
                    'z-ndex': '1',
                    'top': '0',
                    'opacity': 0.6,
                    'cursor': 'pointer'
                }).appendTo(elm);

                var width;

                function defaultPos() { // 25% in, so 15th frame out of 60
                    img.css('left', -width * frames / 4);
                }

                img.on('load',function() {
                    $(this).show();
                    width = this.width / frames;
                    elm.css('width', width);
                    defaultPos();
                });
                elm.mousemove(function(e) {
                    var left = (e.clientX - elm.offset().left);
                    if(left<0) {    // TODO Do all the frames show?
                        left=0;
                    }
                    slider.show().css('left', left - 1); // -1 because it's 2px width
                    img.css('left', -Math.floor((left / width) * frames) * width);
                    var frm = Math.floor((left / width) * frames);
                    var frame =  -frm * width;
                    //console.log(frm+"/"+frames+", left: "+left+", clientX: " + e.clientX+", offset position: "+elm.offset().left); 
                }).mouseout(function(e) {
                    slider.hide();
                    defaultPos();
                });

            });
        };
    })(jQuery);



var stripview = function() {
    $('.video-preview').videoPreview();
}


