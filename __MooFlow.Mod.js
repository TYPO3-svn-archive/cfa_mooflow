/**
 * MooFlow - Image gallery
 *
 * Dependencies: MooTools 1.2
 *
 * @version			0.2.1
 *
 * @license			MIT-style license
 * @author			Tobias Wetzel <info [at] outcut.de>
 * @copyright		Author
 * @docmentation	http://outcut.de/MooFlow/Docmentation.html
 */ 

var MooFlow = new Class({

	Implements: [Events, Options],
	
	options: {
		onStart: $empty,
		onClickView: $empty,
		onAutoPlay: $empty,
		onAutoStop: $empty,
		onRequest: $empty,
		onResized: $empty,
		onEmptyinit: $empty,
		reflection: 0.4,
		heightRatio: 0.6,
		offsetY: 0,
		startIndex: 0,
		interval: 3000,
		factor: 115,
		bgColor: '#000',
		useCaption: false,
		useResize: false,
		useSlider: false,
		useWindowResize: false,
		useMouseWheel: true,
		useKeyInput: false,
		useViewer: false
	},
	
	initialize: function(element, options){
		this.MooFlow = element;
		this.setOptions(options);
		this.foc = 150;
		this.factor = this.options.factor;
		this.offY = this.options.offsetY;
		this.isFull = false;
		this.isAutoPlay = false;
		this.isLoading = false;
		this.inMotion = false;
		this.isHold = false;
		this.isStop = false;
		this.isPlay = false
		
		this.MooFlow.addClass('mf').setStyles({
			'overflow':'hidden',
			'background-color':this.options.bgColor,
			'position':'relative',
			'height':this.MooFlow.getSize().x * this.options.heightRatio,
			'opacity':0
		});
		//alert(this.MooFlow.getSize().x);

		if(this.options.useWindowResize) window.addEvent('resize', this.update.bind(this, 'init'));
		if(!this.options.useAutoPlay){
			if(this.options.useMouseWheel || this.options.useSlider) this.MooFlow.addEvent('mousewheel', this.wheelTo.bind(this));
			if(this.options.useKeyInput) document.addEvent('keydown', this.keyTo.bind(this));
		}
		
		this.getElements(this.MooFlow);
	},
	
	clearInit: function(){
		this.fireEvent('emptyinit');
	},
	
	getElements: function(el){
		this.master = {'images':[]};
		var els = el.getChildren();
		if(!els.length) {this.clearInit(); return;}
		$$(els).each(function(el){
			var hash = $H(el.getElement('img').getProperties('src','title','alt','longdesc'));
			if(el.get('tag') == 'a') hash.combine(el.getProperties('href','rel','target'));
			this.master['images'].push(hash.getClean());
			el.dispose();
		}, this);
		this.clearMain();
	},
	
	clearMain: function(){
		if(this.cap){this.cap.fade(0);}
		if(this.nav){
			new Fx.Tween(this.nav, {
				'onComplete': function(){
					this.MooFlow.empty();
					this.createAniObj();
				}.bind(this)
			}).start('bottom', -50);
		}
		if(!this.nav && !this.cap){
			this.MooFlow.empty();
			this.createAniObj();
		}
	},
	
	getMooFlowElements: function(key){
		var els = [];
		this.master.images.each(function(el){ 
			els.push(el[key]); 
		});
		return els;
	},
	
	createAniObj: function(){
		this.aniFx = new Fx.Value({
			'transition': Fx.Transitions.Expo.easeOut,
			'link': 'cancel',
			'duration': 750,
			onMotion: this.process.bind(this),
			'onStart': this.flowStart.bind(this),
			'onComplete': this.flowComplete.bind(this)
		});
		this.addLoader();
	},
	
	addLoader: function(){
		this.MooFlow.store('height', this.MooFlow.getSize().y);
		this.loader = new Element('div',{'class':'loader'}).inject(this.MooFlow);
		new Fx.Tween(this.MooFlow, {
			'duration': 800,
			'onComplete': this.preloadImg.bind(this)
		}).start('opacity', 1);
	},
	
	preloadImg: function(){
		this.loadedImages = new Asset.images(this.getMooFlowElements('src'), {
			'onComplete': this.loaded.bind(this),
			'onProgress': this.createMooFlowElement.bind(this)
		});
	},
	
	createMooFlowElement: function(counter, i){
		var obj = this.getCurrent(i);
		var img = this.loadedImages[i];
		obj['width'] = img.width;
		obj['height'] = img.height;
		img.removeProperties('width','height');

		obj['div'] = new Element('div').setStyles({
			'position':'absolute',
			'display':'none',
			'height': this.MooFlow.getSize().y
		}).inject(this.MooFlow);
		obj['con'] = new Element('div').inject(obj['div']);
		if(this.options.startIndex == i) {
		  img.setStyles({'vertical-align':'bottom', 'width':'100%', 'height':'50%', 'cursor':'pointer'});
		  img.addEvents({'click': this.viewCallBack.bind(this, i)});
		} else {
		  img.setStyles({'vertical-align':'bottom', 'width':'100%', 'height':'50%'});
		  img.addEvents({'click': this.xClickTo.bind(this, i)});
		}
		img.inject(obj['con']);
		
		new Element('div').reflect({ 'img': img,
			'ref': this.options.reflection,
			'height': obj.height,
			'width': obj.width,
			'color': this.options.bgColor
		}).setStyles({'width':'100%','height':'50%','background-color': this.options.bgColor}).inject(obj['con']);
		
		this.loader.set('text', (counter+1) + ' / ' + this.loadedImages.length);
	},
	
	loaded: function(){
		this.index = this.options.startIndex;
		this.iL = this.master.images.length-1;
		new Fx.Tween(this.loader, {
			'duration': 800,
			'onComplete': this.createUI.bind(this)
		}).start('opacity', 0);
	},
	
	createUI: function(){
		this.loader.dispose();
		if(this.options.useCaption){
			this.cap = new Element('div').addClass('caption').set('opacity',0).inject(this.MooFlow);
		}	
		this.nav = new Element('div').addClass('mfNav').setStyle('bottom','-50px');
		this.autoPlayCon = new Element('div').addClass('autoPlayCon');
		this.sliderCon = new Element('div').addClass('sliderCon');
		this.resizeCon = new Element('div').addClass('resizeCon');		
		if(this.options.useAutoPlay){
			this.autoPlayCon.adopt(
				new Element('a',{'class':'stop','events': {'click':this.stop.bind(this)}}), 
				new Element('a',{'class':'play','events': {'click':this.play.bind(this)}})
			);
		}
		if(this.options.useSlider){
			this.sliPrev = new Element('a',{'class':'sliderNext','events': {'click':this.prev.bind(this)}});
			this.sliNext = new Element('a',{'class':'sliderPrev','events': {'click':this.next.bind(this)}});
			this.knob = new Element('div',{'class':'knob'});
			this.knob.adopt(new Element('div',{'class':'knobleft'}));
			this.slider = new Element('div',{'class':'slider'}).adopt(this.knob);
			this.sliderCon.adopt(this.sliPrev,this.slider,this.sliNext);
			this.slider.store('parentWidth', this.sliderCon.getSize().x-this.sliPrev.getSize().x-this.sliNext.getSize().x);
		}
		if(this.options.useResize){
			this.resizeCon.adopt(new Element('a',{'class':'resize','events': {'click':this.setScreen.bind(this)}}));
		}		
		this.MooFlow.adopt(this.nav.adopt(this.autoPlayCon, this.sliderCon, this.resizeCon));	
		this.showUI();
	},
	
	showUI: function(){
		if(this.cap) this.cap.fade(1);
		this.nav.tween('bottom', 20);
		this.fireEvent('start');
		this.update();
	},
	
	update: function(e){
		if(e == 'init') return;
		this.oW = this.MooFlow.getSize().x;
		this.sz = this.oW * 0.5;
		if(this.options.useSlider){	
			this.slider.setStyle('width',this.slider.getParent().getSize().x-this.sliPrev.getSize().x-this.sliNext.getSize().x-1);
			this.knob.setStyle('width',(this.slider.getSize().x/this.iL));
			this.sli = new SliderEx(this.slider, this.knob, {steps: this.iL}).set(this.index);
			this.sli.addEvent('onChange', this.glideTo.bind(this));
		}
		this.glideTo(this.index);
		this.isLoading = false;
	},
	
	setScreen: function(){
		if(this.isFull = !this.isFull){
			this.holder = new Element('div').inject(this.MooFlow,'after');
			this.MooFlow.wraps(new Element('div').inject(document.body));
			this.MooFlow.setStyles({'position':'absolute','z-index':'100','top':'0','left':'0','width':window.getSize().x,'height':window.getSize().y});
			if(this.options.useWindowResize){
				this._initResize = this.initResize.bind(this);
				window.addEvent('resize', this._initResize);
			}
		} else {
			this.MooFlow.wraps(this.holder);
			window.removeEvent('resize', this._initResize);
			delete this.holder, this._initResize;
			this.MooFlow.setStyles({'position':'relative','z-index':'','top':'','left':'','width':'','height':this.MooFlow.retrieve('height')});
			if(this.options.useSlider){ 
				this.slider.setStyle('width',this.slider.retrieve('parentWidth'));
			}
		}
		this.fireEvent('resized', this.isFull);
		this.update();
	},
	
	initResize: function(){
		this.MooFlow.setStyles({'width':window.getSize().x,'height':window.getSize().y});
		this.update();
	},
	
	getCurrent: function(index){
		return this.master.images[$chk(index) ? index : this.index];
	},
	
	loadJSON: function(url){
		if(!url || this.isLoading) return;
		this.isLoading = true;
		new Request.JSON({
			'onComplete': function(data){
				if($chk(data)){
					this.master = data;
					this.clearMain();
					this.fireEvent('request', data);
				}
			}.bind(this)
		}, this).get(url);
	},
	
	loadHTML: function(url, filter){
		if(!url || !filter || this.isLoading) return;
		this.isLoading = true;
		new Request.HTML({
			'onSuccess': function(tree, els, htm){
				var result = new Element('div', {'html': htm}).getChildren(filter);
				this.getElements(result);
				this.fireEvent('request', result);
			}.bind(this)
		}, this).get(url);
	},
	
        getDetailView: function(obj){
         var req = new Request.HTML({url:obj.href,
            onSuccess: function(html) {
              var destEl = new Element('div', {
        	     'class': 'detailViewElement',
        	     'text': html,
        	     'styles': {
        		      'font-weight': 'bold',
        		      'margin': '1em'
        	     }
              }); 
            },
         
            onFailure: function() {
              var destEl = new Element('div', {
        	     'class': 'detailViewElement',
        	     'text': 'The request failed.',
        	     'styles': {
        		      'font-weight': 'bold',
        		      'margin': '1em'
        	     }
              }); 
            }
         });
         $('MooFlow').inject(destEl); 		
        },
        	
	flowStart: function(){
		this.inMotion = true;
	},
	
	flowComplete: function(){
		//var tempEl;
		this.inMotion = false;
		/*
		for(var i = 0; i < this.index; i++){
				tempEl = this.master.images.shift();
				this.master.images.push(tempEl);
		}
		*/
	},
	
	viewCallBack: function(index){
		if(this.index != index || this.inMotion) return;
		var el = $H(this.getCurrent());
		var returnObj = {};
		returnObj['coords'] = el.div.getElement('img').getCoordinates();
		el.each(function(v, k){
			if($type(v) == 'number' || $type(v) == 'string') returnObj[k] = v;
		}, this);
		this.fireEvent('clickView', returnObj);
	},
	prev: function(){
		if(this.index > 0) this.clickTo(this.index-1);
		//console.log('prev');
	},
	next: function(){
		if(this.index < this.iL) this.clickTo(this.index+1);
		//console.log('next');
	},
	stop: function(){
		this.autoPlay = $clear(this.autoPlay);
		this.isAutoPlay = false;
		this.fireEvent('autoStop');
		//console.log('stop');
	},
	play: function(){
		this.autoPlay = this.auto.periodical(this.options.interval, this);
		this.isAutoPlay = true;
		this.fireEvent('autoPlay');
		//console.log('play');
	},
	auto: function(){
	  //console.log('auto');
		if(this.index < this.iL) this.next();
		else if(this.index == this.iL) this.clickTo(0);
	},
	keyTo: function(e){
		switch (e.code){
			case 37: e.stop(); this.prev();	break;
			case 39: e.stop(); this.next();
		}
	},
	wheelTo: function(e){
		if(e.wheel > 0) this.prev();
		if(e.wheel < 0) this.next();
		e.stop().preventDefault();
	},
	clickTo: function(index){
	   //console.log('clickTo');
		if(this.index == index) return;
		prevIndex = this.index;
		//this.aniFx.cancel();
		if(this.sli) this.sli.set(index);
		this.glideTo(index);
	},
	xClickTo: function(index){
	  if(this.options.useAutoPlay){
			this.stop();
			this.isPlay = true;
		}
		//console.log('xClickTo');
    if(this.index == index) return;
		prevIndex = this.index;
		if(this.sli) this.sli.set(index);
		this.glideTo(index);
	},
	glideTo: function(index){
	  var oMF = this;
	  prevIndex = this.index;
		this.index = index;
		this.aniFx.start(this.aniFx.get(), index*-this.foc);
		if(this.cap) this.cap.set('html', this.getCurrent().title);
		if(index != prevIndex) {
			this.loadedImages[index].setStyles({'cursor':'pointer'});
			this.loadedImages[prevIndex].setStyles({'cursor':'default'});
			this.loadedImages[index].removeEvents();
			this.loadedImages[prevIndex].removeEvents();

			if(this.options.useAutoPlay){
				this.loadedImages[index].addEvents({
  					'click': this.viewCallBack.bind(this, index),
  					'mouseover': function(){
  					        oMF.stop();
  					        oMF.isPlay = false;
    					},
    					'mouseout': function(){
    						if(!oMF.isHold) {
    							if(oMF.isPlay) {
    								return;
    							} else {
        							oMF.play();
        							//console.log('mouseout');
        							oMF.isPlay = true;
        						}
        					}
    					}
  				});
  				this.loadedImages[prevIndex].addEvents({'click': this.xClickTo.bind(this, prevIndex)});
  			} else {
				this.loadedImages[index].addEvents({'click': this.viewCallBack.bind(this, index)});
				this.loadedImages[prevIndex].addEvents({'click': this.clickTo.bind(this, prevIndex)});
  			}
			
		}
	},
	process: function(x){
		var z,W,H,zI=this.iL,foc=this.foc,f=this.factor,sz=this.sz,oW=this.oW,offY=this.offY,div,elh,elw,oH=this.MooFlow.getSize().y;
		this.master.images.each(function(el){
			div = el.div.style;
			elw = el.width;
			elh = el.height;
			if(x>-foc*6 && x<foc*6){
				with (Math) {
					z = sqrt(10000 + x * x) + 100;
					H = round((elh / elw * f) / z * sz);
					W = round(elw * H / elh);
					if(H >= elw * 0.5) {W = round(f / z * sz);}
					div.left = round(((x / z * sz) + sz) - (f * 0.5) / z * sz) + 'px';
					div.top = round((oH * 0.5) - (H * 0.5)) + offY + 'px';
				}	
				el.con.style.height = H*2 + 'px';		
				div.width = W + 'px';
				div.zIndex = x < 0 ? zI++ : zI--;
				div.display = 'block';
			} else {
				div.display = 'none';
			}
			x += foc;
		});
	}
});

