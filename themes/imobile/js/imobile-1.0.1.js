/******************************************************************************
 *	Thumbnail Manager based on JAIPHO code 
 *	(c) 2011 jaipho.com, calleh
 *
 *	JAIPHO is freely used under the terms of an LGPL license.
 *	For details, see the JAIPHO web site: http://www.jaipho.com/
 ******************************************************************************/

// -------------- PRELOADER 
function JphUtil_PreloaderItem( imageElement, src)
 {
	this.mhImage	=	imageElement;
	this.mSrc		=	src;
 }
 
 JphUtil_PreloaderItem.prototype.LoadImage	=	function()
 {
 	this.mhImage.src	=	this.mSrc;
 } 
 
 JphUtil_PreloaderItem.prototype.toString	=	function()
 {
 	return 'JphUtil_PreloaderItem ['+this.mSrc+']['+this.mhImage.getAttribute("src")+']';
 }
				
function JphUtil_Preloader( maxActiveCount)
 {
 	this.mMaxActiveCount	=	maxActiveCount;
 	
 	this.mActiveCount		=	0;
 	this.mPaused			=	false;
	this.maAllImages		=	[];
	this.maQueue			=	new Array();
 }
 
  JphUtil_Preloader.prototype.Pause		=	function()
 {
	this.mPaused			=	true;
 }


 JphUtil_Preloader.prototype.Load	=	function( img, src)
 {
 	
 	if (this.maAllImages[src] != undefined)
 	{
 		img.src	=	src;
 		this._ImageLoaded( src);
 		return;
 	}
 	
	var item				=	new JphUtil_PreloaderItem( img, src);
	this.maAllImages[src]	=	item;
	
	if (this.mActiveCount < this.mMaxActiveCount)
	{
		this._LoadItem( item);
	}
	else
	{
		this.maQueue[this.maQueue.length]	=	item;
	}
 }
   
  
 JphUtil_Preloader.prototype._LoadItem	=	function( item)
 {
 	
 	this.mActiveCount++;
	attach_method( item.mhImage, 'onload', this, '_ImageLoaded');
	attach_method( item.mhImage, 'onerror', this, '_ImageError');
	item.LoadImage();
 }
 
 JphUtil_Preloader.prototype._ImageError	=	function( e)
 {
 	if (!e) 
 		var e = window.event;
 	var target	=	e.target ? e.target : e.srcElement;
 	this.mActiveCount--;
 	delete this.maAllImages[target.src];
 	if (!this.mPaused)
 		this._LoadNext();
 }
 
 JphUtil_Preloader.prototype._ImageLoaded	=	function( e)
 {
 	if (typeof(e) == 'string')
 	{
 		var src	=	e;
 	}
 	else
 	{
	 	if (!e) 
	 		var e = window.event;
	 	var target	=	e.target ? e.target : e.srcElement;
	 	var src		=	target.src;
 	}
	this.mActiveCount--;
	if (!this.mPaused)
		this._LoadNext();
 }
 
 JphUtil_Preloader.prototype._LoadNext	=	function()
 {

	if (this.maQueue.length)
	{
		this._LoadItem( this.maQueue.shift());
	}
 }
 
 JphUtil_Preloader.prototype.toString	=	function()
 {
 	return '[JphUtil_Preloader [queue length='+this.maQueue.length+'][active count='+this.mActiveCount+']]';
 }

 function attach_method( master, eventName, obj, method)
{
	if (master == null || master == undefined)
		throw new Error('Empty master object passed ['+eventName+']['+obj+']['+method+']');
		
	master[eventName] = 					
		function( event) 						
		{										
			obj[method]( event);				
		};										
} 
 
