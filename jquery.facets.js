/*
 * jQuery Facets Plugin v0.0.9
 * http://srchulo.com/jquery_plugins/jquery_facets.html
 *
 * Copyright 2013, Adam Hopkins
 * http://srchulo.com/
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

(function( $ ){

  var methods = { 
    init : function( options ) { 
      var plugin = this;
      var settings = $.extend( {
       'searchCont'         : '#searchCont', //A selector for where the filtered search results will go. i.e., a div.
       'ajaxURL'            : $(location).attr('href'), //URL to make ajax requests to for filtered results. Defaults to current URL.
       'URLParams'          : new Array(), //extra params for the url, such as the original search query or something. takes array in jQuery's serializeArray() format
                                           //i.e. 
                                           //[ 
                                           //   {
                                           //     "name":"search",
                                           //     "value":"brown jackets"
                                           //     }
                                           // ]
       'ajaxMethod'         : 'GET', // POST or GET
       'bindType'           : 'change',
       'bindTypes'          : new Array(),
       'excludeBindTypes'   : new Array(),
       'generateData'       : function () { 
    
          var settings = this.data("settings");
          var dataArr = remove_empty(this.serializeArray());
    
          //push on any user added params
          for(var k = 0; k < settings.URLParams.length; k++) { 
            dataArr.push(settings.URLParams[k]);
          }   

          return dataArr;
        }, //generates data from the facets form to send server-side!
       'preAJAX'            : function () { return true }, //pre-call ajax function, used for validation etc. returns true or false
       'postAJAX'           : function (data) { $(plugin.data("settings").searchCont).html(data) }, //post-call ajax function
       'preHash'            : function () {}, 
       'postHash'           : function () {}, 
       'hashOptions'        : new Array(), //takes a array of hashes of key value pairs. Must be in jQuery's serializeArray() format
       'hash'               : true, //whether or not to put facet values in url after hashtag #
       'shouldSubmit'       : false, //whether form can submit or not. Default is false.
      }, options);

      //set the settings for this object
      plugin.data("settings",settings);

      //bind each input to the appropriate bind type
      plugin.find(":input").each(function(){

        //find out if we should not bind to this input
        var excludeBindTypes = plugin.data('settings').excludeBindTypes;
        var shouldBind = true;
        for(var k = 0; k < excludeBindTypes.length; k++) { 
          if($(this).is(excludeBindTypes[k])) { 
            shouldBind = false;
            break;
          }   
        }   

        if(!shouldBind)
          return true;

        //use default bindType
        var bindType = plugin.data('settings').bindType;

        //see if this input has a special bindType
        var bindTypes = plugin.data('settings').bindTypes;
        for(var k = 0; k < bindTypes.length; k++) { 
          if($(this).is(bindTypes[k]['selector'])) { 
            bindType = bindTypes[k]['bindType'];
            break; //will get first bindType of selector it matches
          }   
        }   

        $(this).on(bindType, {'plugin': plugin}, methods.ajaxReq);  
      }); 

      if(plugin.data('settings').hash) 
        methods.hashInit.apply(plugin);

      if(!plugin.data('settings').shouldSubmit) { 
        plugin.submit(function(){ return false }); 
      }   

      return plugin;
    },  
    ajaxReq : function(event) { 
      //called by user
      var plugin = this;

      //called by bind event
      if(event != undefined)
          plugin = event.data.plugin;
    
      var settings = plugin.data("settings");

      if(!settings.preAJAX.call())
        return;

      //make ajax call to server for results
      $.ajax({
        type: settings.ajaxMethod,
        url: settings.ajaxURL,
        data: settings.generateData.apply(plugin),
      }).done(settings.postAJAX);

      if(settings.hash)
        return methods.hashURL.call(plugin,true);
    },  
    hashURL : function (plugin,calledByPlugin) { 
      //if called by user
      if(!calledByPlugin)
        plugin = this;

      var settings = plugin.data("settings");
      settings.preHash.call();

      //remove empty valued items from serialization
      //seems complicated, but necessary so we don't get empty selects and text inputs
      //I did a lot of searching, if anyone finds a better way please let me know...
      var arr = plugin.serializeArray();

      //push on any user added params
      for(var k = 0; k < settings.hashOptions.length; k++) { 
        arr.push(settings.hashOptions[k]);
      }   

      var serializeArr = remove_empty(arr);

      var hash = $.param(serializeArr);

      //to prevent scrolling to top of page. Stupid, I know. Let me know if there's a better way.
      if(hash == "" || hash == null || hash == undefined || hash.match(/^\s*$/))
        hash = "nothing";

      window.location.hash = hash;

      settings.postHash.call();

      return hash; //return resulting hash URL
    },  
    hashInit : function () { 
      var hash = window.location.hash;
      hash = hash.replace(/^#/, '');  
      if(hash != "" && hash != undefined && hash != null && hash != "nothing") { 
        this.deserialize(hash); 
        methods.ajaxReq.apply(this);
      }   
    },  
    get : function (key) { 
			return this.data("settings")[key];
		},
    set : function (key,value) { 
			var settings = this.data("settings");
			settings[key] = value;
			this.data("settings",settings);
    },  

  };  

  $.fn.facets = function( method ) { 
    // Method calling logic
    if ( methods[method] ) { 
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 )); 
    } else if ( typeof method === 'object' || ! method ) { 
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.facetedSearch' );
    }    
  };  

  function remove_empty (arr) { 
    var newArr = new Array();
    for(var k = 0; k < arr.length; k++) {
      if(arr[k]['value'] != "") {
        newArr.push(arr[k]);
      }   
    }   
    return newArr;
  }
})( jQuery );