var SliderEx = new Class({
	Extends: Slider,
	set: function(step){
		this.step = Math.round(step);
		this.fireEvent('tick', this.toPosition(this.step));
		return this;
    },
	clickedElement: function(event){
		var dir = this.range < 0 ? -1 : 1;
		var position = event.page[this.axis] - this.element.getPosition()[this.axis] - this.half;
		position = position.limit(-this.options.offset, this.full -this.options.offset);
		this.step = Math.round(this.min + dir * this.toStep(position));
		this.checkStep();
		this.fireEvent('tick', position);
	}
});

Fx.Value = new Class({
	Extends: Fx,
	compute: function(from, to, delta){
		this.value = Fx.compute(from, to, delta);
		this.fireEvent('motion', this.value);
		return this.value;
	},
	get: function(){
		return this.value || 0;
	}
});

Element.implement({
	reflect: function(arg){
		i = arg.img.clone();
		if(Browser.Engine.trident){
			i.style.filter = 'flipv progid:DXImageTransform.Microsoft.Alpha(opacity=20, style=1, finishOpacity=0, startx=0, starty=0, finishx=0, finishy='+100*arg.ref+')';
			i.setStyles({'width':'100%', 'height':'100%'});
			return new Element('div').adopt(i);
		} else {
			var can = new Element('canvas').setProperties({'width':arg.width, 'height':arg.height});
			if(can.getContext){
				var ctx = can.getContext("2d");
				ctx.save();
				ctx.translate(0,arg.height-1);
				ctx.scale(1,-1);
				ctx.drawImage(i, 0, 0, arg.width, arg.height);
				ctx.restore();
				ctx.globalCompositeOperation = "destination-out";
				ctx.fillStyle = arg.color;
				ctx.fillRect(0, arg.height*0.5, arg.width, arg.height);
				var gra = ctx.createLinearGradient(0, 0, 0, arg.height*arg.ref);					
				gra.addColorStop(1, "rgba(255, 255, 255, 1.0)");
				gra.addColorStop(0, "rgba(255, 255, 255, "+(1-arg.ref)+")");
				ctx.fillStyle = gra;
				ctx.rect(0, 0, arg.width, arg.height);
				ctx.fill();
				delete ctx, gra;
			}
			return can;
		}
	}
});

window.addEvent('domready', function(){
	$$('.MooFlowieze').each(function(mooflow){
		new MooFlow(mooflow);
	});
}); 