// -------------- MANAGER 
function JphThumbs_Manager( app)
 {
 	this.mrApp				=	app;
	
 	this.mhThumbnails		=	null;
 	this.mhThumbsTopBar		=	null;
 	this.mhThumbsContainer	=	null;
 	this.mhThumbsCount		=	null;
	this.mvThumbsIndex		=	null;
	this.mvThumbsIndexOld	= 	0;
	
	this.mInitialized		=	false;
	this.mrPreloader		=	null;
	
	this.maThumbnails		=	new Array();
 }
 
 JphThumbs_Manager.prototype.Create		=	function()
 {
	this.mhThumbsTopBar		=	document.getElementById('thumbs-toolbar-top');
	this.mhThumbnails		=	document.getElementById('thumbs-images-container');
	this.mhThumbsContainer	=	document.getElementById('thumbs-container');
	this.mhThumbsCount		=	document.getElementById('thumbs-count-text');
	this.mhThumbsLoadMore	=	document.getElementById('thumbs-load-more');	
	
	if (this.mrApp.mrDao.maImages.length > (MAX_LOADING_THUMBNAILS)) {
		this.mvThumbsIndex = MAX_LOADING_THUMBNAILS;
	} else {
		this.mvThumbsIndex = this.mrApp.mrDao.maImages.length;
	}
	
	for (var i=this.mvThumbsIndexOld;i<this.mvThumbsIndex;i++)
	{
		this.maThumbnails[this.maThumbnails.length]	=	
				new JphThumbs_Item( this.mrApp, this.mrApp.mrDao.maImages[i]);
	} 	
	
	this.mrPreloader =	new JphUtil_Preloader( MAX_CONCURRENT_LOADING_THUMBNAILS);
 }
 
 JphThumbs_Manager.prototype.Init			=	function()
 {
	var c = document.createElement('div');
	c.innerHTML = this._HtmlThumbs();
	while (c.firstChild) {
		this.mhThumbnails.appendChild(c.firstChild);
	}
	this.mhThumbsCount.innerHTML	=	this._HtmlCount();
	if (this.mrApp.mrDao.maImages.length == this.mvThumbsIndex)
	{
		this.HideLoadMore();
	}
	
	for (var i=this.mvThumbsIndexOld;i<this.maThumbnails.length;i++)
	{
		this.maThumbnails[i].Init();

	}
	
	this.mInitialized	=	true;
 }

 JphThumbs_Manager.prototype._HtmlThumbs	=	function()
 {
 	var str	=	new Array();
	var cnt	=	0;
	
	for (var i=this.mvThumbsIndexOld;i<this.maThumbnails.length;i++)
		str[cnt++]	=	this.maThumbnails[i].Html();
	
	return str.join('');
 }
 
 JphThumbs_Manager.prototype._HtmlCount		=	function()
 {
	var count	=	this.mrApp.mrDao.maImages.length;
	if (this.mrApp.mrDao.maImages.length == this.mvThumbsIndex)
	{
		var text	=	count + ' items';
	} 
	else
	{
		var text	=	this.mvThumbsIndex + ' of ' + count + ' items';
	}
	if (count == 1)
		text	=	count + ' item';
			
	return text;
 }

 JphThumbs_Manager.prototype.Show			=	function()
 {
	if (!this.mInitialized)
		this.Init();
		
	this.mhThumbnails.style.display			=	'block';
	this.mhThumbsContainer.style.display	=	'block';
	
	document.body.className	=	'thumbs';
 }
 
 JphThumbs_Manager.prototype.HideLoadMore	=	function()
 {
 	this.mhThumbsLoadMore.style.display	=	'none';
 }
 
 JphThumbs_Manager.prototype.LoadMore		=	function()
 {
	this.mvThumbsIndexOld = this.mvThumbsIndex;
	
	if (this.mrApp.mrDao.maImages.length > (this.mvThumbsIndex + MAX_LOADING_THUMBNAILS)) {
		this.mvThumbsIndex = this.mvThumbsIndex + MAX_LOADING_THUMBNAILS;
	} else {
		this.mvThumbsIndex = this.mrApp.mrDao.maImages.length;
	}
	
	for (var i=this.mvThumbsIndexOld;i<this.mvThumbsIndex;i++)
	{
		this.maThumbnails[this.maThumbnails.length]	=	
				new JphThumbs_Item( this.mrApp, this.mrApp.mrDao.maImages[i]);
	} 	
	
	this.Init();
 }
 
// -------------- ITEM
function JphThumbs_Item( app, image)
 {
 	this.mrApp		=	app;
 	this.mrImage	=	image;
	this.mhDiv		=	null;
	this.mhImage	=	null;
 }
 
 JphThumbs_Item.prototype.Init		=	function()
 {
 	this.mhDiv		=	document.getElementById( this.GetHtmlId('thumb_div'));
 	this.mhImage	=	document.getElementById( this.GetHtmlId('thumb_img'));
	this.mrApp.mrThumbnails.mrPreloader.Load( this.mhImage, this.mrImage.mSrcThumb);
 }

 JphThumbs_Item.prototype.Html				=	function()
 {
 	var str		=	new Array();
	var cnt		=	0;
	
	str[cnt++]	=	'<div class="thumbnail"';
	str[cnt++]	=	get_html_attribute("id", this.GetHtmlId('thumb_div'));
	str[cnt++]	=	'>';
	if (this.mrImage.mType == 'album')
	{
		str[cnt++]      =       '<a href="';
		str[cnt++]		=		this.mrImage.mSrc;
		str[cnt++]      =       '">';
	}
	str[cnt++]	=	'<img';
	str[cnt++]	=	get_html_attribute("id", this.GetHtmlId('thumb_img'));
	str[cnt++]	=	get_html_attribute('title', this.mrImage.mTitle);
	str[cnt++]	=	get_html_attribute('src', BASE_URL + 'dummy.gif');
	str[cnt++]  =   get_html_attribute('style', this.mrImage.mThumbStyle);
	if (this.mrImage.mType != 'album')
	{
		str[cnt++]	=	get_html_attribute('onclick', 'slidemgr.show(' + this.mrImage.mIndex + ');');
	}
	str[cnt++]	=	'/>';
	if (this.mrImage.mType == 'album')
	{
		str[cnt++]      =       '<div class="thumb-title">';
		str[cnt++]      =       this.mrImage.mTitle;
		str[cnt++]      =       '</div>';
		str[cnt++]      =       '</a>';
	}
	str[cnt++]      =       '</div>';
	
	
	return str.join('');
 }
 
 JphThumbs_Item.prototype.GetHtmlId = function( key)
 {
 	return this.mrImage.mIndex + '_' + key;
 }

 function get_html_attribute( name, value)
 {
	var str			=	new Array();
	str[str.length]	=	' ';
	str[str.length]	=	name;
	str[str.length]	=	'="';
	str[str.length]	=	value;
	str[str.length]	=	'"';
	return str.join('');
 }
 
