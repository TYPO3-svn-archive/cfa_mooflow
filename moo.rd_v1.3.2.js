/*
	Filename: moo.rd - A lightweight Mootools extension
	
	Author: Riccardo Degni, <http://www.riccardodegni.it/> and the moo.rd Team
	
	License: GNU GPL License
	
	Copyright: copyright 2007 Riccardo Degni
	
	[Credits]
		[li] moo.rd is based on the MooTools framework <http://mootools.net/>, and uses the MooTools syntax
		[li] moo.rd constructors extends some of the MooTools Classes
		[li] moo.rd Documentation is written by Riccardo Degni
	[/Credits]
*/

var Moo = {};

Moo.Rd = {
	version: '1.3.2',
	author: 'Riccardo Degni',
	members: [
		'Cristiano Fino',
		'Moocha'
	]
};

/*
	Filename: constructors.js
	
	[Description] 
		Contains some of the moo.rd native Constructors based on the MooTools Class. It permits a major modularity.
	[/Description]
	
	Contains: Class Table, Class Make
*/
/*
	Class: Table
	Description: Allows you to customize tables, tables rows, cells and columns 
*/
var Table = new Class({	
	initialize: function(element) {
		this.element = $(element);
		this.rows = this.element.getElements('tr');
		this.cells = this.element.getElements('tr').getElements('td');
	}
});

/*
	Class: Make
	Description: Wrapper to create Classes that make dinamically Elements.  
*/
var Make = new Class({
	Implements: [Options],
	options: {
		content: 'text'
	}
});

/*
	Filename: browser.js
	
	[Description]
		Contains some Browser properties to ease the detection of the browser which is working
	[/Description]
	
	Contains: Hash Browser
	
	[Summary]
		Browser ::: Extends the Browser Hash with new properties 
	[/Summary]
*/
/*
	Hash: Browser
	
	[Description]  
		Extends the Browser Hash with new properties, to speed up and ease the detection of the browser which is working.
	[/Description]
	
	[Hash]
		 Browser.ie : alias of Browser.Engine.trident
		 Browser.ie6 : alias of Browser.Engine.trident4
		 Browser.ie7 : alias of Browser.Engine.trident5
		 Browser.firefox : alias of Browser.Engine.gecko
		 Browser.safari : alias of Browser.Engine.webkit
		 Browser.safari2 : alias of Browser.Engine.webkit419
		 Browser.safari3 : alias of Browser.Engine.webkit420
		 Browser.opera : alias of Browser.Engine.presto
		 Browser.opera925 : alias of Browser.Engine.presto925
		 Browser.opera950 : alias of Browser.Engine.presto950
	[/Hash]
*/
Browser.ie = Browser.Engine.trident;
Browser.ie6 = Browser.Engine.trident4;
Browser.ie7 = Browser.Engine.trident5;
Browser.firefox = Browser.Engine.gecko;
Browser.safari = Browser.Engine.webkit;
Browser.safari2 = Browser.Engine.webkit419;
Browser.safari3 = Browser.Engine.webkit420;
Browser.opera = Browser.Engine.presto;
Browser.opera925 = Browser.Engine.presto925;
Browser.opera950 = Browser.Engine.presto950;

