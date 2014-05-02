(function ($, document ){

	$.fn.wysiwyg = function( options ) {
	
		var settings = $.extend({
			resize: true
		}, options );
			
    	return this.each(function() {
    		
    		// generate new wysiwyg editing window
    		var editor  = document.createElement('iframe');
    		var resizer = document.createElement('div');
    		
    		// set up resizing
    		$(resizer).css("padding-right","2px");
    		$(editor).css({height:"100%",width:"100%"});
    		
    		// save this for use later 
    		var cur = $(this);
    		
    		$.data( this, "wysiwyg_editor", editor);
    		$.data( editor, "text_area", this );
    		
    		
    		// match the height and width of the text area
    		$( resizer ).height( cur.outerHeight() )
    					.width( cur.outerWidth() )
    					.resizable({
    							handles: "n, s",
    							helper: "ui-resizable-helper"
    						});
    		
    		// hide old text area
    		cur.css({height:"100%",width:"100%"}).hide();
    		
    		// add wysiwyg items
    		var control_box = document.createElement('div');
    		var toolbar_box = document.createElement('div');
    		var resize_bar  = document.createElement('div');
    		
    		// add wysiwyg editor to page
    		cur.after( control_box );
    		$(resizer).append(cur);
    		
    		// add toolbar and edit window
    		// and move the text area into the control box
    		$( control_box )	.addClass("wysiwyg_container")
    							.append( toolbar_box )
    							.append( resizer )
    							.append( resize_bar );
    		
    		// add toolbar buttons
    		var btn_bold		= document.createElement('div');
    		var btn_italic		= document.createElement('div');
    		var btn_underline	= document.createElement('div');
    		var btn_strike		= document.createElement('div');
    		var btn_foreground	= document.createElement('div');
    		var btn_ol			= document.createElement('div');
    		var btn_ul			= document.createElement('div');
    		var btn_center		= document.createElement('div');
    		var btn_left		= document.createElement('div');
    		var btn_right		= document.createElement('div');
    		var btn_justify		= document.createElement('div');
    		var btn_h1			= document.createElement('div');
    		var btn_h2			= document.createElement('div');
    		var btn_h3			= document.createElement('div');
    		var btn_para		= document.createElement('div');
    		var btn_source		= document.createElement('div');
    		
    		$( toolbar_box )	.addClass("wysiwyg_toolbar")
    							.append( btn_bold )
    							.append( btn_italic )
    							.append( btn_underline )
    							.append( btn_strike )
    							.append( btn_foreground )
    							.append( btn_ol )
    							.append( btn_ul )
    							.append( btn_left )
    							.append( btn_center )
    							.append( btn_right )
    							.append( btn_justify )
    							.append( btn_h1 )
    							.append( btn_h2 )
    							.append( btn_h3 )
    							.append( btn_para )
    							.append( btn_source );
    							
    		$(btn_bold).addClass("wysiwyg_bold");
    		$(btn_italic).addClass("wysiwyg_italic");
    		$(btn_underline).addClass("wysiwyg_underline");
    		$(btn_strike).addClass("wysiwyg_strike");
    		$(btn_foreground).addClass("wysiwyg_foreground");
    		$(btn_ol).addClass("wysiwyg_ol");
    		$(btn_ul).addClass("wysiwyg_ul");
    		$(btn_center).addClass("wysiwyg_center");
    		$(btn_left).addClass("wysiwyg_left");
    		$(btn_right).addClass("wysiwyg_right");
    		$(btn_justify).addClass("wysiwyg_justify");
    		$(btn_h1).addClass("wysiwyg_h1");
    		$(btn_h2).addClass("wysiwyg_h2");
    		$(btn_h3).addClass("wysiwyg_h3");
    		$(btn_para).addClass("wysiwyg_p");
    		$(btn_source).addClass("wysiwyg_source");
    		
    		
    		//
    		$( resize_bar )	.addClass("wysiwyg_resize");
    			    	
    		// when iframe loaded turn on designMode
    		$(editor).load(function(){
    			// get document for turning on content editable
    			var doc = 	editor.contentDocument ||  
    						editor.contentWindow ||
    						editor.document;
    			if( doc.document ){ doc = doc.document }
    			
    			doc.designMode = "on";
    			
    			$(doc.body).html( cur.val() );
    			
    			
    			// functions to update text box on actions
    			$( doc.body ).keyup(function(){
    				cur.val( $(doc.body).html() );
    			});
    			$( toolbar_box ).click(function(){
    				cur.val( $(doc.body).html() );
    			});
    			$( doc ).mouseout( function(){
    				cur.val( $(doc.body).html() );
    			});
    			$('input[type="submit"]').click(function(){
    				if( cur.is(':hidden') ){
    					cur.val( $(doc.body).html() );
    				}
				});
    			
    			// add events to the buttons
    			$(btn_bold).click(function(){
    				doc.execCommand("bold",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			$(btn_italic).click(function(){
    				doc.execCommand("italic",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			$(btn_underline).click(function(){
    				doc.execCommand("underline",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			$(btn_strike).click(function(){
    				doc.execCommand("strikeThrough",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			
				// create a color picker for choosing foreground
				// this is using the colpick jquery plugin which
				// is distributed under GNU GPL 2
				// http://colpick.com/plugin
				if( $.fn.colpick ){
					$(btn_foreground).colpick({
						layout: 'hex',
						onSubmit: function(hsb, hex, rgb, el){
							doc.execCommand('foreColor',false,"#"+hex);
							$(el).css("border-color","#"+hex)
							$(el).colpickHide();
    						cur.val( $(doc.body).html() );
    						editor.contentWindow.focus();
						}
					});
				}
				
				// back to regular buttons
				
				// add ordered and unordered lists
    			$(btn_ol).click(function(){
    				doc.execCommand("insertOrderedList",false,null);
    				editor.contentWindow.focus();
    			});
				
    			$(btn_ul).click(function(){
    				doc.execCommand("insertUnorderedList",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			// positioning buttons
    			$(btn_center).click(function(){
    				doc.execCommand("justifyCenter",false,null);
    				editor.contentWindow.focus();
    			});
    			$(btn_left).click(function(){
    				doc.execCommand("justifyLeft",false,null);
    				editor.contentWindow.focus();
    			});
    			$(btn_right).click(function(){
    				doc.execCommand("justifyRight",false,null);
    				editor.contentWindow.focus();
    			});
    			$(btn_justify).click(function(){
    				doc.execCommand("justifyFull",false,null);
    				editor.contentWindow.focus();
    			});
    			
    			// header styles
    			$(btn_h1).click(function(){
    				doc.execCommand("formatBlock",false,"<h1>");
    				editor.contentWindow.focus();
    			});
    			$(btn_h2).click(function(){
    				doc.execCommand("formatBlock",false,"<h2>");
    				editor.contentWindow.focus();
    			});
    			$(btn_h3).click(function(){
    				doc.execCommand("formatBlock",false,"<h3>");
    				editor.contentWindow.focus();
    			});
    			$(btn_para).click(function(){
    				doc.execCommand("formatBlock",false,"<p>");
    				editor.contentWindow.focus();
    			});
    			
    			// view source button
    			$.data(editor,"view_source",false);
    			$(btn_source).click(function(){
    				if( $.data(editor,"view_source") == true){
    					// go back to edit mode
    					$.data(editor,"view_source",false);
    					
    					// update editor information
    					$(doc.body).html(cur.val());
    					
    					// show editor and hide source
    					$(editor).show();
    					cur.hide();
    					editor.contentWindow.focus();
    				}else{
    					// go to sourc mode
    					$.data(editor,"view_source",true);
    					    					
    					// show source and hide	 editor
    					$(editor).hide();
    					cur.show();
    				}
    			});
    			
    		});
    		
    		// finally append, do it late for chrome.
    		$(resizer).append(editor);
    		
    	});
	};

	

}( jQuery, document ));