// -------------- DAO
function Jph_Image( index, src, thumbSrc, title, type, thumbstyle)
 {
 	if (title==undefined)
		title =	'';
	if (type==undefined)
		type = 'image';
			
 	this.mIndex		=	index;
 	this.mSrcThumb	=	thumbSrc;
	this.mSrc		=	src;
	this.mTitle		=	title;	
	this.mType		=	type;
	this.mThumbStyle	=	thumbstyle;
	
 }
 
 function Jph_Dao()
 {
 	this.maImages =	new Array();
 }
 
 Jph_Dao.prototype.ReadImage	=	function( id, src, thumbSrc, title, type, thumbstyle)
{
	var obj			=	new Jph_Image();
	obj.mIndex		=	id;
 	obj.mSrcThumb	=	thumbSrc;
	obj.mSrc		=	src;
	obj.mTitle		=	title;		
	obj.mType		=	type;
	obj.mThumbStyle	=	thumbstyle;

	this.maImages[obj.mIndex]	=	obj;
}		
 
// -------------- APPLICATION
 function Jph_Application( dao)
 {
	this.mrDao				=	dao;	
	this.mrThumbnails		=	null;
 }

 Jph_Application.prototype.Init		=	function()
 {
	this.mrThumbnails	=	new JphThumbs_Manager( this);
	this.mrThumbnails.Create();
	this.mrThumbnails.Show();

 }

 Jph_Application.prototype.LoadMoreThumbs		=	function()
 {
	this.mrThumbnails.LoadMore();
	this.ScrollDown('thumbs-count-text');	
 }
 
 Jph_Application.prototype.NormalizeVertical = function()
 {
	setTimeout('scrollTo(0,1)',100);
 }

 Jph_Application.prototype.currentYPosition = function()
 {
	if (self.pageYOffset)
		 return self.pageYOffset;
	if (document.documentElement && document.documentElement.scrollTop)
		return document.documentElement.scrollTop;
	if (document.body.scrollTop)
		 return document.body.scrollTop;
	return 0;
 }

 Jph_Application.prototype.elmYPosition = function(eID)
 {
	var elm  = document.getElementById(eID);
	var y    = elm.offsetTop;
	var node = elm;
	while (node.offsetParent && node.offsetParent != document.body) {
		node = node.offsetParent;
		y   += node.offsetTop;
	} return y;

 }

 Jph_Application.prototype.ScrollDown = function(eID)
 {
	//setTimeout('scrollTo(0,document.body.scrollHeight)',500);
	var startY   = this.currentYPosition();
	var stopY    = this.elmYPosition(eID);
	var distance = stopY > startY ? stopY - startY : startY - stopY;
	if (distance < 100) {
		scrollTo(0, stopY); return;
	}
	var speed = Math.round(distance / 100);
	if (speed >= 20) speed = 20;
	var step  = Math.round(distance / 25);
	var leapY = stopY > startY ? startY + step : startY - step;
	var timer = 0;
	if (stopY > startY) {
		for ( var i=startY; i<stopY; i+=step ) {
			setTimeout("window.scrollTo(0, "+leapY+")", timer * speed);
			leapY += step; if (leapY > stopY) leapY = stopY; timer++;
		} return;
	}
	for ( var i=startY; i>stopY; i-=step ) {
		setTimeout("window.scrollTo(0, "+leapY+")", timer * speed);
		leapY -= step; if (leapY < stopY) leapY = stopY; timer++;
	}
 }