/*
	Filename: overlay.js
	
	[Description]
		Contains the Overlay Utility Class, that can be implemented with Implements property into any Class
	[/Description]
	
	Contains: Class Overlay
	
	Requires: browser.js
	
	[Summary]
		Overlay ::: Utility Class for creating customized and advanced overlays
	[/Summary]
*/
/*
	Class: Overlay
	
	[Description]  
		Utility Class for creating customized and advanced overlays. Adds the Overlay properties to the Classes
	[/Description]
	
	Constructor: new Overlay()
	
	[Methods]
		createOverlay -- creates the overlay property which represents the overlay
		createFullPage -- creates the full page overlay which represents the upper level
		setLight -- sets the light of the overlay. Can be 'draken', 'lighten', false or a color
		injectOverlay -- injects the overlay and indicates that the overlay is active
		removeOverlay -- removes the overlay with additional controls for IE 6
	[/Methods]
*/
var Overlay = new Class({
						
	/*
	Method: createOverlay
	Description:  creates the overlay property which represents the overlay
	[Arguments]
		id :: the overlay id
		light :: the overlay light. It can be 'darken' 'lighten', false or a color
	[/Arguments]
	*/
	createOverlay: function(id, light) {
		this.overlay = new Element('div', {
			'id': id || 'overlay',
			'styles': {
				'position': (Browser.ie6) ? 'absolute' : 'fixed',
				'top': '0px',
				'left': '0px',
				'width': (Browser.ie6) ? window.getWidth() : '100%',
				'height': '100%',
				'background-image':'url(g.gif)',
				'z-index': 800
			}
		});
		
		if(Browser.Engine.presto) this.overlay.setStyle('overflow', 'hidden');  
	
		this.setLight(light);
		
		//this.fade = new Fx.Morph(this.overlay, 'opacity').set(0);
		this.fade = new Fx.Morph(this.overlay, {duration: 'long', transition: Fx.Transitions.Sine.easeOut});
		
		
		return this;
	},
	
	/*
	Method: createFullPage
	Description:  creates the full page overlay which represents the upper level
	[Arguments]
		id :: the fullpage id
	[/Arguments]
	*/
	createFullPage: function(id) {
		this.fullpage = new Element('div', {
			'id': id || 'fullpage',
			'styles': {
				'position': (Browser.ie6) ? 'absolute' : 'fixed',
				'top': '0px',
				'left': '0px',
				'width': (Browser.ie6) ? window.getWidth() : '100%',
				'height': '100%',
				'background-image':'url(g.gif)',
				'z-index': 900
			}
		});
		
		if(Browser.Engine.presto) this.fullpage.setStyle('overflow', 'hidden');  
		
		return this;
	},
	
	/*
	Method: setLight
	Description:  sets the light of the overlay. Can be 'darken', 'lighten', false or a color
	[Arguments]
		light :: the light of the overlay 
		opacity :: optional. the opacity value of the overlay
	[/Arguments]
	*/
	setLight: function(light, opacity) {
		switch(light) {
			case 'darken': 
				var color = '#333333';
				break;
			case 'lighten':
				var color = '#FFFFFF';
				break;
			case false:
				var color = 'transparent';
				break;
			default: 
				var color = light;
				break;
		};
		this.overlay.setStyles({
			'background-color': color,
			'opacity': opacity || '0.0'			   
		});
		
		return this;
	},
	
	/*
	Method: injectOverlay
	Description:  injects the overlay and indicates that the overlay is active
	[Arguments]
		fullpage :: if true the fullpage will be injected too
	[/Arguments]
	*/
	injectOverlay: function(fullpage) {
		this.overlay.inject(document.body);
		if(fullpage) this.fullpage.inject(document.body);
		this.overlayActive = true;
		this.fixOverlay();
		this.fade.start({'opacity':[0,0.8], 'visibility':'visible'});
		return this;
	},
	
	/*
	Method: removeOverlay
	Description:  removes the overlay with additional controls for IE 6
	*/
	removeOverlay: function() {
	  this.fade.start({'opacity':[0,0.8], 'visibility':'hidden'});
		this.overlay.dispose();
		this.fullpage.dispose();
		if(Browser.ie6) {
			document.body.setStyles({'height': this.bodyHeightTrident4, 'overflow': this.bodyOverflowTrident4});
		}
		this.overlayActive = false;
		return this;
	},
	
	fixOverlay: function() {
		if(Browser.ie6) {
			if(!this.bodyHeightTrident4) this.bodyHeightTrident4 = $(document.body).getStyle('height');
			this.bodyOverflowTrident4 = $(document.body).getStyle('overflow');
			
			$$(this.overlay, this.fullpage).setStyle('overflow', 'hidden');
			
			$$(window, document.body).setStyles({
				'height': '100%',
				'overflow': 'auto',
				'margin': 0,
				'padding': 0
			});
		}
		return this;
	}
});